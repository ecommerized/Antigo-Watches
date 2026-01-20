<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{ translate('Maintenance Mode') }}</title>

    @php($icon = Helpers::get_business_settings('fav_icon'))
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/app/public/ecommerce/' . $icon ?? '') }}">
    <link rel="shortcut icon" href="#">

    <link rel="stylesheet" href="{{asset('public/assets/admin/css/font/open-sans.css')}}">

    <link rel="stylesheet" href="{{asset('public/assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/vendor/icon-set/style.css')}}">

    <link rel="stylesheet" href="{{asset('public/assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/toastr.css')}}">
</head>

<body>

    <div class="container">
        <div class="text-center my-5">
            <img class="mt-5" src="{{ asset('public/assets/admin/img/maintenance.png') }}" alt="{{ translate('maintenance') }}">
        </div>
        <div class="text-center my-5">
            <h2>{{$exception->getHeaders()['maintenanceMessage'] ?? '' }}</h2>
            <p>{{ $exception->getHeaders()['messageBody'] ?? '' }}</p>
        </div>
        <div class="text-center my-5">
            @if($exception->getHeaders()['businessNumber'] || $exception->getHeaders()['businessEmail'] )
                <h6>{{ translate('Any query? Feel free to call or mail Us') }}</h6>
                @if($exception->getHeaders()['businessNumber'])
                    <div>
                        <a href="tel:{{\App\CentralLogics\Helpers::get_business_settings('phone')}}">{{ \App\CentralLogics\Helpers::get_business_settings('phone') }}</a>
                    </div>
                @endif
                @if($exception->getHeaders()['businessEmail'])
                    <div>
                        <a href="mailto:{{\App\CentralLogics\Helpers::get_business_settings('email_address')}}">{{ \App\CentralLogics\Helpers::get_business_settings('email_address') }}</a>
                    </div>
                @endif
            @endif
        </div>
    </div>

</body>
</html>
