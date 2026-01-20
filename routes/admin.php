<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\System\AddonController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\BusinessSettingsController;
use App\Http\Controllers\Admin\DatabaseSettingsController;
use App\Http\Controllers\Admin\LocationSettingsController;
use App\Http\Controllers\Admin\SMSModuleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DeliveryManController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\POSController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewsController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\LoginSetupController;
use App\Http\Controllers\Admin\DeliveryChargeSetupController;

Route::group(['as' => 'admin.'], function () {
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/code/captcha/{tmp}', [LoginController::class, 'captcha'])->name('default-captcha');
        Route::get('login', [LoginController::class, 'login'])->name('login');
        Route::post('login', [LoginController::class, 'submit'])->middleware('actch');
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
    });

    Route::group(['middleware' => ['admin']], function () {
//        Route::get('/fcm/{id}', [SystemController::class, 'fcm'])->name('dashboard');
        Route::get('/', [SystemController::class, 'dashboard'])->name('dashboard');
        Route::post('order-stats', [SystemController::class, 'orderStats'])->name('order-stats');
        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settingsUpdate']);
        Route::post('settings-password', [SystemController::class, 'settingsPasswordUpdate'])->name('settings-password');
        Route::get('/get-restaurant-data', [SystemController::class, 'restaurantData'])->name('get-restaurant-data');
        Route::get('dashboard/earning-statistics', [SystemController::class, 'getEarningStatitics'])->name('dashboard.earning-statistics');
        Route::get('ignore-check-order', [SystemController::class, 'ignoreCheckOrder'])->name('ignore-check-order');

        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
            Route::get('/', [POSController::class, 'index'])->name('index');
            Route::get('quick-view', [POSController::class, 'quickView'])->name('quick-view');
            Route::get('quick-view-modal-footer', [POSController::class, 'quickViewModalFooter'])->name('quick-view-modal-footer');
            Route::post('variant_price', [POSController::class, 'variantPrice'])->name('variant_price');
            Route::post('add-to-cart', [POSController::class,'addToCart'])->name('add-to-cart');
            Route::post('remove-from-cart', [POSController::class, 'removeFromCart'])->name('remove-from-cart');
            Route::post('cart-items', [POSController::class, 'cartItems'])->name('cart_items');
            Route::post('update-quantity', [POSController::class, 'updateQuantity'])->name('updateQuantity');
            Route::post('empty-cart', [POSController::class, 'emptyCart'])->name('emptyCart');
            Route::post('discount', [POSController::class, 'updateDiscount'])->name('discount');
            Route::get('delete-discount', [POSController::class, 'deleteDiscount'])->name('delete-discount');
            Route::get('customers', [POSController::class, 'getCustomers'])->name('customers');
            Route::post('order', [POSController::class, 'placeOrder'])->name('order');
            Route::get('orders', [POSController::class, 'orderList'])->name('orders');
            Route::get('order-details/{id}', [POSController::class, 'orderDetails'])->name('order-details');
            Route::get('invoice/{id}', [POSController::class, 'generateInvoice']);
            Route::any('store-keys', [POSController::class, 'storeKeys'])->name('store-keys');
            Route::post('customer-store', [POSController::class, 'customerStore'])->name('customer-store');
            Route::get('orders/export', [POSController::class, 'exportOrders'])->name('orders.export');
            Route::get('get-customer-coupon-list', [POSController::class, 'getCustomerCouponList'])->name('get-customer-coupon-list');
            Route::post('apply-coupon', [POSController::class, 'applyCoupon'])->name('apply-coupon');
            Route::get('delete-coupon', [POSController::class, 'deleteCoupon'])->name('delete-coupon');
            Route::get('get-cart-payment-section', [POSController::class, 'getCartPaymentSection'])->name('get-cart-payment-section');
        });

        Route::group(['prefix' => 'banner', 'as' => 'banner.'], function () {
            Route::get('add-new', [BannerController::class, 'index'])->name('add-new');
            Route::post('store', [BannerController::class, 'store'])->name('store');
            Route::get('edit/{id}', [BannerController::class, 'edit'])->name('edit');
            Route::put('update/{id}', [BannerController::class, 'update'])->name('update');
            Route::get('list', [BannerController::class, 'list'])->name('list');
            Route::get('status/{id}/{status}', [BannerController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [BannerController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
            Route::get('add-new', [AttributeController::class, 'index'])->name('add-new');
            Route::post('store', [AttributeController::class, 'store'])->name('store');
            Route::get('edit/{id}', [AttributeController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [AttributeController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [AttributeController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'branch', 'as' => 'branch.'], function () {
            Route::get('add-new', [BranchController::class, 'index'])->name('add-new');
            Route::get('list', [BranchController::class, 'list'])->name('list');
            Route::post('store', [BranchController::class, 'store'])->name('store');
            Route::get('edit/{id}', [BranchController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [BranchController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [BranchController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [BranchController::class, 'status'])->name('status');
        });

        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.'], function () {
            Route::get('add', [DeliveryManController::class, 'index'])->name('add');
            Route::post('store', [DeliveryManController::class, 'store'])->name('store');
            Route::get('list', [DeliveryManController::class, 'list'])->name('list');
            Route::get('preview/{id}', [DeliveryManController::class, 'preview'])->name('preview');
            Route::get('edit/{id}', [DeliveryManController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [DeliveryManController::class, 'update'])->name('update');
            Route::delete('delete/{id}', [DeliveryManController::class, 'delete'])->name('delete');
            Route::post('search', [DeliveryManController::class, 'search'])->name('search');
            Route::get('pending/list', [DeliveryManController::class, 'pendingList'])->name('pending');
            Route::get('denied/list', [DeliveryManController::class, 'deniedList'])->name('denied');
            Route::get('update-application/{id}/{status}', [DeliveryManController::class, 'updateApplication'])->name('application');


            Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
                Route::get('list', [DeliveryManController::class, 'reviewsList'])->name('list');
            });
        });

        Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
            Route::get('add-new', [NotificationController::class, 'index'])->name('add-new');
            Route::post('store', [NotificationController::class, 'store'])->name('store');
            Route::get('edit/{id}', [NotificationController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [NotificationController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [NotificationController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [NotificationController::class, 'delete'])->name('delete');
        });

        Route::group(['prefix' => 'product', 'as' => 'product.'], function () {
            Route::get('add-new', [ProductController::class, 'index'])->name('add-new');
            Route::post('variant-combination', [ProductController::class, 'variantCombination'])->name('variant-combination');
            Route::post('store', [ProductController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ProductController::class, 'update'])->name('update');
            Route::get('list', [ProductController::class, 'list'])->name('list');
            Route::delete('delete/{id}', [ProductController::class, 'delete'])->name('delete');
            Route::get('status/{id}/{status}', [ProductController::class, 'status'])->name('status');
            Route::post('search', [ProductController::class, 'search'])->name('search');
            Route::get('bulk-import', [ProductController::class, 'bulkImportIndex'])->name('bulk-import');
            Route::post('bulk-import', [ProductController::class, 'bulkImportProduct']);
            Route::get('bulk-export', [ProductController::class, 'bulkExportProduct'])->name('bulk-export');
            Route::get('view/{id}', [ProductController::class, 'view'])->name('view');
            Route::get('get-categories', [ProductController::class, 'getCategories'])->name('get-categories');
            Route::get('remove-image/{id}/{name}', [ProductController::class, 'removeImage'])->name('remove-image');
        });

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
            Route::get('status', [OrderController::class, 'status'])->name('status');
            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', [OrderController::class, 'addDeliveryman'])->name('add-delivery-man');
            Route::get('payment-status', [OrderController::class, 'paymentStatus'])->name('payment-status');
            Route::get('generate-invoice/{id}', [OrderController::class, 'generateInvoice'])->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', [OrderController::class, 'addPaymentReferenceCode'])->name('add-payment-ref-code');
            Route::get('branch-filter/{branch_id}', [OrderController::class, 'branchFilter'])->name('branch-filter');
            Route::get('export/{status}', [OrderController::class, 'exportOrders'])->name('export');
            Route::get('search-product', [OrderController::class, 'searchProduct'])->name('search-product');
            Route::post('update-product-list/{id}', [OrderController::class, 'updateProductList'])->name('update-product-list');
            Route::post('update-shipping/{id}', [OrderController::class, 'updateShipping'])->name('update-shipping');
        });

        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('add', [CategoryController::class, 'index'])->name('add');
            Route::get('add-sub-category', [CategoryController::class, 'subIndex'])->name('add-sub-category');
            Route::post('store', [CategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::post('store', [CategoryController::class, 'store'])->name('store');
            Route::get('status/{id}/{status}', [CategoryController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CategoryController::class, 'delete'])->name('delete');
            Route::post('search', [CategoryController::class, 'search'])->name('search');
            Route::get('featured/{id}/{featured}', [CategoryController::class, 'featured'])->name('featured');
        });

        Route::group(['prefix' => 'message', 'as' => 'message.'], function () {
            Route::get('list', [ConversationController::class, 'list'])->name('list');
            Route::post('update-fcm-token', [ConversationController::class, 'updateFcmToken'])->name('update_fcm_token');
            Route::get('get-firebase-config', [ConversationController::class, 'getFirebaseConfig'])->name('get_firebase_config');
            Route::get('get-conversations', [ConversationController::class, 'getConversations'])->name('get_conversations');
            Route::post('store/{user_id}', [ConversationController::class, 'store'])->name('store');
            Route::get('view/{user_id}', [ConversationController::class, 'view'])->name('view');
        });

        Route::group(['prefix' => 'reviews', 'as' => 'reviews.'], function () {
            Route::get('list', [ReviewsController::class, 'list'])->name('list');
        });

        Route::group(['prefix' => 'coupon', 'as' => 'coupon.'], function () {
            Route::get('add-new', [CouponController::class, 'index'])->name('add-new');
            Route::post('store', [CouponController::class, 'store'])->name('store');
            Route::get('update/{id}', [CouponController::class, 'edit'])->name('update');
            Route::post('update/{id}', [CouponController::class, 'update']);
            Route::get('status/{id}/{status}', [CouponController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [CouponController::class, 'delete'])->name('delete');
            Route::get('details', [CouponController::class, 'details'])->name('details');
        });

        Route::group(['prefix' => 'flash-sale', 'as' => 'flash-sale.'], function () {
            Route::get('index', [FlashSaleController::class, 'index'])->name('index');
            Route::post('store', [FlashSaleController::class, 'store'])->name('store');
            Route::get('edit/{id}', [FlashSaleController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [FlashSaleController::class, 'update'])->name('update');
            Route::get('status/{id}/{status}', [FlashSaleController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [FlashSaleController::class, 'delete'])->name('delete');

            Route::get('add-product/{flash_sale_id}', [FlashSaleController::class, 'addProduct'])->name('add-product');
            Route::get('add-product-to-session/{flash_sale_id}/{product_id}', [FlashSaleController::class, 'addProductToSession'])->name('add-product-to-session');
            Route::get('delete-product-from-session/{flash_sale_id}/{product_id}', [FlashSaleController::class, 'deleteProductFromSession'])->name('delete-product-from-session');
            Route::get('delete-all-products-from-session/{flash_sale_id}', [FlashSaleController::class, 'deleteAllProductsFromSession'])->name('delete-all-products-from-session');
            Route::post('add-flash-sale-product/{flash_sale_id}', [FlashSaleController::class, 'flashSaleProductStore'])->name('add_flash_sale_product');
            Route::delete('product/delete/{flash_sale_id}/{product_id}', [FlashSaleController::class, 'deleteFlashProduct'])->name('product.delete');
        });

        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.','middleware'=>['actch']], function () {
            Route::get('ecom-setup', [BusinessSettingsController::class, 'BusinessSetup'])->name('ecom-setup');
            Route::post('update-setup', [BusinessSettingsController::class, 'BusinessSetupUpdate'])->name('update-setup');

            Route::get('fcm-index', [BusinessSettingsController::class, 'fcmIndex'])->name('fcm-index');
            Route::post('update-fcm', [BusinessSettingsController::class, 'updateFcm'])->name('update-fcm');

            Route::post('update-fcm-messages', [BusinessSettingsController::class, 'updateFcmMessages'])->name('update-fcm-messages');

            Route::get('mail-config', [BusinessSettingsController::class, 'mailIndex'])->name('mail-config');
            Route::post('mail-send',  [BusinessSettingsController::class, 'mailSend'])->name('mail-send');
            Route::post('mail-config', [BusinessSettingsController::class, 'mailConfig']);
            Route::get('mail-config/status/{status}', [BusinessSettingsController::class, 'mailConfigStatus'])->name('mail-config.status');

            Route::get('payment-method', [BusinessSettingsController::class, 'paymentIndex'])->name('payment-method');
            Route::post('payment-method-update/{payment_method}', [BusinessSettingsController::class, 'paymentUpdate'])->name('payment-method-update');
            Route::post('payment-config-update', [BusinessSettingsController::class, 'paymentConfigUpdate'])->name('payment-config-update')->middleware('actch');

            Route::get('currency-add', [BusinessSettingsController::class, 'currency_index'])->name('currency-add');
            Route::post('currency-add', [BusinessSettingsController::class, 'currencyStore']);
            Route::get('currency-update/{id}', [BusinessSettingsController::class, 'currencyEdit'])->name('currency-update');
            Route::put('currency-update/{id}', [BusinessSettingsController::class, 'currencyUpdate']);
            Route::delete('currency-delete/{id}', [BusinessSettingsController::class, 'currencyDelete'])->name('currency-delete');

            Route::get('terms-and-conditions', [BusinessSettingsController::class, 'termsAndConditions'])->name('terms-and-conditions');
            Route::post('terms-and-conditions', [BusinessSettingsController::class, 'termsAndConditionsUpdate']);

            Route::get('privacy-policy', [BusinessSettingsController::class, 'privacyPolicy'])->name('privacy-policy');
            Route::post('privacy-policy',  [BusinessSettingsController::class, 'privacyPolicyUpdate']);

            Route::get('about-us', [BusinessSettingsController::class, 'aboutUs'])->name('about-us');
            Route::post('about-us', [BusinessSettingsController::class, 'aboutUsUpdate']);

            Route::get('db-index', [DatabaseSettingsController::class, 'databaseIndex'])->name('db-index');
            Route::post('db-clean', [DatabaseSettingsController::class, 'cleanDatabase'])->name('clean-db');

            Route::get('firebase-message-config', [BusinessSettingsController::class, 'firebaseMessageConfigIndex'])->name('firebase_message_config_index');
            Route::post('firebase-message-config', [BusinessSettingsController::class, 'firebaseMessageConfig'])->name('firebase_message_config');

            Route::get('location-setup', [LocationSettingsController::class, 'locationIndex'])->name('location-setup');
            Route::post('update-location', [LocationSettingsController::class, 'locationSetup'])->name('update-location');

            Route::get('sms-module', [SMSModuleController::class, 'smsIndex'])->name('sms-module');
            Route::post('sms-module-update/{sms_module}', [SMSModuleController::class, 'smsUpdate'])->name('sms-module-update');

            Route::get('recaptcha', [BusinessSettingsController::class, 'recaptchaIndex'])->name('recaptcha_index');
            Route::post('recaptcha-update', [BusinessSettingsController::class, 'recaptchaUpdate'])->name('recaptcha_update');

            Route::get('return-page', [BusinessSettingsController::class, 'returnPageIndex'])->name('return_page_index');
            Route::post('return-page-update', [BusinessSettingsController::class, 'returnPageUpdate'])->name('return_page_update');

            Route::get('refund-page', [BusinessSettingsController::class, 'refundPageIndex'])->name('refund_page_index');
            Route::post('refund-page-update', [BusinessSettingsController::class, 'refundPageUpdate'])->name('refund_page_update');

            Route::get('cancellation-page', [BusinessSettingsController::class, 'cancellationPageIndex'])->name('cancellation_page_index');
            Route::post('cancellation-page-update', [BusinessSettingsController::class, 'cancellationPageUpdate'])->name('cancellation_page_update');

            Route::get('app-setting', [BusinessSettingsController::class, 'appSettingIndex'])->name('app_setting');
            Route::post('app-setting', [BusinessSettingsController::class, 'appSettingUpdate']);

            Route::get('currency-position/{position}', [BusinessSettingsController::class, 'currencySymbolPosition'])->name('currency-position');
            Route::get('maintenance-mode', [BusinessSettingsController::class, 'maintenanceMode'])->name('maintenance-mode');

            Route::get('map-api-settings', [BusinessSettingsController::class, 'mapApiSettings'])->name('map_api_settings');
            Route::post('map-api-settings', [BusinessSettingsController::class, 'updateMapApi']);

            Route::get('social-media', [BusinessSettingsController::class, 'socialMedia'])->name('social-media');
            Route::get('fetch', [BusinessSettingsController::class, 'fetch'])->name('fetch');
            Route::post('social-media-store', [BusinessSettingsController::class, 'socialMediaStore'])->name('social-media-store');
            Route::post('social-media-edit', [BusinessSettingsController::class, 'socialMediaEdit'])->name('social-media-edit');
            Route::post('social-media-update', [BusinessSettingsController::class, 'socialMediaUpdate'])->name('social-media-update');
            Route::post('social-media-delete', [BusinessSettingsController::class, 'socialMediaDelete'])->name('social-media-delete');
            Route::post('social-media-status-update', [BusinessSettingsController::class, 'socialMediaStatusUpdate'])->name('social-media-status-update');

            Route::get('otp-setup', [BusinessSettingsController::class, 'otpIndex'])->name('otp-setup');
            Route::post('update-otp', [BusinessSettingsController::class ,'updateOtp'])->name('update-otp');

            Route::get('cookies-setup', [BusinessSettingsController::class, 'cookiesSetup'])->name('cookies-setup');
            Route::post('update-cookies', [BusinessSettingsController::class, 'cookiesSetupUpdate'])->name('update-cookies');

            Route::get('delivery-fee-setup', [BusinessSettingsController::class, 'deliveryFeeSetup'])->name('delivery-fee-setup');
            Route::post('update-delivery-fee', [BusinessSettingsController::class, 'deliveryFeeSetupUpdate'])->name('update-delivery-fee');

            Route::get('social-media-login', [BusinessSettingsController::class, 'socialMediaLogin'])->name('social-media-login');
            Route::get('social_login_status/{medium}/{status}', [BusinessSettingsController::class, 'changeSocialLoginStatus'])->name('social_login_status');
            Route::post('update-apple-login-setup', [BusinessSettingsController::class, 'updateAppleLogin'])->name('update-apple-login-setup');

            Route::get('social-media-chat', [BusinessSettingsController::class, 'socialMediaChat'])->name('social-media-chat');
            Route::post('update-social-media-chat', [BusinessSettingsController::class, 'updateSocialMediaChat'])->name('update-social-media-chat');

            Route::get('customer-setup', [BusinessSettingsController::class, 'customerSetup'])->name('customer-setup');
            Route::post('customer-setup-update', [BusinessSettingsController::class, 'customerSetupUpdate'])->name('customer-setup-update');

            Route::get('login-setup', [LoginSetupController::class, 'loginSetup'])->name('login-setup');
            Route::post('login-setup-update', [LoginSetupController::class, 'loginSetupUpdate'])->name('login-setup-update');
            Route::get('check-active-sms-gateway', [LoginSetupController::class, 'checkActiveSMSGateway'])->name('check-active-sms-gateway');
            Route::get('check-active-social-media', [LoginSetupController::class, 'checkActiveSocialMedia'])->name('check-active-social-media');

            Route::post('maintenance-mode-setup', [BusinessSettingsController::class, 'maintenanceModeSetup'])->name('maintenance-mode-setup')->middleware('actch');

            Route::get('delivery-fee-setup', [DeliveryChargeSetupController::class, 'deliveryFeeSetup'])->name('delivery-fee-setup')->middleware('actch');
            Route::post('store-kilometer-wise-delivery-charge', [DeliveryChargeSetupController::class, 'storeKilometerWiseDeliveryCharge'])->name('store-kilometer-wise-delivery-charge');
            Route::post('store-delivery-wise-delivery-charge', [DeliveryChargeSetupController::class, 'StoreAreaWiseDeliveryCharge'])->name('store-delivery-wise-delivery-charge');
            Route::post('store-fixed-delivery-charge', [DeliveryChargeSetupController::class, 'storeFixedDeliveryCharge'])->name('store-fixed-delivery-charge');
            Route::post('change-delivery-charge-type', [DeliveryChargeSetupController::class, 'changeDeliveryChargeType'])->name('change-delivery-charge-type');
            Route::delete('delete-area-delivery-charge/{id}', [DeliveryChargeSetupController::class, 'deleteAreaDeliveryCharge'])->name('delete-area-delivery-charge');
            Route::get('edit-area-delivery-charge/{id}', [DeliveryChargeSetupController::class, 'editAreaDeliveryCharge'])->name('edit-area-delivery-charge');
            Route::post('update-area-delivery-charge/{id}', [DeliveryChargeSetupController::class, 'updateAreaDeliveryCharge'])->name('update-area-delivery-charge');
            Route::get('export-area-delivery-charge/{id}', [DeliveryChargeSetupController::class, 'exportAreaDeliveryCharge'])->name('export-area-delivery-charge');
            Route::post('import-area-delivery-charge/{id}', [DeliveryChargeSetupController::class, 'importAreaDeliveryCharge'])->name('import-area-delivery-charge');
            Route::get('check-distance-based-delivery', [DeliveryChargeSetupController::class, 'checkDistanceBasedDelivery'])->name('check-distance-based-delivery');
            Route::post('store-free-delivery-over-amount', [DeliveryChargeSetupController::class, 'freeDeliveryOverAmountSetup'])->name('store-free-delivery-over-amount');

            Route::get('firebase-auth', [BusinessSettingsController::class, 'firebaseAuth'])->name('firebase-auth');
            Route::post('update-firebase-auth', [BusinessSettingsController::class, 'updateFirebaseAuth'])->name('update-firebase-auth');

        });

        Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
            Route::get('order', [ReportController::class, 'orderIndex'])->name('order');
            Route::get('earning', [ReportController::class, 'earningIndex'])->name('earning');
            Route::post('set-date', [ReportController::class, 'setDate'])->name('set-date');
            Route::get('driver-report', [ReportController::class, 'driverReport'])->name('driver-report');
            Route::get('product-report', [ReportController::class, 'productReport'])->name('product-report');
            Route::get('export-product-report', [ReportController::class, 'exportProductReport'])->name('export-product-report');
            Route::get('sale-report', [ReportController::class, 'saleReport'])->name('sale-report');
            Route::get('export-sale-report', [ReportController::class, 'exportSaleReport'])->name('export-sale-report');
            Route::get('wallet-transaction-history', [ReportController::class, 'walletTransactionHistory'])->name('wallet-transaction-history');
            Route::get('export-wallet-transaction-history', [ReportController::class, 'exportWalletTransactionHistory'])->name('export-wallet-transaction-history');
        });

        Route::group(['prefix' => 'customer', 'as' => 'customer.'], function () {
            Route::get('list', [CustomerController::class, 'customerList'])->name('list');
            Route::get('view/{user_id}', [CustomerController::class, 'view'])->name('view');
            Route::get('subscribed-emails', [CustomerController::class, 'subscribedEmails'])->name('subscribed_emails');
            Route::get('export-subscribed-emails', [CustomerController::class, 'exportSubscribedEmails'])->name('export-subscribed-emails');
        });

        Route::get('system-addons-index', function (){
            return to_route('admin.system-addon.index');
        })->name('addon.index');

        Route::group(['namespace' => 'System','prefix' => 'system-addon', 'as' => 'system-addon.'], function () {
            Route::get('/', [AddonController::class, 'index'])->name('index');
            Route::post('publish',  [AddonController::class, 'publish'])->name('publish');
            Route::post('activation',  [AddonController::class, 'activation'])->name('activation');
            Route::post('upload',  [AddonController::class, 'upload'])->name('upload');
            Route::post('delete',  [AddonController::class, 'deleteAddon'])->name('delete');
        });
    });
});
