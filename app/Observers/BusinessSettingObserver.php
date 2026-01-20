<?php

namespace App\Observers;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Cache;

class BusinessSettingObserver
{
    /**
     * Handle the BusinessSetting "created" event.
     *
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return void
     */
    public function created(BusinessSetting $businessSetting)
    {
        $this->refreshBusinessSettingsCache();
    }

    /**
     * Handle the BusinessSetting "updated" event.
     *
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return void
     */
    public function updated(BusinessSetting $businessSetting)
    {
        $this->refreshBusinessSettingsCache();
    }

    /**
     * Handle the BusinessSetting "deleted" event.
     *
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return void
     */
    public function deleted(BusinessSetting $businessSetting)
    {
        $this->refreshBusinessSettingsCache();
    }

    /**
     * Handle the BusinessSetting "restored" event.
     *
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return void
     */
    public function restored(BusinessSetting $businessSetting)
    {
        $this->refreshBusinessSettingsCache();
    }

    /**
     * Handle the BusinessSetting "force deleted" event.
     *
     * @param  \App\Models\BusinessSetting  $businessSetting
     * @return void
     */
    public function forceDeleted(BusinessSetting $businessSetting)
    {
        $this->refreshBusinessSettingsCache();
    }

    private function refreshBusinessSettingsCache()
    {
        Cache::forget(CACHE_BUSINESS_SETTINGS_TABLE);
    }
}
