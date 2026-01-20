<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Service Providers
    |--------------------------------------------------------------------------
    */

    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    // App\Providers\BroadcastServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    App\Providers\ConfigServiceProvider::class,

    /*
    |--------------------------------------------------------------------------
    | Third-Party Service Providers
    |--------------------------------------------------------------------------
    */
    Barryvdh\DomPDF\ServiceProvider::class,
    Kreait\Laravel\Firebase\ServiceProvider::class,
];
