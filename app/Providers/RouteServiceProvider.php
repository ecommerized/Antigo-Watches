<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
  public function boot()
{
    parent::boot();

    // Load API V1 routes
    Route::middleware('api')
        ->prefix('api/v1')
        ->group(base_path('routes/api/v1/api.php'));

    // Load API V2 routes
    Route::middleware('api')
        ->prefix('api/v2')
        ->group(base_path('routes/api/v2/api.php'));
}

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiV1Routes();

        $this->mapApiV2Routes();

        $this->mapWebRoutes();

        $this->mapAdminRoutes();

        $this->mapBranchRoutes();

        //$this->mapUpdateRoutes();

        //$this->mapInstallRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }


    protected function mapAdminRoutes()
    {
        Route::prefix('admin')
            ->middleware('web')
            ->group(base_path('routes/admin.php'));
    }

    protected function mapBranchRoutes()
    {
        Route::prefix('branch')
            ->middleware('web')
            ->group(base_path('routes/branch.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiV1Routes()
    {
        Route::prefix('api/v1')
            ->middleware('api')
            ->group(base_path('routes/api/v1/api.php'));
    }

    protected function mapApiV2Routes()
    {
        Route::prefix('api/v2')
            ->middleware('api')
            ->group(base_path('routes/api/v2/api.php'));
    }

    protected function mapInstallRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/install.php'));
    }

    protected function mapUpdateRoutes()
    {
        Route::middleware('web')
            ->group(base_path('routes/update.php'));
    }
}
