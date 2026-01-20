<?php

use App\Http\Controllers\Branch\Auth\LoginController;
use App\Http\Controllers\Branch\OrderController;
use App\Http\Controllers\Branch\POSController;
use App\Http\Controllers\Branch\SystemController;
use Illuminate\Support\Facades\Route;


Route::group(['as' => 'branch.', 'middleware' => 'maintenance_mode'], function () {
    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/code/captcha/{tmp}', [LoginController::class,'captcha'])->name('default-captcha');
        Route::get('login', [LoginController::class,'login'])->name('login');
        Route::post('login', [LoginController::class,'submit']);
        Route::get('logout', [LoginController::class,'logout'])->name('logout');
    });
    /*authentication*/

    Route::group(['middleware' => ['branch']], function () {
        Route::get('/', [SystemController::class, 'dashboard'])->name('dashboard');
        Route::get('settings', [SystemController::class, 'settings'])->name('settings');
        Route::post('settings', [SystemController::class, 'settingsUpdate']);
        Route::post('settings-password', [SystemController::class, 'settingsPasswordUpdate'])->name('settings-password');
        Route::post('order-stats', [SystemController::class, 'orderStats'])->name('order-stats');
        Route::get('/get-restaurant-data', [SystemController::class, 'restaurantData'])->name('get-restaurant-data');
        Route::get('dashboard/earning-statistics', [SystemController::class, 'getEarningStatistics'])->name('dashboard.earning-statistics');
        Route::get('ignore-check-order', [SystemController::class, 'ignoreCheckOrder'])->name('ignore-check-order');

        Route::group(['prefix' => 'pos', 'as' => 'pos.'], function () {
            Route::get('/', [POSController::class, 'index'])->name('index');
            Route::get('quick-view', [POSController::class, 'quickView'])->name('quick-view');
            Route::get('quick-view-modal-footer', [POSController::class, 'quickViewModalFooter'])->name('quick-view-modal-footer');
            Route::post('variant_price', [POSController::class, 'variantPrice'])->name('variant_price');
            Route::post('add-to-cart', [POSController::class, 'addToCart'])->name('add-to-cart');
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

        Route::group(['prefix' => 'orders', 'as' => 'orders.'], function () {
            Route::get('list/{status}', [OrderController::class, 'list'])->name('list');
            Route::get('details/{id}', [OrderController::class, 'details'])->name('details');
            Route::get('status', [OrderController::class, 'status'])->name('status');
            Route::get('add-delivery-man/{order_id}/{delivery_man_id}', [OrderController::class, 'addDeliveryMan'])->name('add-delivery-man');
            Route::get('payment-status', [OrderController::class, 'paymentStatus'])->name('payment-status');
            Route::get('generate-invoice/{id}', [OrderController::class, 'generateInvoice'])->name('generate-invoice');
            Route::post('add-payment-ref-code/{id}', [OrderController::class, 'addPaymentRefCode'])->name('add-payment-ref-code');
            Route::get('export/{status}', [OrderController::class, 'exportOrders'])->name('export');
            Route::get('search-product', [OrderController::class, 'searchProduct'])->name('search-product');
            Route::post('update-product-list/{id}', [OrderController::class, 'updateProductList'])->name('update-product-list');
            Route::post('update-shipping/{id}', [OrderController::class, 'updateShipping'])->name('update-shipping');

        });
    });
});
