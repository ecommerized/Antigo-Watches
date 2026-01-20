<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="nofollow, noindex, max-snippet:1, max-video-preview:1, max-image-preview:standard">
    <!-- Title -->
    <title>{{ translate('Admin') }} | {{ translate('Login') }}</title>

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
<main id="content" role="main" class="main">
    <div class="d-flex flex-column flex-md-row min-vh-100">
        <div class="d-none d-md-flex justify-content-center flex-grow-1 bg-light login-bg-box md-w-50" data-bg-img="{{asset('public/assets/admin/img/login_bg.png')}}">
            <div class="login-left-content p-3">
                <a class="d-flex mb-4" href="javascript:">
                    <img class="z-index-2 height-60px"
                        src="{{ $logo }}"
                        alt="{{ translate('Image Description') }}">
                </a>

                <h3 class="mb-0">{{ translate('Your') }} <br /> {{ translate('All Service') }}</h3>
                <h2 class="text-primary font-weight-bold">{{ translate('in one field') }}....</h2>
            </div>
        </div>
        <div class="flex-grow-1 bg-white d-flex justify-content-center md-w-50">
            <div class="card-content-wrap pb-5 pb-md-0">
                <div class="card-body">
                    <div class="software-version d-flex justify-content-end">
                        <label class="badge badge-soft-success __login-badge text-primary">{{ translate('Software version') }} : {{ env('SOFTWARE_VERSION') }}</label>
                    </div>

                    <form id="form-id" action="{{route('admin.auth.login')}}" method="post">
                        @csrf

                        <div>
                            <div class="mb-5">
                                <h3 class="display-4"> {{translate('sign_in')}}</h3>
                                <p>{{translate('want to login your branches')}}
                                    ?
                                    <a href="{{route('branch.auth.login')}}">
                                        {{translate('branch')}} {{translate('login')}}
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div class="js-form-message form-group">
                            <label class="input-label text-capitalize"
                                    for="signinSrEmail">{{translate('your')}} {{translate('email')}}</label>

                            <input type="email" class="form-control form-control-lg" name="email" id="signinSrEmail"
                                    tabindex="1" placeholder="{{ translate('email@address.com') }}" aria-label="email@address.com"
                                    required data-msg="Please enter a valid email address.">
                        </div>

                        <div class="js-form-message form-group">
                            <label class="input-label" for="signupSrPassword" tabindex="0">
                                <span class="d-flex justify-content-between align-items-center">
                                    {{translate('password')}}
                                </span>
                            </label>

                            <div class="input-group input-group-merge">
                                <input type="password" class="js-toggle-password form-control form-control-lg"
                                        name="password" id="signupSrPassword" placeholder="8+ characters required"
                                        aria-label="{{ translate('8+ characters required') }}" required
                                        data-msg="{{ translate('Your password is invalid. Please try again.') }}"
                                        data-hs-toggle-password-options='{
                                            "target": "#changePassTarget",
                                            "defaultClass": "tio-hidden-outlined",
                                            "showClass": "tio-visible-outlined",
                                            "classChangeTarget": "#changePassIcon"
                                        }'>
                                <div id="changePassTarget" class="input-group-append">
                                    <a class="input-group-text" href="javascript:">
                                        <i id="changePassIcon" class="tio-visible-outlined"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="termsCheckbox" name="remember">
                                <label class="custom-control-label text-muted" for="termsCheckbox">
                                    {{translate('remember')}} {{translate('me')}}
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            @php($recaptcha = Helpers::get_business_settings('recaptcha'))
                            @if(isset($recaptcha) && $recaptcha['status'] == 1)
                                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

                                <input type="hidden" name="set_default_captcha" id="set_default_captcha_value" value="0" >
                                <div class="row d-none" id="reload-captcha">
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-lg" name="default_captcha_value" value=""
                                               placeholder="{{translate('Enter captcha value')}}" autocomplete="off">
                                    </div>
                                    <div class="col-6">
                                        <a>
                                            <img src="{{ route('admin.auth.default-captcha', ['tmp' => rand()]) }}" class="input-field rounded h-54px"
                                                 id="default_recaptcha_id" alt="{{ translate('image') }}">
                                            <i class="tio-refresh icon"></i>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-lg" name="default_captcha_value" value=""
                                                placeholder="{{translate('Enter captcha value')}}" autocomplete="off">
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" class="captcha_refresh">
                                            <img src="{{ route('admin.auth.default-captcha', ['tmp' => rand()]) }}" class="input-field rounded h-54px"
                                                 id="default_recaptcha_id" alt="{{ translate('image') }}">
                                            <i class="tio-refresh icon captcha_refresh"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-block btn-primary" id="signInBtn">{{translate('login')}}</button>
                    </form>

                    @if(env('APP_MODE')=='demo')
                    <div class="login-footer d-flex justify-content-between mt-4 border-top pt-3">
                        <div class="font-weight-medium">
                            <div>{{ translate('Email : admin@admin.com') }}</div>
                            <div>{{ translate('Password : 12345678') }}</div>
                        </div>
                        <button type="button" class="btn btn-primary login-copy" id="copyButton">
                            <i class="tio-copy"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="{{asset('public/assets/admin/js/vendor.min.js')}}"></script>
