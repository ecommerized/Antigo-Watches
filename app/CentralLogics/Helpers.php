<?php

namespace App\CentralLogics;

use App\Models\Branch;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\DMReview;
use App\Models\Order;
use App\Models\Review;
use App\Models\DeliveryChargeByArea;
use App\Models\LoginSetup;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            $err_keeper[] = ['code' => $index, 'message' => $error[0]];
        }
        return $err_keeper;
    }

    public static function getPagination()
    {
        $pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
        return $pagination_limit->value;
    }

    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function variation_price($product, $variation)
    {
        if (empty(json_decode($variation, true))) {
            $result = $product['price'];
        } else {
            $match = json_decode($variation, true)[0];
            $result = 0;
            foreach (json_decode($product['variations'], true) as $property => $value) {
                if ($value['type'] == $match['type']) {
                    $result = $value['price'];
                }
            }
        }
        return $result;
    }

    public static function product_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data) {
            foreach ($data as $item) {
                $variations = [];
                $item['category_ids'] = json_decode($item['category_ids']);
                $item['image'] = json_decode($item['image']);
                $item['attributes'] = json_decode($item['attributes']);
                $item['choice_options'] = json_decode($item['choice_options']);
                foreach (json_decode($item['variations'], true) as $var) {
                    $variations[] = [
                        'type' => $var['type'],
                        'price' => (double)$var['price'],
                        'stock' => (double)$var['stock'],
                    ];
                }
                $item['variations'] = $variations;

                if (count($item['translations'])) {
                    foreach ($item['translations'] as $translation) {
                        if ($translation->key == 'name') {
                            $item['name'] = $translation->value;
                        }
                        if ($translation->key == 'description') {
                            $item['description'] = $translation->value;
                        }
                    }
                }
                unset($item['translations']);
                $storage[] = $item;
            }
            $data = $storage;
        } else {
            $variations = [];
            $data['category_ids'] = json_decode($data['category_ids']);
            $data['image'] = json_decode($data['image']);
            $data['attributes'] = json_decode($data['attributes']);
            $data['choice_options'] = json_decode($data['choice_options']);
            foreach (json_decode($data['variations'], true) as $var) {
                $variations[] = [
                    'type' => $var['type'],
                    'price' => (double)$var['price'],
                    'stock' => (int)$var['stock'],
                ];
            }

            $data['variations'] = $variations;
            if (count($data['translations']) > 0) {
                foreach ($data['translations'] as $translation) {
                    if ($translation->key == 'name') {
                        $data['name'] = $translation->value;
                    }
                    if ($translation->key == 'description') {
                        $data['description'] = $translation->value;
                    }
                }
            }
        }

        return $data;
    }

    public static function order_details_formatter($details)
    {
        if ($details->count() > 0) {
            foreach ($details as $detail) {
                $detail['product_details'] = gettype($detail['product_details']) != 'array' ? (array)json_decode($detail['product_details'], true) : (array)$detail['product_details'];
                $detail['variation'] = (function ($v) {
                    $v = is_string($v) ? json_decode($v, true) : $v;
                    if (!is_array($v)) return [];
                    if ($v === [[]]) return [];
                    return (count($v) === 1 && is_array(reset($v))) ? reset($v) : $v;
                })($detail['variation']);

                $detail['variant'] = gettype($detail['variant']) != 'array' ? (array)json_decode($detail['variant'], true) : (array)$detail['variant'];

                $orderType = Order::find($detail->order_id)->order_type;

                if ($orderType === 'pos') {
                    $detail['variation'] = !empty($detail['variation'])
                        ? implode('-', array_values($detail['variation']))
                        : null;
                } else {
                    $detail['variation'] = (count($detail['variation']) > 0 && in_array(null, $detail['variation'], true))
                        ? null
                        : (!empty($detail['variation']) ? implode('-', array_values($detail['variation'])) : null);
                }

                $detail['product_details'] = Helpers::product_formatter($detail['product_details']);
            }
        }

        return $details;
    }

    public static function product_formatter($product)
    {
        $product['image'] = gettype($product['image']) != 'array' ? (array)json_decode($product['image'], true) : (array)$product['image'];
        $product['variations'] = gettype($product['variations']) != 'array' ? (array)json_decode($product['variations'], true) : (array)$product['variations'];
        $product['attributes'] = gettype($product['attributes']) != 'array' ? (array)json_decode($product['attributes'], true) : (array)$product['attributes'];
        $product['category_ids'] = gettype($product['category_ids']) != 'array' ? (array)json_decode($product['category_ids'], true) : (array)$product['category_ids'];
        $product['choice_options'] = gettype($product['choice_options']) != 'array' ? (array)json_decode($product['choice_options'], true) : (array)$product['choice_options'];

        return $product;
    }

    public static function get_business_settings($name)
    {
        $config = null;
        $settings = Cache::rememberForever(CACHE_BUSINESS_SETTINGS_TABLE, function () {
            return BusinessSetting::all();
        });

        $data = $settings?->firstWhere('key', $name);
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }

    public static function get_login_settings($name)
    {
        $config = null;
        $settings = Cache::rememberForever(CACHE_LOGIN_SETUP_TABLE, function () {
            return LoginSetup::all();
        });

        $data = $settings?->firstWhere('key', $name);
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }

    public static function currency_code()
    {
        $currency_code = BusinessSetting::where(['key' => 'currency'])->first()->value;
        return $currency_code;
    }

    public static function currency_symbol()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        return $currency_symbol;
    }

    public static function set_symbol($amount)
    {
        $position = Helpers::get_business_settings('currency_symbol_position');
        if (!is_null($position) && $position == 'left') {
            $string = self::currency_symbol() . '' . number_format($amount, 2);
        } else {
            $string = number_format($amount, 2) . '' . self::currency_symbol();
        }
        return $string;
    }

    /**
     * @param array|null $data
     * @return false|void
     */
    public static function sendNotificationToHttp(array|null $data)
    {
        $config = self::get_business_settings('push_notification_service_file_content');
        $key = (array)$config;
        $url = 'https://fcm.googleapis.com/v1/projects/'.$key['project_id'].'/messages:send';
        $headers = [
            'Authorization' => 'Bearer ' . self::getAccessToken($key),
            'Content-Type' => 'application/json',
        ];
        try {
            Http::withHeaders($headers)->post($url, $data);
        }catch (\Exception $exception){
            return false;
        }
    }

    public static function getAccessToken($key):String
    {
        $jwtToken = [
            'iss' => $key['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => time() + 3600,
            'iat' => time(),
        ];
        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtPayload = base64_encode(json_encode($jwtToken));
        $unsignedJwt = $jwtHeader . '.' . $jwtPayload;
        openssl_sign($unsignedJwt, $signature, $key['private_key'], OPENSSL_ALGO_SHA256);
        $jwt = $unsignedJwt . '.' . base64_encode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);
        return $response->json('access_token');
    }

    public static function send_push_notif_to_device($fcm_token, $data)
    {
        $postData = [
            'message' => [
                "token" => $fcm_token,
                "data" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "image" => (string)$data['image'],
                    "order_id" => (string)$data['order_id'],
                    "type" => (string)$data['type'],
                    "user_name" => (string)($data['user_name'] ?? ''),
                    "user_image" => (string)($data['user_image'] ?? ''),
                ],
                "notification" => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    "image" => (string)$data['image'],
                ],
                "android" => [
                    'priority' => 'high',
                    "notification" => [
                        "channel_id" => "hexacom",
                        "sound" => "notification.wav",
                        "icon" => "notification_icon",
                    ]
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "notification.wav"
                        ]
                    ],
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                ],
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function send_push_notif_to_topic($data, $type)
    {
        $image = asset('storage/app/public/notification') . '/' . $data['image'];

        $postData = [
            'message' => [
                "topic" => 'market',
                "data" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "order_id" => (string)$data['order_id'],
                    "type" => (string)$data['type'],
                    "image" => (string)$image
                ],
                "notification" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "image" => (string)$image,
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "notification.wav"
                        ]
                    ]
                ],
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function sendPushNotifToTopicForMaintenanceMode($data, $topic)
    {
        $postData = [
            'message' => [
                "topic" => $topic,
                "data" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "type" => (string)$data['type'],
                ],
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function rating_count($product_id, $rating)
    {
        return Review::where(['product_id' => $product_id, 'rating' => $rating])->count();
    }

    public static function dm_rating_count($deliveryman_id, $rating)
    {
        return DMReview::where(['delivery_man_id' => $deliveryman_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($product, $price)
    {
        if ($product['tax_type'] == 'percent') {
            $price_tax = ($price / 100) * $product['tax'];
        } else {
            $price_tax = $product['tax'];
        }
        return $price_tax;
    }

    public static function discount_calculate($product, $price)
    {
        if ($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    public static function max_earning()
    {
        $data = Order::where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function order_status_update_message($status)
    {
        if ($status == 'pending') {
            $data = self::get_business_settings('order_pending_message');
        } elseif ($status == 'confirmed') {
            $data = self::get_business_settings('order_confirmation_msg');
        } elseif ($status == 'processing') {
            $data = self::get_business_settings('order_processing_message');
        } elseif ($status == 'out_for_delivery') {
            $data = self::get_business_settings('out_for_delivery_message');
        } elseif ($status == 'delivered') {
            $data = self::get_business_settings('order_delivered_message');
        } elseif ($status == 'delivery_boy_delivered') {
            $data = self::get_business_settings('delivery_boy_delivered_message');
        } elseif ($status == 'del_assign') {
            $data = self::get_business_settings('delivery_boy_assign_message');
        } elseif ($status == 'ord_start') {
            $data = self::get_business_settings('delivery_boy_start_message');
        } elseif ($status == 'returned') {
            $data = self::get_business_settings('returned_message');
        }  elseif ($status == 'failed') {
            $data = self::get_business_settings('failed_message');
        }  elseif ($status == 'canceled') {
            $data = self::get_business_settings('canceled_message');
        } elseif ($status == 'customer_notify_message') {
            $data = self::get_business_settings('customer_notify_message');
        } else {
            $data = '{"status":"0","message":""}';
        }

        if ($data == null || $data['status'] == 0) {
            return 0;
        }
        return $data['message'];
    }

    public static function day_part()
    {
        $part = "";
        $morning_start = date("h:i:s", strtotime("5:00:00"));
        $afternoon_start = date("h:i:s", strtotime("12:01:00"));
        $evening_start = date("h:i:s", strtotime("17:01:00"));
        $evening_end = date("h:i:s", strtotime("21:00:00"));

        if (time() >= $morning_start && time() < $afternoon_start) {
            $part = "morning";
        } elseif (time() >= $afternoon_start && time() < $evening_start) {
            $part = "afternoon";
        } elseif (time() >= $evening_start && time() <= $evening_end) {
            $part = "evening";
        } else {
            $part = "night";
        }

        return $part;
    }

    public static function remove_dir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") Helpers::remove_dir($dir . "/" . $object); else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function get_language_name($key)
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian - shqip",
            "am" => "Amharic - አማርኛ",
            "ar" => "Arabic - العربية",
            "an" => "Aragonese - aragonés",
            "hy" => "Armenian - հայերեն",
            "ast" => "Asturian - asturianu",
            "az" => "Azerbaijani - azərbaycan dili",
            "eu" => "Basque - euskara",
            "be" => "Belarusian - беларуская",
            "bn" => "Bengali - বাংলা",
            "bs" => "Bosnian - bosanski",
            "br" => "Breton - brezhoneg",
            "bg" => "Bulgarian - български",
            "ca" => "Catalan - català",
            "ckb" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
            "zh" => "Chinese - 中文",
            "zh-HK" => "Chinese (Hong Kong) - 中文（香港）",
            "zh-CN" => "Chinese (Simplified) - 中文（简体）",
            "zh-TW" => "Chinese (Traditional) - 中文（繁體）",
            "co" => "Corsican",
            "hr" => "Croatian - hrvatski",
            "cs" => "Czech - čeština",
            "da" => "Danish - dansk",
            "nl" => "Dutch - Nederlands",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-CA" => "English (Canada)",
            "en-IN" => "English (India)",
            "en-NZ" => "English (New Zealand)",
            "en-ZA" => "English (South Africa)",
            "en-GB" => "English (United Kingdom)",
            "en-US" => "English (United States)",
            "eo" => "Esperanto - esperanto",
            "et" => "Estonian - eesti",
            "fo" => "Faroese - føroyskt",
            "fil" => "Filipino",
            "fi" => "Finnish - suomi",
            "fr" => "French - français",
            "fr-CA" => "French (Canada) - français (Canada)",
            "fr-FR" => "French (France) - français (France)",
            "fr-CH" => "French (Switzerland) - français (Suisse)",
            "gl" => "Galician - galego",
            "ka" => "Georgian - ქართული",
            "de" => "German - Deutsch",
            "de-AT" => "German (Austria) - Deutsch (Österreich)",
            "de-DE" => "German (Germany) - Deutsch (Deutschland)",
            "de-LI" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
            "de-CH" => "German (Switzerland) - Deutsch (Schweiz)",
            "el" => "Greek - Ελληνικά",
            "gn" => "Guarani",
            "gu" => "Gujarati - ગુજરાતી",
            "ha" => "Hausa",
            "haw" => "Hawaiian - ʻŌlelo Hawaiʻi",
            "he" => "Hebrew - עברית",
            "hi" => "Hindi - हिन्दी",
            "hu" => "Hungarian - magyar",
            "is" => "Icelandic - íslenska",
            "id" => "Indonesian - Indonesia",
            "ia" => "Interlingua",
            "ga" => "Irish - Gaeilge",
            "it" => "Italian - italiano",
            "it-IT" => "Italian (Italy) - italiano (Italia)",
            "it-CH" => "Italian (Switzerland) - italiano (Svizzera)",
            "ja" => "Japanese - 日本語",
            "kn" => "Kannada - ಕನ್ನಡ",
            "kk" => "Kazakh - қазақ тілі",
            "km" => "Khmer - ខ្មែរ",
            "ko" => "Korean - 한국어",
            "ku" => "Kurdish - Kurdî",
            "ky" => "Kyrgyz - кыргызча",
            "lo" => "Lao - ລາວ",
            "la" => "Latin",
            "lv" => "Latvian - latviešu",
            "ln" => "Lingala - lingála",
            "lt" => "Lithuanian - lietuvių",
            "mk" => "Macedonian - македонски",
            "ms" => "Malay - Bahasa Melayu",
            "ml" => "Malayalam - മലയാളം",
            "mt" => "Maltese - Malti",
            "mr" => "Marathi - मराठी",
            "mn" => "Mongolian - монгол",
            "ne" => "Nepali - नेपाली",
            "no" => "Norwegian - norsk",
            "nb" => "Norwegian Bokmål - norsk bokmål",
            "nn" => "Norwegian Nynorsk - nynorsk",
            "oc" => "Occitan",
            "or" => "Oriya - ଓଡ଼ିଆ",
            "om" => "Oromo - Oromoo",
            "ps" => "Pashto - پښتو",
            "fa" => "Persian - فارسی",
            "pl" => "Polish - polski",
            "pt" => "Portuguese - português",
            "pt-BR" => "Portuguese (Brazil) - português (Brasil)",
            "pt-PT" => "Portuguese (Portugal) - português (Portugal)",
            "pa" => "Punjabi - ਪੰਜਾਬੀ",
            "qu" => "Quechua",
            "ro" => "Romanian - română",
            "mo" => "Romanian (Moldova) - română (Moldova)",
            "rm" => "Romansh - rumantsch",
            "ru" => "Russian - русский",
            "gd" => "Scottish Gaelic",
            "sr" => "Serbian - српски",
            "sh" => "Serbo-Croatian - Srpskohrvatski",
            "sn" => "Shona - chiShona",
            "sd" => "Sindhi",
            "si" => "Sinhala - සිංහල",
            "sk" => "Slovak - slovenčina",
            "sl" => "Slovenian - slovenščina",
            "so" => "Somali - Soomaali",
            "st" => "Southern Sotho",
            "es" => "Spanish - español",
            "es-AR" => "Spanish (Argentina) - español (Argentina)",
            "esLA" => "Spanish (Latin America) - español (Latinoamérica)",
            "es-MX" => "Spanish (Mexico) - español (México)",
            "es-ES" => "Spanish (Spain) - español (España)",
            "es-US" => "Spanish (United States) - español (Estados Unidos)",
            "su" => "Sundanese",
            "sw" => "Swahili - Kiswahili",
            "sv" => "Swedish - svenska",
            "tg" => "Tajik - тоҷикӣ",
            "ta" => "Tamil - தமிழ்",
            "tt" => "Tatar",
            "te" => "Telugu - తెలుగు",
            "th" => "Thai - ไทย",
            "ti" => "Tigrinya - ትግርኛ",
            "to" => "Tongan - lea fakatonga",
            "tr" => "Turkish - Türkçe",
            "tk" => "Turkmen",
            "tw" => "Twi",
            "uk" => "Ukrainian - українська",
            "ur" => "Urdu - اردو",
            "ug" => "Uyghur",
            "uz" => "Uzbek - o‘zbek",
            "vi" => "Vietnamese - Tiếng Việt",
            "wa" => "Walloon - wa",
            "cy" => "Welsh - Cymraeg",
            "fy" => "Western Frisian",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba - Èdè Yorùbá",
            "zu" => "Zulu - isiZulu",
        );
        return array_key_exists($key, $languages) ? $languages[$key] : $key;
    }

    public static function upload(string $dir, string $format = APPLICATION_IMAGE_FORMAT, array|object|null $image = null) {
        if (!$image) {
            return null;
        }

        set_time_limit(300);

        $dir = rtrim($dir, '/') . '/';
        $sourcePath = $image instanceof UploadedFile
            ? $image->getRealPath()
            : $image;

        $info = @getimagesize($sourcePath);
        if (!$info || empty($info['mime'])) {
            return false;
        }

        $mime = strtolower($info['mime']);

        // Detect format safely
        $format = match ($mime) {
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
            default      => $format,
        };

        $imageName = Carbon::now()->format('Y-m-d') . '-' . uniqid() . '.' . $format;

        // Ensure directory exists
        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $savePath = storage_path("app/public/{$dir}{$imageName}");

        /**
         * 🚨 IMPORTANT
         * Never process GIF with GD (animation will break)
         */
        if ($mime === 'image/gif') {
            return copy($sourcePath, $savePath) ? $imageName : false;
        }

        /**
         * WEBP copy-only if already webp
         */
        if ($mime === 'image/webp' && $format === 'webp') {
            return copy($sourcePath, $savePath) ? $imageName : false;
        }

        /**
         * Create GD image
         */
        $gdImage = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png'  => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default      => false,
        };

        if (!$gdImage) {
            return false;
        }

        /**
         * Preserve transparency
         */
        if (in_array($mime, ['image/png', 'image/webp'])) {
            imagealphablending($gdImage, false);
            imagesavealpha($gdImage, true);
        }

        /**
         * Resize logic
         */
        $maxSize = 2500;
        $width   = imagesx($gdImage);
        $height  = imagesy($gdImage);

        if ($width > $maxSize || $height > $maxSize) {
            $ratio = min($maxSize / $width, $maxSize / $height);
            $newW  = (int)($width * $ratio);
            $newH  = (int)($height * $ratio);

            $temp = imagecreatetruecolor($newW, $newH);

            if (in_array($mime, ['image/png', 'image/webp'])) {
                imagealphablending($temp, false);
                imagesavealpha($temp, true);
            }

            imagecopyresampled(
                $temp,
                $gdImage,
                0,
                0,
                0,
                0,
                $newW,
                $newH,
                $width,
                $height
            );

            imagedestroy($gdImage);
            $gdImage = $temp;
        }

        /**
         * Save image
         */
        $saved = match ($format) {
            'jpg', 'jpeg' => imagejpeg($gdImage, $savePath, 85),
            'png'         => imagepng($gdImage, $savePath, -1),
            'webp'        => imagewebp($gdImage, $savePath, 78),
            default       => false,
        };

        imagedestroy($gdImage);

        return $saved ? $imageName : false;
    }

    public static function uploadFile(string $dir, UploadedFile $file)
    {
        $dir = rtrim($dir, '/') . '/';
        $fileName = date('Y-m-d') . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        $file->storeAs("public/{$dir}", $fileName);

        return $fileName;
    }

    public static function update(string $dir, $old_image, string $format, array|object|null $image = null)
    {
        if (Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        $imageName = Helpers::upload($dir, $format, $image);
        return $imageName;
    }

    public static function delete($full_path)
    {
        if (Storage::disk('public')->exists($full_path)) {
            Storage::disk('public')->delete($full_path);
        }
        return [
            'success' => 1,
            'message' => 'Removed successfully !'
        ];
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        if (is_bool(env($envKey))) {
            $oldValue = var_export(env($envKey), true);
        } else {
            $oldValue = env($envKey);
        }

        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);

        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }

    public static function requestSender()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt_array($curl, array(
            CURLOPT_URL => route(base64_decode('YWN0aXZhdGlvbi1jaGVjaw==')),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        $data = json_decode($response, true);
        return $data;
    }

    public static function pagination_limit()
    {
        $pagination_limit = BusinessSetting::where('key', 'pagination_limit')->first();
        return $pagination_limit->value;
    }

    public static function remove_invalid_charcaters($str)
    {
        return str_ireplace(['\'', '"', ',', ';', '<', '>', '?'], ' ', $str);
    }

    public static function file_remover(string $dir, $image)
    {
        if (!isset($image)) return true;

        if (Storage::disk('public')->exists($dir . $image)) Storage::disk('public')->delete($dir . $image);

        return true;
    }

    public static function onErrorImage($data, $src, $error_src ,$path)
    {
        if(isset($data) && strlen($data) >1 && Storage::disk('public')->exists($path.$data)){
            return $src;
        }
        return $error_src;
    }

    public static function get_delivery_charge($branchId, int|float|string|null $distance = null, int|string|null $selectedDeliveryArea = null)
    {
        $branch = Branch::with(['delivery_charge_setup', 'delivery_charge_by_area'])
            ->where(['id' => $branchId])
            ->first(['id', 'name', 'status']);

        $deliveryType = $branch->delivery_charge_setup->delivery_charge_type ?? 'fixed';
        $deliveryType = $deliveryType === 'area' ? 'area' : ($deliveryType === 'distance' ? 'distance' : 'fixed');

        if($deliveryType == 'area'){
            $area = DeliveryChargeByArea::find($selectedDeliveryArea);
            $deliveryCharge = $area->delivery_charge ?? 0;
        }elseif($deliveryType == 'distance') {
            $minDeliveryCharge = $branch->delivery_charge_setup->minimum_delivery_charge;
            $shippingChargePerKM = $branch->delivery_charge_setup->delivery_charge_per_kilometer;
            $minDistanceForFreeDelivery = $branch->delivery_charge_setup->minimum_distance_for_free_delivery;

            if ($distance < $minDistanceForFreeDelivery) {
                $deliveryCharge = 0;
            } else {
                $distanceDeliveryCharge = $shippingChargePerKM * $distance;
                $deliveryCharge = max($distanceDeliveryCharge, $minDeliveryCharge);
            }
        }else{
            $deliveryCharge = $branch->delivery_charge_setup->fixed_delivery_charge ?? 0;
        }
        return $deliveryCharge;
    }

    public static function trimWords($text, $limit = 50)
    {
        $words = explode(' ', strip_tags($text));
        $wordCount = count($words);
        if ($wordCount <= $limit || $limit == 0) {
            return [
                'text' => implode(' ', $words),
                'isTruncated' => false
            ];
        }
        return [
            'text' => implode(' ', array_slice($words, 0, $limit)) . '...',
            'isTruncated' => true
        ];
    }


    public static function paginateValueNumberOptions(?int $custom = null): array
    {
        $allowedNumberOptions = [5, 10, 20, 30, 40, 50, 100, (int) Helpers::getPagination()];

        if ($custom) {
            $allowedNumberOptions[] = (int) $custom;
        }

        $uniqueAllowedNumberOptions = array_unique($allowedNumberOptions);
        sort($uniqueAllowedNumberOptions);

        return $uniqueAllowedNumberOptions;
    }


}

function translate($key)
{
    $local = session()->has('local') ? session('local') : 'en';
    App::setLocale($local);
    $lang_array = include(base_path('resources/lang/' . $local . '/messages.php'));
    $processed_key = ucfirst(str_replace('_', ' ', Helpers::remove_invalid_charcaters($key)));
    if (!array_key_exists($key, $lang_array)) {
        $lang_array[$key] = $processed_key;
        $str = "<?php return " . var_export($lang_array, true) . ";";
        file_put_contents(base_path('resources/lang/' . $local . '/messages.php'), $str);
        $result = $processed_key;
    } else {
        $result = __('messages.' . $key);
    }
    return $result;
}

