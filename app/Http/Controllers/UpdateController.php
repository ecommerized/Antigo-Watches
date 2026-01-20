<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\DeliveryChargeSetup;
use App\Models\LoginSetup;
use App\Models\User;
use App\Traits\ActivationClass;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use function App\Library\businessSettingInsertOrUpdate;

class UpdateController extends Controller
{
    use ActivationClass;

    public function update_software_index()
    {
        return view('update.update-software');
    }

    public function update_software(Request $request)
    {
        Helpers::setEnvironmentValue('SOFTWARE_ID', 'MzExNTc0NTQ=');
        Helpers::setEnvironmentValue('BUYER_USERNAME', $request['username']);
        Helpers::setEnvironmentValue('PURCHASE_CODE', $request['purchase_key']);
        Helpers::setEnvironmentValue('APP_MODE', 'live');
        Helpers::setEnvironmentValue('SOFTWARE_VERSION', '7.9');
        Helpers::setEnvironmentValue('APP_NAME', 'Hexacom');
        Helpers::setEnvironmentValue('APP_URL', URL::to('/'));

        if ($this->actch()) {
            return redirect(base64_decode('aHR0cHM6Ly82YW10ZWNoLmNvbS9zb2Z0d2FyZS1hY3RpdmF0aW9u'));
        }

        Artisan::call('migrate', ['--force' => true]);

        $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvier, $previousRouteServiceProvier);

        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('optimize:clear');

        if (!BusinessSetting::where(['key' => 'terms_and_conditions'])->first()) {
            BusinessSetting::insert([
                'key' => 'terms_and_conditions',
                'value' => ''
            ]);
        }
        if (!BusinessSetting::where(['key' => 'razor_pay'])->first()) {
            BusinessSetting::insert([
                'key' => 'razor_pay',
                'value' => '{"status":"0","razor_key":"","razor_secret":""}'
            ]);
        }
        if (!BusinessSetting::where(['key' => 'minimum_order_value'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'minimum_order_value'], [
                'value' => 1
            ]);
        }
        if (!BusinessSetting::where(['key' => 'point_per_currency'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'point_per_currency'], [
                'value' => 1
            ]);
        }
        if (!BusinessSetting::where(['key' => 'language'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'language'], [
                'value' => json_encode(["en"])
            ]);
        }
        if (!BusinessSetting::where(['key' => 'time_zone'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'time_zone'], [
                'value' => 'Pacific/Midway'
            ]);
        }
        if (!BusinessSetting::where(['key' => 'internal_point'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'internal_point'], [
                'value' => json_encode(['status' => 0])
            ]);
        }
        if (!BusinessSetting::where(['key' => 'privacy_policy'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'privacy_policy'], [
                'value' => ''
            ]);
        }
        if (!BusinessSetting::where(['key' => 'about_us'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'about_us'], [
                'value' => ''
            ]);
        }

        DB::table('business_settings')->updateOrInsert(['key' => 'phone_verification'], [
            'value' => 0
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'msg91_sms'], [
            'key' => 'msg91_sms',
            'value' => '{"status":0,"template_id":null,"authkey":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => '2factor_sms'], [
            'key' => '2factor_sms',
            'value' => '{"status":"0","api_key":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'nexmo_sms'], [
            'key' => 'nexmo_sms',
            'value' => '{"status":0,"api_key":null,"api_secret":null,"signature_secret":"","private_key":"","application_id":"","from":null,"otp_template":null}'
        ]);
        DB::table('business_settings')->updateOrInsert(['key' => 'twilio_sms'], [
            'key' => 'twilio_sms',
            'value' => '{"status":0,"sid":null,"token":null,"from":null,"otp_template":null}'
        ]);

