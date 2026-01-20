<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\SocialMedia;
use App\Models\LoginSetup;
use App\Models\Setting;
use App\Traits\HelperTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConfigController extends Controller
{
    use HelperTrait;

    public function __construct(
        private LoginSetup $loginSetup)
    {}

    public function configuration(): \Illuminate\Http\JsonResponse
    {
        $publishedStatus = 0;
        $paymentPublishedStatus = config('get_payment_publish_status');
        if (isset($paymentPublishedStatus[0]['is_published'])) {
            $publishedStatus = $paymentPublishedStatus[0]['is_published'];
        }
        $activeAddonPaymentList = $publishedStatus == 1 ? $this->getPaymentMethods() : $this->getDefaultPaymentMethods();
        $digitalPaymentStatusValue = Helpers::get_business_settings('digital_payment');

        $currencySymbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        $cod = Helpers::get_business_settings('cash_on_delivery');
        $dp = Helpers::get_business_settings('digital_payment');

        $deliveryManConfig = Helpers::get_business_settings('delivery_management');
        $deliveryManagement = array(
            "status" => (int)$deliveryManConfig['status'],
            "min_shipping_charge" => (float)$deliveryManConfig['min_shipping_charge'],
            "shipping_per_km" => (float)$deliveryManConfig['shipping_per_km'],
        );

        $cookiesConfig = Helpers::get_business_settings('cookies');
        $cookiesManagement = array(
            "status" => (int)$cookiesConfig['status'],
            "text" => $cookiesConfig['text'],
        );

        $customerSetupWalletEarningConfig = Helpers::get_business_settings('customer_setup_wallet_earning');
        $customerSetupWalletEarning = array(
            "status" => (int)$customerSetupWalletEarningConfig['status'],
            "order_wise_earning_percentage" => $customerSetupWalletEarningConfig['order_wise_earning_percentage'],
        );


        $advanceMaintenanceMode = $this->checkMaintenanceMode();

        $emailVerification = (int) Helpers::get_login_settings('email_verification');
        $phoneVerification = (int) Helpers::get_login_settings('phone_verification');

        $firebaseOTPVerification = Helpers::get_business_settings('firebase_otp_verification');
        $firebaseOTPVerificationStatus = (integer)($firebaseOTPVerification ? $firebaseOTPVerification['status'] : 0);


        $status = 0;
        if ($emailVerification == 1) {
            $status = 1;
        } elseif ($phoneVerification == 1) {
            $status = 1;
        }

        $customerVerification = [
            'status' => $status,
            'phone'=> $phoneVerification,
            'email'=> $emailVerification,
            'firebase'=> (int) $firebaseOTPVerificationStatus,
        ];

        $loginOptions = Helpers::get_login_settings('login_options');
        $socialMediaLoginOptions = Helpers::get_login_settings('social_media_for_login');

        $customerLogin = [
            'login_option' => $loginOptions,
            'social_media_login_options' => $socialMediaLoginOptions
        ];

        $emailConfig = Helpers::get_business_settings('mail_config');
        $dataValues = Setting::where('settings_type', 'sms_config')->get();
        $activeCount = 0;
        foreach ($dataValues as $gateway) {
            $status = isset($gateway->live_values['status']) ? (int)$gateway->live_values['status'] : 0;
            if ($status == 1) {
                $activeCount++;
            }
        }

        $forgotPassword = [
            'firebase' => $firebaseOTPVerificationStatus,
            'phone' => $activeCount > 0 ? 1: 0,
            'email' => $emailConfig['status'] ?? 0
        ];

        $apple = Helpers::get_business_settings('apple_login');
        $appleLogin = array(
            'login_medium' => $apple['login_medium'],
            'client_id' => $apple['client_id']
        );

        return response()->json([
            'ecommerce_name' => Helpers::get_business_settings('restaurant_name'),
            'ecommerce_logo' => Helpers::get_business_settings('logo'),
            'app_logo' => Helpers::get_business_settings('app_logo'),
            'ecommerce_address' => Helpers::get_business_settings('address'),
            'ecommerce_phone' => Helpers::get_business_settings('phone'),
            'ecommerce_email' => Helpers::get_business_settings('email_address'),
            'ecommerce_location_coverage' => Branch::where(['id' => 1])->first(['longitude', 'latitude', 'coverage']),
            'minimum_order_value' => (float) Helpers::get_business_settings('minimum_order_value'),
            'self_pickup' => (int) Helpers::get_business_settings('self_pickup'),
            'base_urls' => [
                'product_image_url' => asset('storage/app/public/product'),
                'customer_image_url' => asset('storage/app/public/profile'),
                'banner_image_url' => asset('storage/app/public/banner'),
                'category_image_url' => asset('storage/app/public/category'),
                'category_banner_image_url' => asset('storage/app/public/category/banner'),
                'review_image_url' => asset('storage/app/public/review'),
                'notification_image_url' => asset('storage/app/public/notification'),
                'ecommerce_image_url' => asset('storage/app/public/ecommerce'),
                'delivery_man_image_url' => asset('storage/app/public/delivery-man'),
                'chat_image_url' => asset('storage/app/public/conversation'),
                'flash_sale_image_url' => asset('storage/app/public/flash-sale'),
                'gateway_image_url' => asset('storage/app/public/payment_modules/gateway_image'),
            ],
            'currency_symbol' => $currencySymbol,
            'delivery_charge' => (float) Helpers::get_business_settings('delivery_charge'),
            'delivery_management' => $deliveryManagement,
            'cash_on_delivery' => $cod['status'] == 1 ? 'true' : 'false',
            'digital_payment' => $dp['status'] == 1 ? 'true' : 'false',
            'branches' => Branch::active()->get(['id', 'name', 'email', 'longitude', 'latitude', 'address', 'coverage', 'status']),
            'terms_and_conditions' => Helpers::get_business_settings('terms_and_conditions'),
            'privacy_policy' => Helpers::get_business_settings('privacy_policy'),
            'about_us' => Helpers::get_business_settings('about_us'),
            'email_verification' => (boolean) $emailVerification,
            'phone_verification' => (boolean) $phoneVerification,
            'currency_symbol_position' => Helpers::get_business_settings('currency_symbol_position') ?? 'right',
            'maintenance_mode' => (boolean)Helpers::get_business_settings('maintenance_mode') ?? 0,
            'country' => Helpers::get_business_settings('country') ?? 'BD',
            'play_store_config' => [
                "status" => (boolean)Helpers::get_business_settings('play_store_config')['status'],
                "link" => Helpers::get_business_settings('play_store_config')['link'],
                "min_version" => Helpers::get_business_settings('play_store_config')['min_version'],
            ],
            'app_store_config' => [
                "status" => (boolean) Helpers::get_business_settings('app_store_config')['status'],
                "link" => Helpers::get_business_settings('app_store_config')['link'],
                "min_version" => Helpers::get_business_settings('app_store_config')['min_version'],
            ],
            'social_media_link' => SocialMedia::orderBy('id', 'desc')->active()->get(),
            'software_version' => (string)env('SOFTWARE_VERSION') ?? null,
            'footer_text' => Helpers::get_business_settings('footer_text'),
            'dm_self_registration' => (int) Helpers::get_business_settings('dm_self_registration'),
            'otp_resend_time' => Helpers::get_business_settings('otp_resend_time') ?? 60,
            'cookies_management' => $cookiesManagement,
            'customer_setup_wallet_earning' => $customerSetupWalletEarning,
            'social_login' => [
                'google' => (integer) Helpers::get_business_settings('google_social_login'),
                'facebook' => (integer) Helpers::get_business_settings('facebook_social_login'),
            ],
            'whatsapp' => Helpers::get_business_settings('whatsapp'),
            'telegram' => Helpers::get_business_settings('telegram'),
            'messenger' => Helpers::get_business_settings('messenger'),
            'digital_payment_status' => (integer) $digitalPaymentStatusValue['status'],
            'active_payment_method_list' => (integer) $digitalPaymentStatusValue['status'] == 1 ? $activeAddonPaymentList : [],
            'advance_maintenance_mode' => $advanceMaintenanceMode,
            'google_map_status' => (integer) (Helpers::get_business_settings('google_map_status') ?? 0),
            'customer_verification' => $customerVerification,
            'customer_login' => $customerLogin,
            'guest_checkout' => (integer) (Helpers::get_business_settings('guest_checkout') ?? 0),
            'firebase_otp_verification_status' => $firebaseOTPVerificationStatus,
            'forgot_password' => $forgotPassword,
            'apple_login' => $appleLogin,
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_image_size' => uploadMaxFileSize('image')
        ]);
    }

    private function getPaymentMethods(): array
    {
        if (!Schema::hasTable('addon_settings')) {
            return [];
        }

        $methods = DB::table('addon_settings')->where('settings_type', 'payment_config')->get();
        $env = env('APP_ENV') == 'live' ? 'live' : 'test';
        $credentials = $env . '_values';

        $data = [];
        foreach ($methods as $method) {
            $credentialsData = json_decode($method->$credentials);
            $additionalData = json_decode($method->additional_data);
            if ($credentialsData->status == 1) {
                $data[] = [
                    'gateway' => $method->key_name,
                    'gateway_title' => $additionalData?->gateway_title,
                    'gateway_image' => $additionalData?->gateway_image
                ];
            }
        }
        return $data;
    }

    private function getDefaultPaymentMethods(): array
    {
        if (!Schema::hasTable('addon_settings')) {
            return [];
        }

        $methods = DB::table('addon_settings')
            ->whereIn('settings_type', ['payment_config'])
            ->whereIn('key_name', ['ssl_commerz', 'paypal', 'stripe', 'razor_pay', 'senang_pay', 'paystack', 'paymob_accept', 'flutterwave', 'bkash', 'mercadopago'])
            ->get();

        $env = env('APP_ENV') == 'live' ? 'live' : 'test';
        $credentials = $env . '_values';

        $data = [];
        foreach ($methods as $method) {
            $credentialsData = json_decode($method->$credentials);
            $additionalData = json_decode($method->additional_data);
            if ($credentialsData->status == 1) {
                $data[] = [
                    'gateway' => $method->key_name,
                    'gateway_title' => $additionalData?->gateway_title,
                    'gateway_image' => $additionalData?->gateway_image
                ];
            }
        }
        return $data;
    }

    public function deliveryFree(Request $request): JsonResponse
    {
        $branches = Branch::with(['delivery_charge_setup', 'delivery_charge_by_area'])
            ->active()
            ->get(['id', 'name', 'status']);

        foreach ($branches as $branch){
            if (!empty($branch->delivery_charge_setup) && $branch->delivery_charge_setup->delivery_charge_type == 'distance') {
                unset($branch->delivery_charge_by_area);
                $branch->delivery_charge_by_area = [];
            }
        }

        return response()->json($branches);
    }
}
