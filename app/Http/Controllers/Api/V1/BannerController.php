<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    public function __construct(
        private Banner $banner
    )
    {
    }

    /**
     * @return JsonResponse
     */
    public function getBanners(): JsonResponse
    {
        $banners = Cache::rememberForever(CACHE_BANNER_TABLE, function () {
            return $this->banner->active()->latest()->get();
        });

        return response()->json($banners, 200);
    }
}
