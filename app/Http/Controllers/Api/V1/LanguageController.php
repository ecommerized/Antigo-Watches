<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use Illuminate\Http\JsonResponse;
use App\Models\BusinessSetting;

class LanguageController extends Controller
{
    public function __construct(
        private BusinessSetting $businessSetting
    )
    {
    }

    /**
     * @return JsonResponse
     */
    public function get(): JsonResponse
    {
        $languages = json_decode($this->businessSetting->where(['key' => 'language'])->first()->value, true);

        $languages = array_map(function ($lang) {
            return array(
                'key' => $lang,
                'value' => \App\CentralLogics\Helpers::get_language_name($lang)
            );
        }, $languages);

        return response()->json($languages, 200);
    }
}
