<?php

namespace App\Library;

use App\Models\BusinessSetting;

if (!function_exists('responseFormatter')) {
    function responseFormatter($constant, $content = null, $limit = null, $offset = null, $errors = [], $params =[]): array
    {
        $data = [
            'total_size' => isset($limit) ? $content->total() : null,
            'limit' => $limit,
            'offset' => $offset,
            'data' => $content,
            'errors' => $errors,
        ];
        $responseConst = [
            'response_code' => $constant['response_code'],
            'message' => translate($constant['message']),
        ];
        return array_merge($responseConst, $data,$params);
    }
}

if (!function_exists('errorProcessor')) {
    function errorProcessor($validator)
    {
        $errors = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $errors[] = ['error_code' => $index, 'message' => translate($error[0])];
        }
        return $errors;
    }
}

if (!function_exists('businessSettingInsertOrUpdate')) {

    function businessSettingInsertOrUpdate($key, $value): void
    {
        $businessSetting = BusinessSetting::where(['key' => $key['key']])->first();
        if ($businessSetting) {
            $businessSetting->value = $value['value'];
            $businessSetting->save();
        } else {
            $data = [
                'key' => $key['key'],
                'value' => $value['value'],
            ];
            BusinessSetting::create($data);
        }
    }
}
