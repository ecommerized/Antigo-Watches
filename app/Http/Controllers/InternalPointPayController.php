<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InternalPointPayController extends Controller
{
    public function payment(Request $request)
    {
        $order = Order::find($request['order_id']);
        $user = User::find($order['user_id']);
        $value = BusinessSetting::where(['key' => 'point_per_currency'])->first()->value;
        $order_point = $order['order_amount'] * $value;

        if ($order['payment_status'] == 'unpaid') {
            if ($order['order_amount'] <= $order_point) {
                User::where(['id' => $user['id']])->decrement('point', $order_point);
                $tr_ref = 'payment_' . Str::random('15');
                DB::table('orders')
                    ->where('id', $order['id'])
                    ->update([
                        'payment_method' => 'internal_point',
                        'transaction_reference' => $tr_ref,
                        'order_status' => 'confirmed',
                        'payment_status' => 'paid',
                        'updated_at' => now(),
                    ]);
                DB::table('point_transitions')->insert([
                    'user_id' => $user['id'],
                    'description' => 'paid for order ID : ' . $order['id'] . '.',
                    'type' => 'point_out',
                    'amount' => $order_point,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return redirect('payment-success');
            }
        }

        return redirect('payment-fail');
    }
}
