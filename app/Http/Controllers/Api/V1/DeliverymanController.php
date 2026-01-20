<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\DeliveryHistory;
use App\Models\DeliveryMan;
use App\Models\Order;
use App\Traits\WalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DeliverymanController extends Controller
{
    use WalletTransaction;

    public function __construct(
        private DeliveryMan     $deliveryMan,
        private DeliveryHistory $deliveryHistory,
        private Order           $order,
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($dm)) {
            return response()->json([
                'errors' => [
                    ['code' => '401', 'message' => translate('Invalid token')]
                ]
            ], 401);
        }
        return response()->json($dm, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCurrentOrders(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($dm)) {
            return response()->json([
                'errors' => [
                    ['code' => '401', 'message' => translate('Invalid token')]
                ]
            ], 401);
        }
        $orders = $this->order->with(['customer', 'branch'])
            ->whereIn('order_status', ['pending', 'processing', 'out_for_delivery', 'confirmed'])
            ->where(['delivery_man_id' => $dm['id']])->get();
        return response()->json($orders, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function recordLocationData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($dm)) {
            return response()->json([
                'errors' => [
                    ['code' => '401', 'message' => translate('Invalid token')]
                ]
            ], 401);
        }
        $data = [
            'deliveryman_id' => $dm['id'],
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'time' => now(),
            'location' => $request['location'],
            'created_at' => now(),
            'updated_at' => now()
        ];
        $deliveryHistory = $this->deliveryHistory->where(['deliveryman_id' => $dm['id']])->whereNull('order_id')->latest()->first();

        if ($deliveryHistory) {
            $deliveryHistory->update($data);
        } else {
            $this->deliveryHistory->insert($data);
        }

        return response()->json(['message' => translate('location recorded')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($dm)) {
            return response()->json([
                'errors' => [
                    ['code' => '401', 'message' => translate('Invalid token')]
                ]
            ], 401);
        }

        $history = $this->deliveryHistory->where(['order_id' => $request['order_id'], 'deliveryman_id' => $dm['id']])->get();
        return response()->json($history, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateOrderStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'order_id' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($dm)) {
            return response()->json([
                'errors' => [
                    ['code' => '401', 'message' => translate('Invalid token')]
                ]
            ], 401);
        }

        $order = $this->order->where(['id' => $request['order_id'], 'delivery_man_id' => $dm['id']])->first();
        if (!isset($order)) {
            return response()->json(['errors' => [['code' => '403', 'message' => 'order not found']]], 403);

        }

        if ($order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled') {
            return response()->json(['errors' => [['code' => '403', 'message' => 'you can not change the status of ' . $order->order_status . ' order!']]], 403);
        }

        $order->order_status = $request['status'];
        DB::beginTransaction();
        $order->save();
        if ($request['status'] == 'delivered') {
            if ($order->is_guest != 1) {
                $this->customerCreditWalletTransactionsForOrderComplete(customer: $order->customer, order: $order);
            }
        }
        DB::commit();

        $fcmToken = $order->is_guest == 0 ? ($order->customer ? $order->customer->cm_firebase_token : null) : ($order->guest ? $order->guest->fcm_token : null);

        if ($request['status'] == 'out_for_delivery') {
            $value = Helpers::order_status_update_message('ord_start');
        } elseif ($request['status'] == 'delivered') {
            $value = Helpers::order_status_update_message('delivery_boy_delivered');
        }

        try {
            if ($value) {
                $data = [
                    'title' => 'Order',
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                    'type' => 'order',
                ];
                Helpers::send_push_notif_to_device($fcmToken, $data);
            }
        } catch (\Exception $e) {

        }

        return response()->json(['message' => translate('Status updated')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getOrderDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($dm)) {
            return response()->json([
                'errors' => [
                    ['code' => '401', 'message' => translate('Invalid token')]
                ]
            ], 401);
        }
        $order = $this->order->with(['details'])->where(['delivery_man_id' => $dm['id'], 'id' => $request['order_id']])->first();
        $details = $order->details;
        $details = Helpers::order_details_formatter($details);
        return response()->json($details, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllOrders(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($dm)) {
            return response()->json([
                'errors' => [
                    ['code' => '401', 'message' => translate('Invalid token')]
                ]
            ], 401);
        }

        $orders = $this->order
            ->with(['deliveryAddress', 'customer'])
            ->whereNotIn('order_status', ['pending', 'processing', 'out_for_delivery', 'confirmed'])
            ->where(['delivery_man_id' => $dm['id']])
            ->get();

        return response()->json($orders, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getLastLocation(Request $request): JsonResponse
    {
        $lastData = $this->deliveryHistory->where(['order_id' => null, 'deliveryman_id' => auth('api')->user()->id])->latest()->first();

        return response()->json($lastData, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function orderPaymentStatusUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($dm)) {
            return response()->json([
                'errors' => [
                    ['code' => '401', 'message' => 'Invalid token!']
                ]
            ], 401);
        }

        if ($this->order->where(['delivery_man_id' => $dm['id'], 'id' => $request['order_id']])->first()) {
            $this->order->where(['delivery_man_id' => $dm['id'], 'id' => $request['order_id']])->update([
                'payment_status' => $request['status']
            ]);
            return response()->json(['message' => 'Payment status updated'], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('not found')]
            ]
        ], 404);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (!isset($dm)) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery-man', 'message' => translate('Invalid token')]
                ]
            ], 401);
        }

        $this->deliveryMan->where(['id' => $dm['id']])->update([
            'fcm_token' => $request['fcm_token']
        ]);

        return response()->json(['message' => translate('successfully updated')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeAccount(Request $request): JsonResponse
    {
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();
        if (isset($dm)) {
            Helpers::file_remover('delivery-man/', $dm->image);
            $dm->delete();

        } else {
            return response()->json(['status_code' => 401, 'message' => translate('Not found')], 401);
        }

        return response()->json(['status_code' => 200, 'message' => translate('Successfully deleted')], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function orderModel(Request $request): JsonResponse
    {
        $dm = $this->deliveryMan->where(['auth_token' => $request['token']])->first();

        if (!isset($dm)) {

            return response()->json([
                'errors' => [
                    ['code' => '401', 'message' => translate('Invalid token!')]
                ]
            ], 401);
        }

        $order = $this->order
            ->with(['customer', 'branch'])
            ->where(['delivery_man_id' => $dm['id'], 'id' => $request->id])
            ->first();

        return response()->json($order, 200);
    }

    public function getOrdersCount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $deliveryman = $this->deliveryMan->where('auth_token', $request['token'])->first();

        if (!$deliveryman) {
            return response()->json(['errors' => [['code' => 'delivery-man', 'message' => translate('Invalid token!')]]], 401);
        }

        $orders['out_for_delivery'] = $this->order->where(['delivery_man_id' => $deliveryman->id, 'order_status' => 'out_for_delivery'])->count();

        return response()->json($orders, 200);

    }
}
