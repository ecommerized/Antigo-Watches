<?php

namespace App\Traits;

use App\CentralLogics\Helpers;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use function App\CentralLogics\translate;

trait OrderPricing
{
    protected function calculateOrderAmount(array $cart, ?string $coupon_code = null, ?int $customer_id = null): array
    {
        if (empty($cart)) {
            return [
                'order_amount' => 0,
                'total_tax' => 0,
                'cart_total' => 0,
                'coupon_discount' => 0,
            ];
        }

        $productIds = collect($cart)->pluck('product_id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $totalTaxAmount = 0;
        $cartTotalProductPrice = 0;
        $cartTotalProductDiscountPrice = 0;

        foreach ($cart as $c) {
            $product = $products->get($c['product_id']);
            if (!$product) continue;

            $variations = json_decode($product->variations, true);
            $price = !empty($variations)
                ? Helpers::variation_price($product, json_encode($c['variation']))
                : $product->price;

            $singleProductDiscount = Helpers::discount_calculate($product, $price);
            $productSubtotal = ($price - $singleProductDiscount) * $c['quantity'];
            $discountOnProduct = $singleProductDiscount * $c['quantity'];
            $singleProductTax = Helpers::tax_calculate($product, $productSubtotal);

            $totalTaxAmount += $singleProductTax;
            $cartTotalProductPrice += ($productSubtotal + $discountOnProduct);
            $cartTotalProductDiscountPrice += $discountOnProduct;
        }

        $cartTotalAfterProductDiscount = $cartTotalProductPrice - $cartTotalProductDiscountPrice;
        $couponDiscountAmount = $this->calculateCouponDiscount($coupon_code, $customer_id, $cartTotalAfterProductDiscount);

        $order_amount = ($cartTotalAfterProductDiscount + $totalTaxAmount) - $couponDiscountAmount;

        return [
            'order_amount' => $order_amount,
            'total_tax' => $totalTaxAmount,
            'cart_total' => $cartTotalAfterProductDiscount,
            'coupon_discount' => $couponDiscountAmount,
        ];
    }

    /**
     * Calculate the coupon discount.
     */
    protected function calculateCouponDiscount(?string $coupon_code, ?int $customer_id, float $cartTotal): float
    {
        if (!$coupon_code || !$customer_id) {
            return 0;
        }

        $coupons = $this->couponList($customer_id);
        $coupon = $coupons->firstWhere('code', $coupon_code);

        if (!$coupon) {
            return 0;
        }

        if ($coupon->discount_type === 'first_order' && Order::where('user_id', $customer_id)->count() > 0) {
            throw ValidationException::withMessages([
                'coupon_code' => [translate('this_coupon_is_only_for_first_order')]
            ]);
        }

        if ($coupon->min_purchase > $cartTotal) {
            throw ValidationException::withMessages([
                'coupon_code' => [translate('minimum_purchase_amount_for_this_coupon_is') . ' ' . Helpers::set_symbol($coupon->min_purchase)]
            ]);
        }


        if ($coupon->discount_type == 'amount') {
            $couponDiscountAmount = $coupon->discount;
            if ($couponDiscountAmount > $cartTotal) {
                $couponDiscountAmount = $cartTotal;
            }
        }

        if ($coupon->discount_type == 'percent') {
            $couponDiscountAmount = (($cartTotal * $coupon->discount) / 100);
            if ($couponDiscountAmount > $coupon->max_discount) {
                $couponDiscountAmount = $coupon->max_discount;
            }
        }

        return $couponDiscountAmount;
    }

    protected function couponList($customerId): Collection
    {
        if (is_null($customerId) || $customerId == 0) {
            return collect();
        }

        $totalOrders = Order::where('user_id', $customerId)->count();

        return Coupon::withCount(['orders as used_count' => function ($query) use ($customerId) {
            $query->where('user_id', $customerId);
        }])
            ->active()
            ->get()
            ->filter(function ($item) use ($totalOrders) {
                if ($item->coupon_type == 'first_order') {
                    return $totalOrders == 0 && $item->used_count < 1;
                }
                return $item->used_count < $item->limit || $item->limit == null;
            })
            ->values();
    }

    protected function calculateOrderAmountForEdit(array $cart, ?string $coupon_code = null, ?int $customer_id = null): array
    {
        $productIds = collect($cart)->pluck('id')->toArray();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $totalTaxAmount = 0;
        $cartTotalProductPrice = 0;
        $cartTotalProductDiscountPrice = 0;

        foreach ($cart as $c) {
            $product = $products->get($c['id']);
            if (!$product) continue;

            if (count(json_decode($product['variations'], true)) > 0) {
                $variation = collect(json_decode($product['variations'], true))
                    ->where('type', $c['variant'])
                    ->values()
                    ->all();
                $price = Helpers::variation_price($product, json_encode($variation));
            } else {
                $price = $product['price'];
            }

            $singleProductDiscount = Helpers::discount_calculate($product, $price);
            $productSubtotal = ($price - $singleProductDiscount) * $c['quantity'];
            $discountOnProduct = $singleProductDiscount * $c['quantity'];
            $singleProductTax = Helpers::tax_calculate($product, $productSubtotal);

            $totalTaxAmount += $singleProductTax;
            $cartTotalProductPrice += ($productSubtotal + $discountOnProduct);
            $cartTotalProductDiscountPrice += $discountOnProduct;
        }

        $cartTotalAfterProductDiscount = $cartTotalProductPrice - $cartTotalProductDiscountPrice;
        $couponDiscountAmount = $this->calculateCouponDiscount($coupon_code, $customer_id, $cartTotalAfterProductDiscount);

        $order_amount = ($cartTotalAfterProductDiscount + $totalTaxAmount) - $couponDiscountAmount;

        return [
            'order_amount' => $order_amount,
            'total_tax' => $totalTaxAmount,
            'cart_total' => $cartTotalAfterProductDiscount,
            'coupon_discount' => $couponDiscountAmount,
        ];
    }

    protected function calculatePOSCouponAndExtraDiscount(): void
    {
        $cart = session()->get('cart', collect([]));
        $cartSubTotalAfterDiscount = 0;

        if ($cart->isNotEmpty()) {
            $cartItems = $cart->filter(fn($value, $key) => is_array($value))->values();
            $discount_on_product = 0;
            $subtotal = 0;
            foreach ($cartItems as $cartItem) {
                $product_subtotal = ($cartItem['price']) * $cartItem['quantity'];
                $discount_on_product += ($cartItem['discount'] * $cartItem['quantity']);
                $subtotal += $product_subtotal;
            }

            $cartSubTotalAfterDiscount = $subtotal - $discount_on_product;
        }
        $coupons = $this->couponList(session()->get('customer_id'));
        $coupon = $coupons->firstWhere('code', $cart['coupon_code'] ?? '');
        $couponDiscountAmount = 0;

        if (!empty($coupon)) {
            if ($coupon->min_purchase > $cartSubTotalAfterDiscount) {
                unset($cart['coupon_code'], $cart['coupon_discount']);

            } else {
                if ($coupon->discount_type == 'amount') {
                    $couponDiscountAmount = $coupon->discount;
                    if ($couponDiscountAmount > $cartSubTotalAfterDiscount) {
                        $couponDiscountAmount = $cartSubTotalAfterDiscount;
                    }
                }

                if ($coupon->discount_type == 'percent') {
                    $couponDiscountAmount = (($cartSubTotalAfterDiscount * $coupon->discount) / 100);
                    if ($couponDiscountAmount > $coupon->max_discount) {
                        $couponDiscountAmount = $coupon->max_discount;
                    }
                }


                $cart['coupon_discount'] = $couponDiscountAmount;
                $cart['coupon_code'] = $coupon->code;
            }
        } else {
            unset($cart['coupon_code'], $cart['coupon_discount']);
        }

        $afterCouponDiscountPrice = $cartSubTotalAfterDiscount - $couponDiscountAmount;

        if ($afterCouponDiscountPrice <= 0) {
            unset($cart['extra_discount'], $cart['extra_discount_type']);
        }

        if ($cart->has('extra_discount_type')) {
            if ($cart['extra_discount_type'] == 'amount') {
                $extra_discount = $cart['extra_discount'];
                if ($extra_discount > $afterCouponDiscountPrice) {
                    $extra_discount = $afterCouponDiscountPrice;
                }
                $cart['extra_discount'] = $extra_discount;
                $cart['extra_discount_type'] ='amount';
            }
        }

        session()->put('cart', $cart);
    }

}
