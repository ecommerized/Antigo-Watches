<?php

namespace App\Providers;

use App\CentralLogics\Helpers;
use App\Models\Banner;
use App\Models\Category;
use App\Models\LoginSetup;
use App\Observers\BannerObserver;
use App\Observers\BusinessSettingObserver;
use App\Observers\CategoryObserver;
use App\Observers\LoginSetupObserver;
use App\Traits\SystemAddonTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use App\Models\BusinessSetting;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    use SystemAddonTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Custom class aliases (facades) used in your app
        $aliases = [
            'Helpers'  => Helpers::class,
            'PDF' => Pdf::class,
        ];

        foreach ($aliases as $alias => $class) {
            if (! class_exists($alias) && class_exists($class)) {
                class_alias($class, $alias);
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(env('FORCE_HTTPS')) {
            \URL::forceScheme('https');
        }

        BusinessSetting::observe(BusinessSettingObserver::class);
        LoginSetup::observe(LoginSetupObserver::class);
        Banner::observe(BannerObserver::class);
        Category::observe(CategoryObserver::class);

        //for system addon
        Config::set('addon_admin_routes',$this->get_addon_admin_routes());
        Config::set('get_payment_publish_status',$this->get_payment_publish_status());

        try {
            $timezone = BusinessSetting::where(['key' => 'time_zone'])->first();
            if (isset($timezone)) {
                config(['app.timezone' => $timezone->value]);
                date_default_timezone_set($timezone->value);
            }
        }catch(\Exception $exception){}

        Paginator::useBootstrap();
    }
}
