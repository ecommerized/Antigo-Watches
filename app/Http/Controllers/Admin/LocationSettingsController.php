<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationSettingsController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function locationIndex(): View|Factory|Application
    {
        return view('admin-views.business-settings.location-index');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function locationSetup(Request $request): RedirectResponse
    {
        DB::table('branches')->updateOrInsert(['id' => 1], [
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'coverage' => $request['coverage'] ? $request['coverage'] : 0,
        ]);

        Toastr::success(translate('Settings updated!'));
        return back();
    }
}
