<?php

namespace App\Observers;

use App\Models\LoginSetup;
use Illuminate\Support\Facades\Cache;

class LoginSetupObserver
{
    /**
     * Handle the LoginSetup "created" event.
     *
     * @param  \App\Models\LoginSetup  $loginSetup
     * @return void
     */
    public function created(LoginSetup $loginSetup)
    {
        $this->refreshLoginSetupCache();
    }

    /**
     * Handle the LoginSetup "updated" event.
     *
     * @param  \App\Models\LoginSetup  $loginSetup
     * @return void
     */
    public function updated(LoginSetup $loginSetup)
    {
        $this->refreshLoginSetupCache();
    }

    /**
     * Handle the LoginSetup "deleted" event.
     *
     * @param  \App\Models\LoginSetup  $loginSetup
     * @return void
     */
    public function deleted(LoginSetup $loginSetup)
    {
        $this->refreshLoginSetupCache();
    }

    /**
     * Handle the LoginSetup "restored" event.
     *
     * @param  \App\Models\LoginSetup  $loginSetup
     * @return void
     */
    public function restored(LoginSetup $loginSetup)
    {
        $this->refreshLoginSetupCache();
    }

    /**
     * Handle the LoginSetup "force deleted" event.
     *
     * @param  \App\Models\LoginSetup  $loginSetup
     * @return void
     */
    public function forceDeleted(LoginSetup $loginSetup)
    {
        $this->refreshLoginSetupCache();
    }

    private function refreshLoginSetupCache()
    {
        Cache::forget(CACHE_LOGIN_SETUP_TABLE);
    }
}