<script src="{{asset('public/assets/admin/js/theme.min.js')}}"></script>
<script src="{{asset('public/assets/admin/js/toastr.js')}}"></script>
{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script src="https://www.google.com/recaptcha/api.js?render={{$recaptcha['site_key']}}"></script>
@endif

<script>
    "use strict";

    $(document).on('ready', function () {
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });

        let $bgImg = $("[data-bg-img]");
        $bgImg.css("background-image", function () {
            return 'url("' + $(this).data("bg-img") + '")';
        }).removeAttr("data-bg-img").addClass("bg-img");
    });

    @if(isset($recaptcha) && $recaptcha['status'] == 1)
        $(document).ready(function() {
            $('#signInBtn').click(function (e) {

                if( $('#set_default_captcha_value').val() == 1){
                    $('#form-id').submit();
                    return true;
                }

                e.preventDefault();

                if (typeof grecaptcha === 'undefined') {
                    toastr.error('Invalid recaptcha key provided. Please check the recaptcha configuration.');

                    $('#reload-captcha').removeClass('d-none');
                    $('#set_default_captcha_value').val('1');

                    return;
                }
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ $recaptcha['site_key'] }}', {
                        action: 'submit'
                    }).then(function(token) {
                        $('#g-recaptcha-response').val(token);
                        $('#form-id').submit();
                    });
                });

                window.onerror = function(message) {
                    var errorMessage = 'An unexpected error occurred. Please check the recaptcha configuration';
                    if (message.includes('Invalid site key')) {
                        errorMessage = 'Invalid site key provided. Please check the recaptcha configuration.';
                    } else if (message.includes('not loaded in api.js')) {
                        errorMessage = 'reCAPTCHA API could not be loaded. Please check the recaptcha API configuration.';
                    }

                    $('#reload-captcha').removeClass('d-none');
                    $('#set_default_captcha_value').val('1');

                    toastr.error(errorMessage)
                    return true;
                };
            });
        });

    @endif

    $('.captcha_refresh').on('click', function() {
        re_captcha();
    });

    function re_captcha() {
        const captchaImg = document.getElementById('default_recaptcha_id');
        captchaImg.src = `{{ url('/admin/auth/code/captcha') }}/${Math.random()}`;
    }



    $('#copyButton').on('click', function() {
        copy_cred();
    });

    @if(env('APP_MODE')=='demo')
        function copy_cred() {
            $('#signinSrEmail').val('admin@admin.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
            CloseButton: true,
            ProgressBar: true
        });
        }
   @endif
</script>

<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('public/assets/admin/vendor/babel-polyfill/polyfill.min.js')}}"><\/script>');
</script>
</body>
</html>