        if (!BusinessSetting::where(['key' => 'pagination_limit'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'pagination_limit'], [
                'value' => 10
            ]);
        }
        if (!BusinessSetting::where(['key' => 'map_api_key'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'map_api_key'], [
                'value' => ''
            ]);
        }
        if (!BusinessSetting::where(['key' => 'play_store_config'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'play_store_config'], [
                'value' => '{"status":"","link":"","min_version":""}'
            ]);
        }
        if (!BusinessSetting::where(['key' => 'app_store_config'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'app_store_config'], [
                'value' => '{"status":"","link":"","min_version":""}'
            ]);
        }
        if (!BusinessSetting::where(['key' => 'delivery_management'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'delivery_management'], [
                'value' => json_encode([
                    'status' => 0,
                    'min_shipping_charge' => 0,
                    'shipping_per_km' => 0,
                ]),
            ]);
        }

        DB::table('branches')->insertOrIgnore([
            'id' => 1,
            'name' => 'Main Branch',
            'email' => '',
            'password' => '',
            'coverage' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if (!BusinessSetting::where(['key' => 'dm_self_registration'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'dm_self_registration'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'maximum_otp_hit'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maximum_otp_hit'], [
                'value' => 5
            ]);
        }

        if (!BusinessSetting::where(['key' => 'otp_resend_time'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'otp_resend_time'], [
                'value' => 60
            ]);
        }

        if (!BusinessSetting::where(['key' => 'temporary_block_time'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'temporary_block_time'], [
                'value' => 120
            ]);
        }

        if (!BusinessSetting::where(['key' => 'maximum_login_hit'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maximum_login_hit'], [
                'value' => 5
            ]);
        }

        if (!BusinessSetting::where(['key' => 'temporary_login_block_time'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'temporary_login_block_time'], [
                'value' => 120
            ]);
        }

        if (!BusinessSetting::where(['key' => 'cookies'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'cookies'], [
                'value' => '{"status":"1","text":"Allow Cookies for this site"}'
            ]);
        }

        $mail_config = \App\CentralLogics\Helpers::get_business_settings('mail_config');
        BusinessSetting::where(['key' => 'mail_config'])->update([
            'value' => json_encode([
                "status" => 0,
                "name" => $mail_config['name'],
                "host" => $mail_config['host'],
                "driver" => $mail_config['driver'],
                "port" => $mail_config['port'],
                "username" => $mail_config['username'],
                "email_id" => $mail_config['email_id'],
                "encryption" => $mail_config['encryption'],
                "password" => $mail_config['password']
            ]),
        ]);

        if (!BusinessSetting::where(['key' => 'fav_icon'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'fav_icon'], [
                'value' => ''
            ]);
        }

        $api_key = Helpers::get_business_settings('map_api_key');
        if (!BusinessSetting::where(['key' => 'map_api_server_key'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'map_api_server_key'], [
                'value' => $api_key
            ]);
        }

        if (!BusinessSetting::where(['key' => 'google_social_login'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'google_social_login'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'facebook_social_login'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'facebook_social_login'], [
                'value' => 1
            ]);
        }

        if (!BusinessSetting::where(['key' => 'whatsapp'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'whatsapp'], [
                'value' => '{"status":"0","number":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'telegram'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'telegram'], [
                'value' => '{"status":"0","user_name":""}'
            ]);
        }

        if (!BusinessSetting::where(['key' => 'messenger'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'messenger'], [
                'value' => '{"status":"0","user_name":""}'
            ]);
        }

        //v7.1
        //new database table
        if (!Schema::hasTable('addon_settings')) {
            $sql = File::get(base_path($request['path'] . 'database/addon_settings.sql'));
            DB::unprepared($sql);
            $this->set_payment_data();
            $this->set_sms_data();
        }

        if (!Schema::hasTable('payment_requests')) {
            $sql = File::get(base_path($request['path'] . 'database/payment_requests.sql'));
            DB::unprepared($sql);
        }

        if (!BusinessSetting::where(['key' => 'app_logo'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'app_logo'], [
                'value' => ''
            ]);
        }

        //version 7.3
        if (!BusinessSetting::where(['key' => 'push_notification_service_file_content'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'push_notification_service_file_content'], [
                'value' => ''
            ]);
        }

        //version 7.4
        $emailVerification = (integer)Helpers::get_business_settings('email_verification') ?? 0;
        $phoneVerification = (integer)Helpers::get_business_settings('phone_verification') ?? 0;

        if (!LoginSetup::where('key', 'email_verification')->exists()) {
            LoginSetup::create([
                'key' => 'email_verification',
                'value' => $emailVerification
            ]);
        }

        if (!LoginSetup::where('key', 'phone_verification')->exists()) {
            LoginSetup::create([
                'key' => 'phone_verification',
                'value' => $phoneVerification
            ]);
        }

        if (!LoginSetup::where('key', 'login_options')->exists()) {
            LoginSetup::create([
                'key' => 'login_options',
                'value' => json_encode([
                    'manual_login' => 1,
                    'otp_login' => 0,
                    'social_media_login' => 0
                ]),
            ]);
        }
        if (!LoginSetup::where('key', 'social_media_for_login')->exists()) {
            LoginSetup::create([
                'key' => 'social_media_for_login',
                'value' => json_encode([
                    'google' => 0,
                    'facebook' => 0,
                    'apple' => 0
                ]),
            ]);
        }

        if (!BusinessSetting::where(['key' => 'maintenance_system_setup'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maintenance_system_setup'], [
                'value' => json_encode([])
            ]);
        }
        if (!BusinessSetting::where(['key' => 'maintenance_duration_setup'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maintenance_duration_setup'], [
                'value' => json_encode([
                    'maintenance_duration' => "until_change",
                    'start_date' => null,
                    'end_date' => null,
                ]),
            ]);
        }
        if (!BusinessSetting::where(['key' => 'maintenance_message_setup'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'maintenance_message_setup'], [
                'value' => json_encode([
                    'business_number' => 1,
                    'business_email' => 1,
                    'maintenance_message' => "We are Cooking Up Something Special!",
                    'message_body' => "Our system is currently undergoing maintenance to bring you an even tastier experience. Hang tight while we make the dishes.",
                ]),
            ]);
        }

        if (!BusinessSetting::where(['key' => 'firebase_otp_verification'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'firebase_otp_verification'], [
                'value' => json_encode([
                    'status' => 0,
                    'web_api_key' => '',
                ]),
            ]);
        }

        if (!BusinessSetting::where(['key' => 'google_map_status'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'google_map_status'], [
                'value' => 1
            ]);
        }

        $recaptcha = Helpers::get_business_settings('recaptcha');
        if (isset($recaptcha) && isset($recaptcha['status'], $recaptcha['site_key'], $recaptcha['secret_key']) && $recaptcha['status'] == 1) {
            DB::table('business_settings')->updateOrInsert(['key' => 'recaptcha'], [
                'value' => json_encode([
                    'status' => 0,
                    'site_key' => $recaptcha['site_key'],
                    'secret_key' => $recaptcha['secret_key'],
                ]),
            ]);
        }

        $fixedDeliveryCharge = Helpers::get_business_settings('delivery_charge');
        $branchIds = Branch::pluck('id')->toArray();
        $existingBranchIds = DeliveryChargeSetup::pluck('branch_id')->toArray();

        foreach ($branchIds as $branchId) {
            if (!in_array($branchId, $existingBranchIds)) {
                DeliveryChargeSetup::updateOrCreate([
                    'branch_id' => $branchId
                ], [
                    'delivery_charge_type' => 'fixed',
                    'fixed_delivery_charge' => $fixedDeliveryCharge,
                ]);
            }
        }

        if (!BusinessSetting::where(['key' => 'apple_login'])->first()) {
            DB::table('business_settings')->updateOrInsert(['key' => 'apple_login'], [
                'value' => '{"status":0, "login_medium":"apple","client_id":"","client_secret":"","team_id":"","key_id":"","service_file":"","redirect_url":""}'
            ]);
        }

        //version 7.6
        $this->updatePaymobSettings();
//        version 7.7

        $this->updateVersion7_7();

        return redirect('/');
    }

    private function set_payment_data()
    {
        try {
            $gateway = [
                'ssl_commerz_payment',
                'razor_pay',
                'paypal',
                'stripe',
                'senang_pay',
                'paystack',
                'bkash',
                'paymob',
                'flutterwave',
                'mercadopago',
            ];


            $data = BusinessSetting::whereIn('key', $gateway)->pluck('value', 'key')->toArray();

            foreach ($data as $key => $value) {
                $gateway = $key;
                if ($key == 'ssl_commerz_payment') {
                    $gateway = 'ssl_commerz';
                }
                if ($key == 'paymob') {
                    $gateway = 'paymob_accept';
                }

                $decoded_value = json_decode($value, true);
                $data = ['gateway' => $gateway,
                    'mode' => isset($decoded_value['status']) == 1 ? 'live' : 'test'
                ];

                $additional_data = [];

                if ($gateway == 'ssl_commerz') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'store_id' => $decoded_value['store_id'],
                        'store_password' => $decoded_value['store_password'],
                    ];
                } elseif ($gateway == 'paypal') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'client_id' => $decoded_value['paypal_client_id'],
                        'client_secret' => $decoded_value['paypal_secret'],
                    ];
                } elseif ($gateway == 'stripe') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'api_key' => $decoded_value['api_key'],
                        'published_key' => $decoded_value['published_key'],
                    ];
                } elseif ($gateway == 'razor_pay') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'api_key' => $decoded_value['razor_key'],
                        'api_secret' => $decoded_value['razor_secret'],
                    ];
                } elseif ($gateway == 'senang_pay') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'callback_url' => null,
                        'secret_key' => $decoded_value['secret_key'],
                        'merchant_id' => $decoded_value['merchant_id'],
                    ];
                } elseif ($gateway == 'paystack') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'callback_url' => $decoded_value['paymentUrl'],
                        'public_key' => $decoded_value['publicKey'],
                        'secret_key' => $decoded_value['secretKey'],
                        'merchant_email' => $decoded_value['merchantEmail'],
                    ];
                } elseif ($gateway == 'paymob_accept') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'callback_url' => null,
                        'api_key' => $decoded_value['api_key'],
                        'iframe_id' => $decoded_value['iframe_id'],
                        'integration_id' => $decoded_value['integration_id'],
                        'hmac' => $decoded_value['hmac'],
                    ];
                } elseif ($gateway == 'mercadopago') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'access_token' => $decoded_value['access_token'],
                        'public_key' => $decoded_value['public_key'],
                    ];
                } elseif ($gateway == 'flutterwave') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'secret_key' => $decoded_value['secret_key'],
                        'public_key' => $decoded_value['public_key'],
                        'hash' => $decoded_value['hash'],
                    ];
                } elseif ($gateway == 'bkash') {
                    $additional_data = [
                        'status' => $decoded_value['status'],
                        'app_key' => $decoded_value['api_key'],
                        'app_secret' => $decoded_value['api_secret'],
                        'username' => $decoded_value['username'],
                        'password' => $decoded_value['password'],
                    ];
                }

                $credentials = json_encode(array_merge($data, $additional_data));

                $payment_additional_data = ['gateway_title' => ucfirst(str_replace('_', ' ', $gateway)),
                    'gateway_image' => null];

                DB::table('addon_settings')->updateOrInsert(['key_name' => $gateway, 'settings_type' => 'payment_config'], [
                    'key_name' => $gateway,
                    'live_values' => $credentials,
                    'test_values' => $credentials,
                    'settings_type' => 'payment_config',
                    'mode' => isset($decoded_value['status']) == 1 ? 'live' : 'test',
                    'is_active' => isset($decoded_value['status']) == 1 ? 1 : 0,
                    'additional_data' => json_encode($payment_additional_data),
                ]);
            }
        } catch (\Exception $exception) {
            Toastr::error('Database import failed! try again');
            return true;
        }
        return true;
    }

    private function set_sms_data()
    {
        try {
            $sms_gateway = ['twilio_sms', 'nexmo_sms', 'msg91_sms', '2factor_sms'];

            $data = BusinessSetting::whereIn('key', $sms_gateway)->pluck('value', 'key')->toArray();
            foreach ($data as $key => $value) {
                $decoded_value = json_decode($value, true);

                if ($key == 'twilio_sms') {
                    $sms_gateway = 'twilio';
                    $additional_data = [
                        'status' => data_get($decoded_value, 'status', null),
                        'sid' => data_get($decoded_value, 'sid', null),
                        'messaging_service_sid' => data_get($decoded_value, 'messaging_service_id', null),
                        'token' => data_get($decoded_value, 'token', null),
                        'from' => data_get($decoded_value, 'from', null),
                        'otp_template' => data_get($decoded_value, 'otp_template', null),
                    ];
                } elseif ($key == 'nexmo_sms') {
                    $sms_gateway = 'nexmo';
                    $additional_data = [
                        'status' => data_get($decoded_value, 'status', null),
                        'api_key' => data_get($decoded_value, 'api_key', null),
                        'api_secret' => data_get($decoded_value, 'api_secret', null),
                        'token' => data_get($decoded_value, 'token', null),
                        'from' => data_get($decoded_value, 'from', null),
                        'otp_template' => data_get($decoded_value, 'otp_template', null),
                    ];
                } elseif ($key == '2factor_sms') {
                    $sms_gateway = '2factor';
                    $additional_data = [
                        'status' => data_get($decoded_value, 'status', null),
                        'api_key' => data_get($decoded_value, 'api_key', null),
                    ];
                } elseif ($key == 'msg91_sms') {
                    $sms_gateway = 'msg91';
                    $additional_data = [
                        'status' => data_get($decoded_value, 'status', null),
                        'template_id' => data_get($decoded_value, 'template_id', null),
                        'auth_key' => data_get($decoded_value, 'authkey', null),
                    ];
                }

                $data = [
                    'gateway' => $sms_gateway,
                    'mode' => isset($decoded_value['status']) == 1 ? 'live' : 'test'
                ];
                $credentials = json_encode(array_merge($data, $additional_data));

                DB::table('addon_settings')->updateOrInsert(['key_name' => $sms_gateway, 'settings_type' => 'sms_config'], [
                    'key_name' => $sms_gateway,
                    'live_values' => $credentials,
                    'test_values' => $credentials,
                    'settings_type' => 'sms_config',
                    'mode' => isset($decoded_value['status']) == 1 ? 'live' : 'test',
                    'is_active' => isset($decoded_value['status']) == 1 ? 1 : 0,
                ]);
            }
        } catch (\Exception $exception) {
            Toastr::error('Database import failed! try again');
            return true;
        }
        return true;
    }

    private function updatePaymobSettings()
    {
        try {
            // Find the paymob_accept key in addon_settings table
            $addonSetting = DB::table('addon_settings')
                ->where('key_name', 'paymob_accept')
                ->first();

            if (!$addonSetting) {
                return response()->json(['message' => 'Paymob settings not found'], 404);
            }

            // Decode the live_values JSON
            $liveValues = json_decode($addonSetting->live_values, true);

            // Remove iframe_id if it exists
            if (isset($liveValues['iframe_id'])) {
                unset($liveValues['iframe_id']);
            }

            // Ensure public_key exists
            if (!isset($liveValues['public_key'])) {
                $liveValues['public_key'] = '';
            }

            // Ensure secret_key exists
            if (!isset($liveValues['secret_key'])) {
                $liveValues['secret_key'] = '';
            }

            // Update the addon_settings table
            DB::table('addon_settings')
                ->where('key_name', 'paymob_accept')
                ->update([
                    'live_values' => json_encode($liveValues),
                    'updated_at' => now(),
                ]);

        } catch (\Exception $e) {
            // Handle exceptions
        }

        return true;
    }

    private function updateVersion7_7()
    {
        foreach (User::doesntHave('userAccount')->cursor() as $user) {
            $user->userAccount()->create([
                'wallet_balance' => 0,
            ]);
        }
        if (!BusinessSetting::where(['key' => 'customer_setup_wallet_earning'])->first()) {
            BusinessSetting::insert([
                'key' => 'customer_setup_wallet_earning',
                'value' => json_encode([
                    'status' => 0,
                    'order_wise_earning_percentage' => 0,
                ]),
            ]);
        }
    }
}
