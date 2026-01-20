<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ translate('Error 404') }} | {{ Helpers::get_business_settings('restaurant_name') }}</title>
    @php($icon = \App\Models\BusinessSetting::where(['key' => 'fav_icon'])->first()->value)
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/app/public/ecommerce/' . $icon ?? '') }}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/font/open-sans.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/theme.minc619.css?v=1.0')}}">
</head>

<body>

<div class="container">
    <div class="footer-height-offset d-flex justify-content-center align-items-center flex-column">
        <div class="row align-items-sm-center w-100">
            <div class="col-sm-6">
                <div class="text-center text-sm-right mr-sm-4 mb-5 mb-sm-0">
                    <img class="w-60 w-sm-100 mx-auto max-w-15rem"
                         src="{{asset('public/assets/admin/svg/illustrations/think.svg')}}" alt="{{ translate('image') }}"
                         style="max-width: 15rem;">
                </div>
            </div>

            <div class="col-sm-6 col-md-4 text-center text-sm-left">
                <h1 class="display-1 mb-0">404</h1>
                <p class="lead">{{ translate('Sorry, the page you are looking for cannot be found.') }}</p>
                @if(auth('branch')->check())
                    <a class="btn btn-primary" href="{{route('branch.dashboard')}}">{{ translate('Dashboard') }}</a>
                @else
                    <a class="btn btn-primary" href="{{route('admin.dashboard')}}">{{ translate('Dashboard') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="footer text-center">
    <ul class="list-inline list-separator">
        <li class="list-inline-item">
            <a class="list-separator-link" target="_blank" href="<?php echo url('/') ?>">{{ Helpers::get_business_settings('restaurant_name') }} {{ translate('Support') }}</a>
        </li>
    </ul>
</div>


<script src="{{asset('public/assets/admin//js/theme.min.js')}}"></script>
</body>

</html>
