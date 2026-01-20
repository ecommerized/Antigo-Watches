<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    public function __construct(
        private Coupon $coupon,
        private Order  $order
    )
    {
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        if (!auth('api')->user()) {
            return response()->json([
                'errors' => [
                    ['code' => 'coupon', 'message' => translate('Coupon list is only valid for login customer!')]
                ]
            ], 401);
        }

        $coupon = $this->coupon->active()->orderBy('id', 'desc')->get();
        return response()->json($coupon, 200);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function apply(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $userId = auth('api')->user() ? auth('api')->user()->id : config('guest_id');
        $userType = auth('api')->user() ? 0 : 1;

        if ($userType == 1){
            return response()->json([
                'errors' => [
                    ['code' => 'coupon', 'message' => translate('Coupon is only valid for login customer!')]
                ]
            ], 401);
        }

        try {
            $coupon = $this->coupon->active()->where(['code' => $request['code']])->first();
            if (isset($coupon)) {

                if ($coupon['coupon_type'] == 'first_order' && $userType == 0) {
                    $total = $this->order->where(['user_id' => $userId, 'is_guest' => 0])->count();
                    if ($total == 0) {
                        return response()->json($coupon, 200);
                    } else {
                        return response()->json([
                            'errors' => [
                                ['code' => 'coupon', 'message' => translate('This coupon in not valid for you!')]
                            ]
                        ], 401);
                    }
                }

                if ($coupon['limit'] == null) {
                    return response()->json($coupon, 200);
                } else {
                    $total = $this->order->where(['user_id' => $userId, 'is_guest' => $userType, 'coupon_code' => $request['code']])->count();
                    if ($total < $coupon['limit']) {
                        return response()->json($coupon, 200);
                    } else {
                        return response()->json([
                            'errors' => [
                                ['code' => 'coupon', 'message' => translate('coupon limit is over')]
                            ]
                        ], 401);
                    }
                }

            } else {
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => translate('not found')]
                    ]
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }
}
