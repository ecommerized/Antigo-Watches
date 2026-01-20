<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrder;
use App\Models\CustomerAddress;
use App\Models\DMReview;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\GuestUser;
use App\Models\OrderArea;
use App\Traits\OrderPricing;
use App\Traits\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Mockery\Exception;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    use WalletTransaction, OrderPricing;
    public function __construct(
        private CustomerAddress $customerAddress,
        private DMReview        $dmReview,
        private Order           $order,
        private OrderDetail     $orderDetail,
        private Product         $product,
        private OrderArea $orderArea
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function trackOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $phone = $request->input('phone');
        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        $order = $this->order->find($request['order_id']);

        if (!isset($order)){
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        if (!is_null($phone)){
            if ($order['is_guest'] == 0){
                $trackOrder = $this->order
                    ->with(['customer', 'deliveryAddress'])
                    ->where(['id' => $request['order_id']])
                    ->whereHas('customer', function ($customerSubQuery) use ($phone) {
                        $customerSubQuery->where('phone', $phone);
                    })
                    ->first();
            }else{
                $trackOrder = $this->order
                    ->with(['deliveryAddress'])
                    ->where(['id' => $request['order_id']])
                    ->whereHas('deliveryAddress', function ($addressSubQuery) use ($phone) {
                        $addressSubQuery->where('contact_person_number', $phone);
                    })
                    ->first();
            }
        }else{
            $trackOrder = $this->order
                ->where(['id' => $request['order_id'], 'user_id' => $userId, 'is_guest' => $userType])
                ->first();
        }

        if (!isset($trackOrder)){
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        return response()->json(OrderLogic::track_order($request['order_id']), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function placeOrder(StoreOrder $request): JsonResponse
    {
        try{
            $orderCalculation = $this->calculateOrderAmount(
                $request['cart'],
                $request->coupon_code ?? null,
                auth('api')->id() ?? config('guest_id')
            );

            $orderAmount = $orderCalculation['order_amount'];
            $couponDiscount = $orderCalculation['coupon_discount'];
            $userId = auth('api')->id() ?? config('guest_id');
            $userType = auth('api')->check() ? 0 : 1;
            $deliveryCharge = $request['order_type'] === 'self_pickup' ? 0 : Helpers::get_delivery_charge(
                    branchId: $request['branch_id'],
                    distance: $request['distance'] ?? 0,
                    selectedDeliveryArea: $request['selected_delivery_area'] ?? null
                );
            $orderData = [
                'user_id' => $userId,
                'is_guest' => $userType,
                'order_amount' => $orderAmount + $deliveryCharge,
                'coupon_discount_amount' => $couponDiscount,
                'coupon_discount_title' => $request->coupon_discount_title == 0 ? null : 'coupon_discount_title',
                'coupon_code' => $request['coupon_code'] ?? null,
                'payment_status' => $request['payment_method'] === 'cash_on_delivery' ? 'unpaid' : 'paid',
                'order_status' => $request['payment_method'] === 'cash_on_delivery' ? 'pending' : 'confirmed',
                'total_tax_amount' => $orderCalculation['total_tax'],
                'payment_method' => $request['payment_method'],
                'transaction_reference' => $request->transaction_reference ?? null,
                'order_note' => $request['order_note'] ?? null,
                'order_type' => $request['order_type'],
                'branch_id' => $request['branch_id'],
                'bring_change_amount' => $request['bring_change_amount'],
                'delivery_address_id' => $request['delivery_address_id'],
                'delivery_charge' => $deliveryCharge,
                'delivery_address' => $this->customerAddress->find($request->delivery_address_id) ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            DB::beginTransaction();
            $order = $this->order->create($orderData);
            foreach ($request['cart'] as $c) {
                $product = $this->product->find($c['product_id']);
                $price = !empty(json_decode($product->variations, true))
                    ? Helpers::variation_price($product, json_encode($c['variation']))
                    : $product->price;
                $discountAmount = Helpers::discount_calculate($product, $price);
                $taxAmount = Helpers::tax_calculate($product, $price - $discountAmount);

                DB::table('order_details')->insert([
                    'order_id' => $order->id,
                    'product_id' => $c['product_id'],
                    'product_details' => $product,
                    'quantity' => $c['quantity'],
                    'price' => $price,
                    'unit' => $product['unit'],
                    'tax_amount' => $taxAmount,
                    'discount_on_product' => $discountAmount,
                    'discount_type' => 'discount_on_product',
                    'variant' => !empty($c['variation']) ? $c['variation'][0]['type'] : null,
                    'variation' => !empty($c['variation']) ? json_encode($c['variation'][0]) : json_encode($c['variation']),
                    'is_stock_decreased' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if (!empty(json_decode($product->variations, true))) {
                    $type = $c['variation'][0]['type'] ?? null;
                    $varStore = [];
                    foreach (json_decode($product->variations, true) as $var) {
                        if ($type == $var['type']) {
                            $var['stock'] -= $c['quantity'];
                        }
                        $varStore[] = $var;
                    }
                    $this->product->where('id', $product->id)->update([
                        'variations' => json_encode($varStore),
                        'total_stock' => $product->total_stock - $c['quantity']
                    ]);
                } else {
                    $this->product->where('id', $product->id)->update([
                        'total_stock' => $product->total_stock - $c['quantity']
                    ]);
                }
            }

            if ($request['selected_delivery_area']) {
                $orderArea = $this->orderArea;
                $orderArea->order_id = $order->id;
                $orderArea->branch_id = $orderData['branch_id'];
                $orderArea->area_id = $request['selected_delivery_area'];
                $orderArea->save();
            }

            if ($request['payment_method'] == 'wallet') {
                $this->customerDebitWalletTransactionsForOrderPlace(customer: $order->customer, order: $order);
            }
            DB::commit();

            if (auth('api')->user()){
                $fcmToken = auth('api')->user()->cm_firebase_token;
            }else{
                $guest = GuestUser::find(config('guest_id'));
                $fcmToken = $guest ? $guest->fcm_token : '';
            }
            $value = Helpers::order_status_update_message('pending');
            $emailServices = Helpers::get_business_settings('mail_config');
            if (isset($emailServices['status']) && $emailServices['status'] == 1 && isset(auth('api')->user()->id) && $value) {
                try {
                    $data = [
                        'title' => 'Order',
                        'description' => $value,
                        'order_id' => $order->id,
                        'image' => '',
                        'type' => 'order',
                    ];
                    Helpers::send_push_notif_to_device($fcmToken, $data);
                    Mail::to(auth('api')->user()?->email)->send(new \App\Mail\OrderPlaced($order->id));
                } catch (\Throwable $mailException) {
                }
            }

        }  catch (ValidationException $e) {
            $errors = collect($e->errors())->map(function($messages, $field) {
                return [
                    'code' => $field,
                    'message' => $messages[0] ?? ''
                ];
            })->values();

            return response()->json(['errors' => $errors], 403);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => [['message' => $e->getMessage()]]], 403);
        }

        return response()->json([
            'message' => translate('Order placed successfully'),
            'order_id' => $order->id
        ], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderList(Request $request): JsonResponse
    {
        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        $orders = $this->order->with(['customer', 'delivery_man.rating', 'details'])
            ->withCount('details')
            ->where(['user_id' => $userId, 'is_guest' => $userType])
            ->get();

        $orders->each(function ($order) {
            $order->total_quantity = $order->details->sum('quantity');
        });

        $orders->map(function ($data) {
            $data['deliveryman_review_count'] = $this->dmReview->where(['delivery_man_id' => $data['delivery_man_id'], 'order_id' => $data['id']])->count();
            return $data;
        });

        return response()->json($orders->map(function ($data) {
            $data->details_count = (integer)$data->details_count;
            return $data;
        }), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'phone' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;
        $phone = $request->input('phone');

        $order = $this->order->find($request['order_id']);
        if (!isset($order)){
            return response()->json([
                'errors' => [['code' => 'order', 'message' => 'Order not found!']]], 403);
        }

        if (!is_null($phone)){
            if ($order['is_guest'] == 0){
                $details = $this->orderDetail->with(['order'])
                    ->where(['order_id' => $request['order_id']])
                    ->whereHas('order.customer', function ($customerSubQuery) use ($phone) {
                        $customerSubQuery->where('phone', $phone);
                    })
                    ->get();
            }else{
                $details = $this->orderDetail->with(['order'])
                    ->where(['order_id' => $request['order_id']])
                    ->whereHas('order.deliveryAddress', function ($addressSubQuery) use ($phone) {
                        $addressSubQuery->where('contact_person_number', $phone);
                    })
                    ->get();
            }
        }else{
            $details = $this->orderDetail->with(['order'])
                ->where(['order_id' => $request['order_id']])
                ->whereHas('order', function ($q) use ($userId, $userType) {
                    $q->where(['user_id' => $userId, 'is_guest' => $userType]);
                })
                ->get();
        }

        if ($details->count() < 1) {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => translate('Order not found!')]
                ]
            ], 404);
        }

        $details = Helpers::order_details_formatter($details);
        return response()->json($details, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function cancelOrder(Request $request): JsonResponse
    {
        $order = $this->order::find($request['order_id']);

        if (!isset($order)) {
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order not found!']]], 404);
        }

        if ($order->order_status != 'pending') {
            return response()->json(['errors' => [['code' => 'order', 'message' => 'Order can only cancel when order status is pending!']]], 403);
        }

        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        if ($this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->first()) {

            $order = $this->order->with(['details'])->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->first();

            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = $this->product->find($detail['product_id']);

                    if ($product) {
                        if (count(json_decode($product['variations'], true)) > 0) {
                            $variation = json_decode($detail['variation'], true);
                            $type = $variation[0]['type'] ?? $variation['type'];
                            $varStore = [];
                            foreach (json_decode($product['variations'], true) as $var) {
                                if ($type == $var['type']) {
                                    $var['stock'] += $detail['quantity'];
                                }
                                $varStore[] = $var;
                            }
                            $this->product->where(['id' => $product['id']])->update([
                                'variations' => json_encode($varStore),
                                'total_stock' => $product['total_stock'] + $detail['quantity'],
                            ]);
                        } else {
                            $this->product->where(['id' => $product['id']])->update([
                                'total_stock' => $product['total_stock'] + $detail['quantity'],
                            ]);
                        }
                    }

                    $this->orderDetail->where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 0
                    ]);
                }
            }

            $this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->update([
                'order_status' => 'canceled',
            ]);
            return response()->json(['message' => translate('Order canceled')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('not found')]
            ]
        ], 401);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updatePaymentMethod(Request $request): JsonResponse
    {
        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        if ($this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->first()) {
            $this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'id' => $request['order_id']])->update([
                'payment_method' => $request['payment_method']
            ]);
            return response()->json(['message' => translate('Payment method is updated.')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('not found')]
            ]
        ], 401);
    }

    public function getReorderProduct(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = $this->orderDetail
            ->where(['order_id' => $request['order_id']])
            ->get();

        if ($details->count() < 1) {
            return response()->json([
                'errors' => [['code' => 'order', 'message' => translate('Order not found!')]]], 404);
        }

        $details = Helpers::order_details_formatter($details);

        $orderProductIds = $this->orderDetail
            ->where(['order_id' => $request->order_id])
            ->pluck('product_id')
            ->toArray();

        $products = $this->product
            ->whereIn('id', $orderProductIds)
            ->latest()
            ->get();

        $products = Helpers::product_data_formatting($products, true);

        $data = [
            'order_details' => $details,
            'products' => $products
        ];

        return response()->json($data, 200);

    }
}
