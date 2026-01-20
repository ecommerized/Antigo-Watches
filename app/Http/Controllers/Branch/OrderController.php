<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\DeliveryHistory;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Traits\OrderPricing;
use App\Traits\WalletTransaction;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use function App\CentralLogics\translate;

class OrderController extends Controller
{
    use WalletTransaction, OrderPricing;
    public function __construct(
        private Order $order,
        private OrderDetail $orderDetail,
        private Product $product,
    ){}

    /**
     * @param $status
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list($status, Request $request): View|Factory|Application
    {
        $perPage   = (int) $request->query('per_page', Helpers::getPagination());
        $search    = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $this->order->where('checked', 0)->update(['checked' => 1]);
        $query = $this->order->where(['branch_id' => auth('branch')->id()])->notPos()->with(['customer', 'branch']);

        if ($status !== 'all') {
            $query = $query->where('order_status', $status);
        }

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end   = Carbon::parse($endDate)->endOfDay();
            $query = $query->whereBetween('created_at', [$start, $end]);
        }

        if ($search) {
            $query = $query->where(function ($q) use ($search) {
                $q->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('order_status', 'like', "%{$search}%")
                    ->orWhere('payment_status', 'like', "%{$search}%");
            });
        }

        $queryParam = collect([
            'search'     => $search,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'per_page'   => $perPage,
        ])->filter(fn($value) => filled($value))->all();

        $orders = $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($queryParam);

        return view('branch-views.order.list', compact(
            'orders', 'status', 'search', 'startDate', 'endDate','perPage'
        ));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $orders=$this->order->where(['branch_id'=>auth('branch')->id()])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view'=>view('branch-views.order.partials._table',compact('orders'))->render()
        ]);
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function details($id): View|Factory|RedirectResponse|Application
    {
        $order = $this->order->with('details')->where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        if (isset($order)) {
            $orderedProducts = $this->existedProducts($order);

            $deliveredToLatitude = $deliveredToLongitude = $deliveredFromLatitude = $deliveredFromLongitude = 0;
            if ($order->order_status == 'out_for_delivery') {
                $deliveredToAddress = $order->deliveryAddress;
                $deliveryManHistory = DeliveryHistory::where('deliveryman_id', $order->delivery_man_id)->whereNull('order_id')->first();
                $deliveredFromAddress = !empty($order->delivery_man_id) && !empty($deliveryManHistory) ? $deliveryManHistory : $order->branch;
                $deliveredToLatitude = (float)($deliveredToAddress->latitude ?? 0);
                $deliveredToLongitude = (float)($deliveredToAddress->longitude ?? 0);
                $deliveredFromLatitude = (float)($deliveredFromAddress->latitude ?? 0);
                $deliveredFromLongitude = (float)($deliveredFromAddress->longitude ?? 0);
            }
            return view('branch-views.order.order-view', compact('order', 'orderedProducts', 'deliveredToLatitude', 'deliveredToLongitude', 'deliveredFromLatitude', 'deliveredFromLongitude'));
        } else {
            Toastr::info(translate('No more orders!'));
            return back();
        }
    }

    private function existedProducts(Order $order): Collection
    {
        return $order->details->map(function ($productDetail) {
            $product = $this->product->find($productDetail->product_id);
            $productVariation = json_decode($product->variations, true);
            $data = [];
            $data['id'] = $product->id;
            $data['name'] = $product->name;
            $data['quantity'] = $productDetail->quantity;
            $data['variant'] = json_decode($productDetail->variation, true)[0]['type'] ?? json_decode($productDetail->variation, true)['type'] ?? '';
            $data['base_price'] = $productDetail->price ?? 0;
            $data['price_with_symbol'] = Helpers::set_symbol($productDetail->price);
            $data['price'] = $productDetail->price ?? 0;
            $data['discount'] = $productDetail->discount_on_product ?? 0;
            $data['product_discount'] = $productDetail->price - $productDetail->discount_on_product ?? 0;
            $data['image'] = $product['image_fullpath'][0];
            $data['total_stock'] = !empty(json_decode($productDetail->variation, true)) ? (collect($productVariation)->firstWhere('type', $data['variant'])['stock'] ?? $product->total_stock) + $productDetail->quantity : $product->total_stock + $productDetail->quantity;
            $data['total_price'] = Helpers::set_symbol($productDetail->price * $productDetail->quantity);
            $data['total_discount_price'] = Helpers::set_symbol(($productDetail->price -  $productDetail->discount_on_product) * $productDetail->quantity);
            return $data;
        });
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $order = $this->order->where(['id' => $request->id, 'branch_id' => auth('branch')->id()])->first();

        if (in_array($order->order_status, ['returned', 'delivered', 'failed', 'canceled'])) {
            Toastr::warning(translate('you_can_not_change_the_status_of '. $order->order_status .' order'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['payment_status'] != 'paid') {
            Toastr::warning(translate('you_can_not_delivered_a_order_when_order_status_is_not_paid. please_update_payment_status_first'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['transaction_reference'] == null && !in_array($order['payment_method'],['cash_on_delivery','wallet'])) {
            Toastr::warning(translate('add_your_payment_reference_first'));
            return back();
        }

        if (($request->order_status == 'out_for_delivery' || $request->order_status == 'delivered') && $order['delivery_man_id'] == null && $order['order_type'] != 'self_pickup') {
            Toastr::warning(translate('Please assign delivery man first!'));
            return back();
        }

        if ($request->order_status == 'returned' || $request->order_status == 'failed' || $request->order_status == 'canceled') {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = $this->product->find($detail['product_id']);

                    if($product != null) {
                        $varStore = [];
                        if (count(json_decode($detail['variation'], true)) > 0 ){
                            $type = json_decode($detail['variation'], true)[0]['type'] ?? json_decode($detail['variation'], true)['type'];
                            foreach (json_decode($product['variations'], true) as $var) {
                                if ($type == $var['type']) {
                                    $var['stock'] += $detail['quantity'];
                                }
                                $varStore[] = $var;
                            }
                        }
                        $this->product->where(['id' => $product['id']])->update([
                            'variations' => json_encode($varStore),
                            'total_stock' => $product['total_stock'] + $detail['quantity'],
                        ]);
                        $this->orderDetail->where(['id' => $detail['id']])->update([
                            'is_stock_decreased' => 0
                        ]);
                    }
                }
            }
        } else {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 0) {
                    $product = $this->product->find($detail['product_id']);

                    if($product != null){
                        foreach ($order->details as $c) {
                            $product = $this->product->find($c['product_id']);
                            $type = json_decode($c['variation'])[0]->type;
                            foreach (json_decode($product['variations'], true) as $var) {
                                if ($type == $var['type'] && $var['stock'] < $c['quantity']) {
                                    Toastr::error(translate('Stock is insufficient!'));
                                    return back();
                                }
                            }
                        }

                        $type = json_decode($detail['variation'])[0]->type;
                        $varStore = [];
                        foreach (json_decode($product['variations'], true) as $var) {
                            if ($type == $var['type']) {
                                $var['stock'] -= $detail['quantity'];
                            }
                            $varStore[] = $var;
                        }
                        $this->product->where(['id' => $product['id']])->update([
                            'variations' => json_encode($varStore),
                            'total_stock' => $product['total_stock'] - $detail['quantity'],
                        ]);
                        $this->orderDetail->where(['id' => $detail['id']])->update([
                            'is_stock_decreased' => 1
                        ]);
                    }
                }
            }
        }

        $order->order_status = $request->order_status;
        DB::beginTransaction();
        $order->save();
        if ($request->order_status == 'delivered') {
            if ($order->is_guest != 1) {
                $this->customerCreditWalletTransactionsForOrderComplete(customer: $order->customer,order: $order);
            }
        }
        DB::commit();

        $fcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);
        $value = Helpers::order_status_update_message($request->order_status);

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'image' => '',
                    'order_id' => $order->id,
                    'type' => 'order',
                ];
                if($fcmToken != null) {
                    Helpers::send_push_notif_to_device($fcmToken, $data);
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification failed for Customer!'));
        }

        if (in_array($request->order_status, ['processing', 'out_for_delivery', 'returned', 'failed', 'canceled'])) {

            $DMFcmToken = $order->delivery_man?->fcm_token ?? null;
            $value = translate('One of your order is '. $request->order_status);

            try {
                if (!is_null($DMFcmToken)) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                        'type' => 'order',
                    ];
                    Helpers::send_push_notif_to_device($DMFcmToken, $data);
                }
            } catch (\Exception $e) {
                Toastr::warning(translate('Push notification failed for DeliveryMan!'));
            }
        }

        Toastr::success(translate('Order status updated!'));
        return back();
    }

    /**
     * @param $orderId
     * @param $deliveryManId
     * @return JsonResponse
     */
    public function addDeliveryMan($orderId, $deliveryManId): JsonResponse
    {
        if ($deliveryManId == 0) {
            return response()->json([], 401);
        }
        $order = $this->order->where(['id' => $orderId, 'branch_id' => auth('branch')->id()])->first();

        if($order->order_status == 'pending' || $order->order_status == 'confirmed' || $order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
            return response()->json(['status' => false, 'message' => 'You can not add delivery man when the status is '. $order->order_status ], 200);
        }

        $order->delivery_man_id = $deliveryManId;
        $order->save();

        $fcmToken = $order->delivery_man->fcm_token;
        $customerFcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);
        $value = Helpers::order_status_update_message('del_assign');

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order',
                ];
                Helpers::send_push_notif_to_device($fcmToken, $data);
                $cs_notify_message = Helpers::order_status_update_message('customer_notify_message');
                if($cs_notify_message) {
                    $data['description'] = $cs_notify_message;
                    if($customerFcmToken != null) {
                        Helpers::send_push_notif_to_device($customerFcmToken, $data);
                    }
                }
            }
        } catch (\Exception $e) {
            Toastr::warning(translate('Push notification failed for DeliveryMan!'));
        }

        Toastr::success(translate('Order deliveryman added!'));
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function paymentStatus(Request $request): RedirectResponse
    {
        $order = $this->order->where(['id' => $request->id, 'branch_id' => auth('branch')->id()])->first();
        if ($request->payment_status == 'paid' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery') {
            Toastr::warning('Add your payment reference code first!');
            return back();
        }
        $order->payment_status = $request->payment_status;
        $order->save();
        Toastr::success(translate('Payment status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateShipping(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
            'road' => $request->road,
            'house' => $request->house,
            'floor' => $request->floor,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('customer_addresses')->where('id', $id)->update($address);
        Toastr::success(translate('Address updated!'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function generateInvoice($id): Factory|View|Application
    {
        $order = $this->order->where(['id' => $id, 'branch_id' => auth('branch')->id()])->first();
        return view('branch-views.order.invoice', compact('order'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function addPaymentRefCode(Request $request, $id): RedirectResponse
    {
        $this->order->where(['id' => $id, 'branch_id' => auth('branch')->id()])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success(translate('Payment reference code is added!'));
        return back();
    }

    public function exportOrders(Request $request, $status): StreamedResponse|string
    {
        $queryParams = [];
        $search = $request['search'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];

        if ($status != 'all') {
            $query = $this->order->with(['customer'])->where(['order_status' => $status, 'branch_id' => auth('branch')->id()])
                ->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                })->where(['order_status' => $status]);
        } else {
            $query = $this->order->with(['customer'])->where(['branch_id' => auth('branch')->id()])
                ->when((!is_null($start_date) && !is_null($end_date)), function ($query) use ($start_date, $end_date) {
                    return $query->whereDate('created_at', '>=', $start_date)
                        ->whereDate('created_at', '<=', $end_date);
                });
        }

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('payment_status', 'like', "%{$value}%");
                }
            });
            $queryParams = ['search' => $request['search']];
        }

        $orders = $query->notPos()->orderBy('id', 'desc')->get();

        $storage = [];

        foreach($orders as $order){
            $branch = $order->branch ? $order->branch->name : '';
            if ($order->is_guest == 0){
                $customer = $order->customer ? $order->customer->f_name .' '. $order->customer->l_name : 'Customer Deleted';
            }else{
                $customer = 'Guest Customer';
            }
            $deliveryMan = $order->delivery_man ? $order->delivery_man->f_name .' '. $order->delivery_man->l_name : '';

            $storage[] = [
                'order_id' => $order['id'],
                'customer' => $customer,
                'order_amount' => $order['order_amount'],
                'coupon_discount_amount' => $order['coupon_discount_amount'],
                'payment_status' => $order['payment_status'],
                'order_status' => $order['order_status'],
                'total_tax_amount'=>$order['total_tax_amount'],
                'payment_method' => $order['payment_method'],
                'transaction_reference' => $order['transaction_reference'],
                'delivery_man' => $deliveryMan,
                'delivery_charge' => $order['delivery_charge'],
                'coupon_code' => $order['coupon_code'],
                'order_type' => $order['order_type'],
                'branch'=>  $branch,
                'extra_discount' => $order['extra_discount'],
            ];
        }
        return (new FastExcel($storage))->download('orders.xlsx');

    }

    public function searchProduct(Request $request): JsonResponse
    {
        $keyword = $request->get('search');
        $products = $this->product->where('status', 1)->where('name', 'like', "%{$keyword}%")
            ->orWhere('id', 'like', "%{$keyword}%")
            ->get();
        $order = $this->order->where(['id' => $request->order_id])->first();
        $existedProducts = $this->existedProducts($order);

        return response()->json([
            'success' => true,
            'view' => view('branch-views.order.partials.product-search-result', compact('products', 'existedProducts'))->render(),
        ]);
    }

    public function updateProductList(Request $request, $id): JsonResponse
    {
        if (!$request->filled('products'))
        {
            return response()->json(['errors' => [['code' => 'empty-product', 'message' => translate('Product list is empty')]]], 403);
        }
        $order = $this->order->with(['details', 'orderArea'])->where('id', $id)->first();
        $data = [
            'user_id' => $order->user_id,
            'is_guest' => $order->is_guest,
            'coupon_discount_title' => $order->coupon_discount_title,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'coupon_code' => $order->coupon_code,
            'payment_method' => $order->payment_method,
            'transaction_reference' => $order->transaction_reference,
            'order_note' => $order->order_note,
            'order_type' => $order->order_type,
            'branch_id' => $order->branch_id,
            'bring_change_amount' => $order->bring_change_amount,
            'delivery_address_id' => $order->delivery_address_id,
            'delivery_address' => $order->delivery_address,
            'created_at' => $order->created_at,
            'updated_at' => now(),
        ];

        DB::transaction(function () use($request, $order, $data,){
            foreach ($order->details as $existingDetail) {
                $existingProduct = $this->product->find($existingDetail->product_id);
                $variation = [];
                $quantity = $existingDetail->quantity;
                if (count(json_decode($existingProduct['variations'], true)) > 0) {
                    $existingVariation = json_decode($existingDetail->variation, true);
                    $type = $existingVariation[0]['type'] ?? $existingVariation['type'];
                    $varStore = [];
                    foreach (json_decode($existingProduct['variations'], true) as $var) {
                        if ($var['type'] == $type) {
                            $var['stock'] += $quantity;
                        }
                        $varStore[] = $var;
                    }
                    $this->product->where('id', $existingProduct->id)->update([
                        'variations' => json_encode($varStore),
                        'total_stock' => $existingProduct['total_stock'] + $quantity,
                    ]);
                } else {
                    $this->product->where('id', $existingProduct->id)->update([
                        'total_stock' => $existingProduct['total_stock'] + $quantity,
                    ]);
                }
            }

            $order->details()->delete();


            foreach ($request->products as $product) {
                $existingProduct = $this->product->find($product['id']);
                if (count(json_decode($existingProduct['variations'], true)) > 0) {
                    $variation = collect(json_decode($existingProduct['variations'], true))
                        ->where('type', $product['variant'])
                        ->values()
                        ->all();
                    $price = Helpers::variation_price($existingProduct, json_encode($variation));
                } else {
                    $price = $existingProduct['price'];
                }
                $discountOnProduct = Helpers::discount_calculate($existingProduct, $price);
                $taxAmount = Helpers::tax_calculate($existingProduct, $price - $discountOnProduct);
                $orderDetails = [
                    'order_id' => $order->id,
                    'product_id' => $product['id'],
                    'product_details' => $existingProduct,
                    'quantity' => $product['quantity'],
                    'price' => $price,
                    'unit' => $existingProduct['unit'],
                    'tax_amount' => $taxAmount,
                    'discount_on_product' => $discountOnProduct,
                    'discount_type' => 'discount_on_product',
                    'variant' => $product['variant'],
                    'variation' => !empty($variation) ? json_encode($variation[0]) : json_encode([]),
                    'is_stock_decreased' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if (count(json_decode($existingProduct['variations'], true)) > 0) {
                    $type = $variation[0]['type'];
                    $varStore = [];
                    foreach (json_decode($existingProduct['variations'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['stock'] -= $product['quantity'];
                        }
                        $varStore[] = $var;
                    }
                    $this->product->where(['id' => $existingProduct['id']])->update([
                        'variations' => json_encode($varStore),
                        'total_stock' => $existingProduct['total_stock'] - $product['quantity'],
                    ]);
                } else {
                    $this->product->where(['id' => $existingProduct['id']])->update([
                        'total_stock' => $existingProduct['total_stock'] - $product['quantity'],
                    ]);
                }

                DB::table('order_details')->insert($orderDetails);
            }
            $orderCalculation = $this->calculateOrderAmountForEdit(
                $request->products,
                $order->coupon_code ?? null,
                $order->user_id
            );

            $orderAmount = $orderCalculation['order_amount'];
            $couponDiscount = $orderCalculation['coupon_discount'];
            $totalTax = $orderCalculation['total_tax'];
            $deliveryCharge = $order->order_type === 'self_pickup' ? 0 : Helpers::get_delivery_charge(
                branchId: $order->branch_id,
                distance: $order->orderArea?->distance ?? 0,
                selectedDeliveryArea: $order->orderArea?->area_id ?? null
            );
            $data = array_merge($data, [
                'order_amount' => $orderAmount + $deliveryCharge,
                'coupon_discount_amount' => $couponDiscount,
                'total_tax_amount' => $totalTax,
                'delivery_charge' => $deliveryCharge,
            ]);

            $order->update($data);
        });


        return response()->json([
            'status' => true,
            'message' => translate('Order placed successfully'),
            'order_id' => $order->id
        ], 200);
    }
}
