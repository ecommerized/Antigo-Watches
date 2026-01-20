<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\{
    BannerController,
    CategoryController,
    ConversationController,
    CouponController,
    CustomerController,
    DeliverymanController,
    DeliveryManReviewController,
    FlashSaleController,
    LanguageController,
    MapApiController,
    NotificationController,
    PageController,
    ProductController,
    WishlistController,
    OrderController,
    GuestUserController,
    ConfigController
};
use App\Http\Controllers\Api\V1\Auth\{
    CustomerAuthController,
    PasswordResetController,
    DeliveryManLoginController
};

Route::group(['middleware' => 'localization'], function () {

    Route::post('fcm-subscribe-to-topic', [CustomerController::class, 'fcmSubscribeToTopic']);

    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::controller(CustomerAuthController::class)->group(function () {
            Route::post('registration', 'registration');
            Route::post('login', 'login');
            Route::post('social-customer-login', 'social_customer_login');
            Route::post('check-phone', 'check_phone');
            Route::post('verify-phone', 'verify_phone');
            Route::post('check-email', 'check_email');
            Route::post('verify-email', 'verify_email');
            Route::post('firebase-auth-verify', 'firebaseAuthVerify');
            Route::post('verify-otp', 'verifyOTP');
            Route::post('registration-with-otp', 'registrationWithOTP');
            Route::post('existing-account-check', 'existingAccountCheck');
            Route::post('registration-with-social-media', 'registrationWithSocialMedia');
        });

        Route::controller(PasswordResetController::class)->group(function () {
            Route::post('forgot-password', 'reset_password_request');
            Route::post('verify-token', 'verify_token');
            Route::put('reset-password', 'reset_password_submit');
        });

        Route::prefix('delivery-man')->controller(DeliveryManLoginController::class)->group(function () {
            Route::post('register', 'registration');
            Route::post('login', 'login');
        });
    });

    // Deliveryman routes
    Route::prefix('delivery-man')->group(function () {
        Route::controller(DeliverymanController::class)->group(function () {
            Route::get('profile', 'getProfile');
            Route::get('current-orders', 'getCurrentOrders');
            Route::get('all-orders', 'getAllOrders');
            Route::get('orders-count', 'getOrdersCount');
            Route::post('record-location-data', 'recordLocationData');
            Route::get('order-delivery-history', 'getOrderHistory');
            Route::put('update-order-status', 'updateOrderStatus');
            Route::put('update-payment-status', 'orderPaymentStatusUpdate');
            Route::get('order-details', 'getOrderDetails');
            Route::get('last-location', 'getLastLocation');
            Route::put('update-fcm-token', 'updateFcmToken');
            Route::get('order-model', 'orderModel');
            Route::delete('remove-account', 'removeAccount');
        });

        Route::prefix('message')->controller(ConversationController::class)->group(function () {
            Route::post('get-message', 'getOrderMessageForDm');
            Route::post('send/{sender_type}', 'storeMessageByOrder');
        });

        Route::prefix('reviews')->middleware('auth:api')->controller(DeliveryManReviewController::class)->group(function () {
            Route::get('{delivery_man_id}', 'getReviews');
            Route::get('rating/{delivery_man_id}', 'getRating');
            Route::post('submit', 'submitReview');
        });
    });

    // Config
    Route::prefix('config')->controller(ConfigController::class)->group(function () {
        Route::get('/', 'configuration');
        Route::get('delivery-fee', 'deliveryFree');
    });

    // Product routes
    Route::prefix('products')->controller(ProductController::class)->group(function () {
        Route::get('latest', 'getLatestProduct');
        Route::get('discounted', 'getDiscountedProduct');
        Route::get('search', 'getSearchedProduct');
        Route::get('details/{id}', 'getProduct');
        Route::get('related-products/{product_id}', 'getRelatedProduct');
        Route::get('reviews/{product_id}', 'getProductReviews');
        Route::get('rating/{product_id}', 'getProductRating');
        Route::get('new-arrival', 'getNewArrivalProducts');
        Route::post('reviews/submit', 'submitProductReview')->middleware('auth:api');
    });

    // Banners
    Route::prefix('banners')->controller(BannerController::class)->group(function () {
        Route::get('/', 'getBanners');
    });

    // Notifications
    Route::prefix('notifications')->middleware('guest_user')->controller(NotificationController::class)->group(function () {
        Route::get('/', 'getNotifications');
    });

    // Categories
    Route::prefix('categories')->controller(CategoryController::class)->group(function () {
        Route::get('/', 'getCategories');
        Route::get('childes/{category_id}', 'getChildes');
        Route::get('products/{category_id}', 'getProducts');
        Route::get('products/{category_id}/all', 'getAllProducts');
        Route::get('featured', 'getFeaturedCategories');
        Route::get('popular', 'getPopularCategories');
    });

    // Customer related routes
    Route::prefix('customer')->middleware('auth:api')->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('info', 'info');
            Route::get('wallet-transaction', 'walletTransaction');
            Route::put('update-profile', 'updateProfile');
            Route::put('cm-firebase-token', 'updateCmFirebaseToken');
            Route::post('verify-profile-info', 'verifyProfileInfo');
            Route::delete('remove-account', 'removeAccount');

            Route::prefix('address')->middleware('guest_user')->withoutMiddleware('auth:api')->group(function () {
                Route::get('list', 'addressList');
                Route::post('add', 'addNewAddress');
                Route::put('update/{id}', 'updateAddress');
                Route::delete('delete', 'deleteAddress');
            });
        });
        Route::post('payment-mobile', [PaymentController::class, 'payment'])->middleware('guest_user')->withoutMiddleware('auth:api')->name('payment-mobile');

        Route::controller(OrderController::class)->group(function () {
            Route::prefix('order')->middleware('guest_user')->withoutMiddleware('auth:api')->group(function () {
                Route::get('list', 'getOrderList');
                Route::post('details', 'getOrderDetails');
                Route::post('place', 'placeOrder');
                Route::put('cancel', 'cancelOrder');
                Route::post('track', 'trackOrder');
                Route::put('payment-method', 'updatePaymentMethod');
            });

            Route::get('reorder/products', 'getReorderProduct');
        });

        Route::prefix('message')->controller(ConversationController::class)->group(function () {
            Route::get('get-admin-message', 'getAdminMessage');
            Route::post('send-admin-message', 'storeAdminMessage');
            Route::get('get-order-message', 'getMessageByOrder');
            Route::post('send/{sender_type}', 'storeMessageByOrder');
        });

        Route::prefix('wish-list')->controller(WishlistController::class)->group(function () {
            Route::get('/', 'wishlist');
            Route::post('add', 'addToWishlist');
            Route::delete('remove', 'removeFromWishlist');
        });
    });

    // Coupon
    Route::prefix('coupon')->middleware('guest_user')->controller(CouponController::class)->group(function () {
        Route::get('list', 'list');
        Route::get('apply', 'apply');
    });

    // Language
    Route::prefix('language')->controller(LanguageController::class)->group(function () {
        Route::get('/', 'get');
    });

    // Map APIs
    Route::prefix('mapapi')->controller(MapApiController::class)->group(function () {
        Route::get('place-api-autocomplete', 'placeApiAutocomplete');
        Route::get('distance-api', 'distanceApi');
        Route::get('place-api-details', 'placeApiDetails');
        Route::get('geocode-api', 'geoCodeApi');
    });

    // Newsletter
    Route::post('subscribe-newsletter', [CustomerController::class, 'subscribeNewsLetter']);

    // Pages
    Route::get('pages', [PageController::class, 'index']);

    // Flash Sale
    Route::get('flash-sale', [FlashSaleController::class, 'getFlashSale']);

    // Guest user
    Route::prefix('guest')->controller(GuestUserController::class)->group(function () {
        Route::post('add', 'guestStore');
    });

});
