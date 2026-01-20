<?php
//
//namespace App\Http\Controllers;
//
//use App\Models\PaymentRequest;
//use App\Models\User;
//use App\Traits\Processor;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Redirect;
//use Illuminate\Support\Facades\Validator;
//
//class PaymobController extends Controller
//{
//    use Processor;
//
//    private $config_values;
//
//    private PaymentRequest $payment;
//    private $user;
//
//    public function __construct(PaymentRequest $payment, User $user)
//    {
//        $config = $this->payment_config('paymob_accept', 'payment_config');
//        if (!is_null($config) && $config->mode == 'live') {
//            $this->config_values = json_decode($config->live_values);
//        } elseif (!is_null($config) && $config->mode == 'test') {
//            $this->config_values = json_decode($config->test_values);
//        }
//        $this->payment = $payment;
//        $this->user = $user;
//    }
//
//    protected function cURL($url, $json)
//    {
//        // Create curl resource
//        $ch = curl_init($url);
//
//        // Request headers
//        $headers = array();
//        $headers[] = 'Content-Type: application/json';
//
//        // Return the transfer as a string
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//
//        // $output contains the output string
//        $output = curl_exec($ch);
//
//        // Close curl resource to free up system resources
//        curl_close($ch);
//        return json_decode($output);
//    }
//
//    protected function GETcURL($url)
//    {
//        // Create curl resource
//        $ch = curl_init($url);
//
//        // Request headers
//        $headers = array();
//        $headers[] = 'Content-Type: application/json';
//
//        // Return the transfer as a string
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//
//        // $output contains the output string
//        $output = curl_exec($ch);
//
//        // Close curl resource to free up system resources
//        curl_close($ch);
//        return json_decode($output);
//    }
//
//    public function credit(Request $request)
//    {
//        $validator = Validator::make($request->all(), [
//            'payment_id' => 'required|uuid'
//        ]);
//
//        if ($validator->fails()) {
//            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
//        }
//
//        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
//        if (!isset($data)) {
//            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
//        }
//
//        session()->put('payment_id', $data->id);
//
//        if ($data['additional_data'] != null) {
//            $business = json_decode($data['additional_data']);
//            $business_name = $business->business_name ?? "my_business";
//        } else {
//            $business_name = "my_business";
//        }
//
//        $payer = json_decode($data['payer_information']);
//
//        try {
//            $token = $this->getToken();
//            $order = $this->createOrder($token, $data, $business_name);
//            $paymentToken = $this->getPaymentToken($order, $token, $data, $payer);
//        } catch (\Exception $exception) {
//            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_404), 200);
//        }
//        return Redirect::away('https://accept.paymobsolutions.com/api/acceptance/iframes/' . $this->config_values->iframe_id . '?payment_token=' . $paymentToken);
//    }
//
//    public function getToken()
//    {
//        $response = $this->cURL(
//            'https://accept.paymob.com/api/auth/tokens',
//            ['api_key' => $this->config_values->api_key]
//        );
//
//        return $response->token;
//    }
//
//    public function createOrder($token, $payment_data, $business_name)
//    {
//        $items[] = [
//            'name' => $business_name,
//            'amount_cents' => round($payment_data->payment_amount * 100),
//            'description' => 'payment ID :' . $payment_data->id,
//            'quantity' => 1
//        ];
//
//        $data = [
//            "auth_token" => $token,
//            "delivery_needed" => "false",
//            "amount_cents" => round($payment_data->payment_amount * 100),
//            "currency" => $payment_data->currency_code,
//            "items" => $items,
//
//        ];
//        $response = $this->cURL(
//            'https://accept.paymob.com/api/ecommerce/orders',
//            $data
//        );
//
//        return $response;
//    }
//
//    public function getPaymentToken($order, $token, $payment_data, $payer)
//    {
//        $value = $payment_data->payment_amount;
//        $billingData = [
//            "apartment" => "N/A",
//            "email" => $payer->email,
//            "floor" => "N/A",
//            "first_name" => $payer->name,
//            "street" => "N/A",
//            "building" => "N/A",
//            "phone_number" => $payer->phone ?? "N/A",
//            "shipping_method" => "PKG",
//            "postal_code" => "N/A",
//            "city" => "N/A",
//            "country" => "N/A",
//            "last_name" => $payer->name,
//            "state" => "N/A",
//        ];
//
//        $data = [
//            "auth_token" => $token,
//            "amount_cents" => round($value * 100),
//            "expiration" => 3600,
//            "order_id" => $order->id,
//            "billing_data" => $billingData,
//            "currency" => $payment_data->currency_code,
//            "integration_id" => $this->config_values->integration_id
//        ];
//
//        $response = $this->cURL(
//            'https://accept.paymob.com/api/acceptance/payment_keys',
//            $data
//        );
//
//        return $response->token;
//    }
//
//    public function callback(Request $request)
//    {
//        $data = $request->all();
//        ksort($data);
//        $hmac = $data['hmac'];
//        $array = [
//            'amount_cents',
//            'created_at',
//            'currency',
//            'error_occured',
//            'has_parent_transaction',
//            'id',
//            'integration_id',
//            'is_3d_secure',
//            'is_auth',
//            'is_capture',
//            'is_refunded',
//            'is_standalone_payment',
//            'is_voided',
//            'order',
//            'owner',
//            'pending',
//            'source_data_pan',
//            'source_data_sub_type',
//            'source_data_type',
//            'success',
//        ];
//        $connectedString = '';
//        foreach ($data as $key => $element) {
//            if (in_array($key, $array)) {
//                $connectedString .= $element;
//            }
//        }
//        $secret = $this->config_values->hmac;
//        $hased = hash_hmac('sha512', $connectedString, $secret);
//
//        if ($hased == $hmac && $data['success'] === "true") {
//
//            $this->payment::where(['id' => session('payment_id')])->update([
//                'payment_method' => 'paymob_accept',
//                'is_paid' => 1,
//                'transaction_id' => session('payment_id'),
//            ]);
//
//            $payment_data = $this->payment::where(['id' => session('payment_id')])->first();
//
//            if (isset($payment_data) && function_exists($payment_data->success_hook)) {
//                call_user_func($payment_data->success_hook, $payment_data);
//            }
//            return $this->payment_response($payment_data,'success');
//        }
//        $payment_data = $this->payment::where(['id' => session('payment_id')])->first();
//        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
//            call_user_func($payment_data->failure_hook, $payment_data);
//        }
//        return $this->payment_response($payment_data,'fail');
//    }
//}


namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Models\User;
use App\Traits\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class PaymobController extends Controller
{
    use Processor;

    private mixed $config_values;
    private PaymentRequest $payment;
    private User $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('paymob_accept', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values, true);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values, true);
        }
        $this->payment = $payment;
        $this->user = $user;
    }

    protected function cURL($url, $json)
    {
        $ch = curl_init($url);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }

    public function credit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);
        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }
        $payment_data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($payment_data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }
        session()->put('payment_id', $payment_data->id);
        if ($payment_data['additional_data'] != null) {
            $business = json_decode($payment_data['additional_data']);
            $business_name = $business->business_name ?? "my_business";
        } else {
            $business_name = "my_business";
        }
        $payer = json_decode($payment_data['payer_information']);
        $url = 'https://ksa.paymob.com/v1/intention/';
        $config = $this->config_values;
        $token = $config['secret_key']; //secret key
        // Data for the request
        $integration_id = (int)$config['integration_id'];
        $data = [
            'amount' => round($payment_data->payment_amount * 100),
            'currency' => $payment_data->currency_code,
            'payment_methods' => [$integration_id], //integration id will be integer
            'items' => [
                [
                    'name' => 'payable amount',
                    'amount' => round($payment_data->payment_amount * 100),
                    'description' => 'payable amount',
                    'quantity' => 1,
                ]
            ],
            'billing_data' => [
                "apartment" => "N/A",
                "email" => !empty($payer->email) ? $payer->email : 'test@gmail.com',
                "floor" => "N/A",
                "first_name" => !empty($payer->name) ? $payer->name : "rashed",
                "street" => "N/A",
                "building" => "N/A",
                "phone_number" => !empty($payer->phone) ? $payer->phone : "0182780000000",
                "shipping_method" => "PKG",
                "postal_code" => "N/A",
                "city" => "N/A",
                "country" => "N/A",
                "last_name" => !empty($payer->name) ? $payer->name : "rashed",
                "state" => "N/A",
            ],
            'special_reference' => time(),
            'customer' => [
                'first_name' => !empty($payer->name) ? $payer->name : "rashed",
                'last_name' => !empty($payer->name) ? $payer->name : "rashed",
                'email' => !empty($payer->email) ? $payer->email : 'test@gmail.com',
                'extras' => [
                    're' => '22',
                ],
            ],
            'extras' => [
                'ee' => 22,
            ],
            "redirection_url" => route('paymob.callback'),
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Token ' . $token,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $result = json_decode($response, true);
        if (!isset($result['client_secret'])) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }
        $secret_key = $result['client_secret'];
        curl_close($ch);
        $publicKey = $config['public_key'];
        $urlRedirect = "https://ksa.paymob.com/unifiedcheckout/?publicKey=$publicKey&clientSecret=$secret_key";
        return redirect()->to($urlRedirect);
    }

    public function getToken()
    {
        $response = $this->cURL(
            'https://ksa.paymob.com/api/auth/tokens',
            ['api_key' => $this->config_values->api_key]
        );
        return $response->token;
    }

    public function createOrder($token, $payment_data, $business_name)
    {
        $items[] = [
            'name' => $business_name,
            'amount_cents' => round($payment_data->payment_amount * 100),
            'description' => 'payment ID :' . $payment_data->id,
            'quantity' => 1
        ];
        $data = [
            "auth_token" => $token,
            "delivery_needed" => "false",
            "amount_cents" => round($payment_data->payment_amount * 100),
            "currency" => $payment_data->currency_code,
            "items" => $items,
        ];
        $response = $this->cURL(
            'https://ksa.paymob.com/api/ecommerce/orders',
            $data
        );
        return $response;
    }

    public function getPaymentToken($order, $token, $payment_data, $payer)
    {
        $value = $payment_data->payment_amount;
        $billingData = [
            "apartment" => "N/A",
            "email" => !empty($payer->email) ? $payer->email : 'test@gmail.com',
            "floor" => "N/A",
            "first_name" => !empty($payer->name) ? $payer->name : "rashed",
            "street" => "N/A",
            "building" => "N/A",
            "phone_number" => !empty($payer->phone) ? $payer->phone : "0182780000000",
            "shipping_method" => "PKG",
            "postal_code" => "N/A",
            "city" => "N/A",
            "country" => "N/A",
            "last_name" => !empty($payer->name) ? $payer->name : "rashed",
            "state" => "N/A",
        ];
        $data = [
            "auth_token" => $token,
            "amount_cents" => round($value * 100),
            "expiration" => 3600,
            "order_id" => $order->id,
            "billing_data" => $billingData,
            "currency" => $payment_data->currency_code,
            "integration_id" => $this->config_values->integration_id
        ];
        $response = $this->cURL(
            'https://ksa.paymob.com/api/acceptance/payment_keys',
            $data
        );
        return $response->token;
    }

    public function callback(Request $request)
    {
        $data = $request->all();
        ksort($data);
        $hmac = $data['hmac'];
        $array = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order',
            'owner',
            'pending',
            'source_data_pan',
            'source_data_sub_type',
            'source_data_type',
            'success',
        ];
        $connectedString = '';
        foreach ($data as $key => $element) {
            if (in_array($key, $array)) {
                $connectedString .= $element;
            }
        }
        $secret = $this->config_values->hmac;
        $hased = hash_hmac('sha512', $connectedString, $secret);
        if ($hased == $hmac && $data['success'] === "true") {
            $this->payment::where(['id' => session('payment_id')])->update([
                'payment_method' => 'paymob_accept',
                'is_paid' => 1,
                'transaction_id' => session('payment_id'),
            ]);
            $payment_data = $this->payment::where(['id' => session('payment_id')])->first();
            if (isset($payment_data) && function_exists($payment_data->success_hook)) {
                call_user_func($payment_data->success_hook, $payment_data);
            }
            return $this->payment_response($payment_data, 'success');
        }
        $payment_data = $this->payment::where(['id' => session('payment_id')])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }
}
