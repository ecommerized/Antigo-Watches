<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Http\Requests\StoreOrder;
use App\Library\Payer;
use App\Library\Payment as PaymentInfo;
use App\Library\Receiver;
use App\Models\Coupon;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Traits\OrderPricing;
use App\Traits\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use function App\CentralLogics\translate;

class PaymentController extends Controller
{
    use OrderPricing;
    public function payment(StoreOrder $request)
    {
        if (!session()->has('payment_method')) {
            session()->put('payment_method', 'ssl_commerz');
        }

        if ($request->filled('customer_id')) {
            session()->put('customer_id', $request->customer_id);
        }

        if ($request->filled('callback')) {
            session()->put('callback', $request->callback);
        }

        if ($request->filled('is_guest')) {
            session()->put('is_guest', $request->is_guest);
        }

        try {
            $amountData = $this->calculateOrderAmount(
                $request->cart,
                $request->coupon_code,
                $request->customer_id
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = collect($e->errors())->map(function($messages, $field) {
                return [
                    'code' => $field,
                    'message' => $messages[0] ?? ''
                ];
            })->values();

            return response()->json(['errors' => $errors], 403);
        } catch (\Exception $e) {
            return response()->json(['errors' => [
                ['code' => 'coupon_code', 'message' => $e->getMessage()]
            ]], 403);
        }

        $deliveryCharge = $request['order_type'] === 'self_pickup' ? 0 : Helpers::get_delivery_charge(
            branchId: $request['branch_id'],
            distance: $request['distance'] ?? 0,
            selectedDeliveryArea: $request['selected_delivery_area'] ?? null
        );

        session()->put('order_amount', $amountData['order_amount'] + $deliveryCharge);
        $order_amount = session('order_amount');
        $customer_id = session('customer_id');
        $is_guest = session('is_guest') == 1 ? 1 : 0;

        if (!isset($order_amount)) {
            return response()->json(['errors' => ['message' => 'Amount not found']], 403);
        }

        if ($order_amount < 0) {
            return response()->json(['errors' => ['message' => 'Amount is less than 0']], 403);
        }

        if (!$request->has('payment_method')) {
            return response()->json(['errors' => ['message' => 'Payment not found']], 403);
        }

        $additional_data = [
            'business_name' => Helpers::get_business_settings('restaurant_name') ?? '',
            'business_logo' => asset('storage/app/public/ecommerce/' . Helpers::get_business_settings('logo'))
        ];

        if ($is_guest == 1) {
            $customer = collect([
                'f_name' => 'Guest',
                'l_name' => 'Customer',
                'phone' => '+8801100000000',
                'email' =>  'guest@mail.com',
            ]);
        } else {
            $customer = User::find($customer_id);
            if (!isset($customer)) {
                return response()->json(['errors' => ['message' => 'Customer not found']], 403);
            }
            $customer = collect([
                'f_name' => $customer['f_name'],
                'l_name' => $customer['l_name'],
                'phone' => $customer['phone'] ?? '+8801100000000',
                'email' => $customer['email'] ?? 'test@mail.com',
            ]);
        }

        $payer = new Payer($customer['f_name'] . ' ' . $customer['l_name'] , $customer['email'], $customer['phone'], '');

        $payment_info = new PaymentInfo(
            success_hook: 'order_place',
            failure_hook: 'order_cancel',
            currency_code: Helpers::currency_code(),
            payment_method: $request->payment_method,
            payment_platform: $request->payment_platform,
            payer_id: session('customer_id'),
            receiver_id: '100',
            additional_data: $additional_data,
            payment_amount: $order_amount,
            external_redirect_link: session('callback'),
            attribute: 'order',
            attribute_id: time()
        );

        $receiver_info = new Receiver('receiver_name','example.png');

        $redirect_link = Payment::generate_link($payer, $payment_info, $receiver_info);

        return response()->json(['redirect_link' => $redirect_link], 200);
    }

    public function success()
    {
        if (session()->has('callback')) {
            return redirect(session('callback') . '/success');
        }
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail()
    {
        if (session()->has('callback')) {
            return redirect(session('callback') . '/fail');
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }


    private function couponList($customerId): Collection
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
            ->filter(function($item) use ($totalOrders) {
                if ($item->coupon_type == 'first_order') {
                    return $totalOrders == 0 && $item->used_count < 1;
                }
                return $item->used_count < $item->limit;
            })
            ->values();
    }
}
