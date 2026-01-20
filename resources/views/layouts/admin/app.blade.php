<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title')</title>
    @php($icon = \App\Models\BusinessSetting::where(['key' => 'fav_icon'])->first()->value)
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/app/public/ecommerce/' . $icon ?? '') }}">
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/font/open-sans.css')}}">

    <link rel="stylesheet" href="{{asset('public/assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/intltelinput/css/intlTelInput.css')}}">


    <link rel="stylesheet" href="{{asset('public/assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link href="{{asset('public/assets/admin/css/dropzone.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/custom.css')}}">
    <link rel="stylesheet" href="{{asset('public/css/demo.css')}}">

    @stack('css_or_js')

    <script src="{{asset('public/assets/admin/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/assets/admin/css/toastr.css')}}">
</head>

<body class="footer-offset {{ env('APP_MODE')=='demo'?'demo':'' }}" id="{{ env('APP_MODE') == 'demo' ? 'demo' : '' }}">


<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" class="d--none">
                <div class="loader-wrap">
                    <img width="200" src="{{asset('public/assets/admin/img/loader.gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.admin.partials._front-settings')

@include('layouts.admin.partials._header')
@include('layouts.admin.partials._sidebar')

<main id="content" role="main" class="main pointer-event">
@yield('content')

@include('layouts.admin.partials._footer')

    <div class="modal fade" id="popup-modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="text-center">
                                <h2>
                                    <i class="tio-shopping-cart-outlined"></i> {{translate('You have new order, Check Please.')}}
                                </h2>
                                <hr>
                                <button class="btn btn-warning mr-3 ignore-order">{{translate('Ignore for now')}}</button>
                                <button class="btn btn-primary check-order">{{translate('Ok, let me check')}}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<span id="message-send-successfully" data-text="{{ translate('Okay') }}"></span>
<script src="{{asset('public/assets/admin/js/custom.js')}}"></script>
<span class="system-default-country-code" data-value="{{ \App\CentralLogics\Helpers::get_business_settings('country') ?? 'us' }}"></span>
<span class="image-file-size-data-to-js"
      data-max-upload-size-for-image="{{ readableUploadMaxFileSize('image') }}"
      data-max-upload-size-for-file="{{ readableUploadMaxFileSize('file') }}"
      data-post-max-size="{{ convertToReadableSize(convertToBytes(ini_get('post_max_size'))) }}"
      data-allowed-extensions=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}"
></span>
@stack('script')
<script src="{{asset('public/assets/admin/js/vendor.min.js')}}"></script>
<script src="{{asset('public/assets/admin/js/theme.min.js')}}"></script>
<script src="{{asset('public/assets/admin/js/sweet_alert.js')}}"></script>
<script src="{{asset('public/assets/admin/js/toastr.js')}}"></script>
<script src="{{asset('public/assets/admin/intltelinput/js/intlTelInput.min.js')}}"></script>
<script src="{{ asset('public/assets/admin/js/country-picker-init.js') }}"></script>
<script src="{{ asset('public/assets/admin/js/file-size-type-validation-with-compress.js') }}"></script>

<script>
    $(function () {
        $(document).on('click', '.toggle-password_custom .eye-on, .toggle-password_custom .eye-off', function () {
            let $wrap = $(this).closest('.toggle-password_custom');
            let $input = $wrap.find('.toggle-password_input');

            let isPassword = $input.attr('type') === 'password';
            $input.attr('type', isPassword ? 'text' : 'password');

            $wrap.find('.eye-on').toggle(!isPassword);
            $wrap.find('.eye-off').toggle(isPassword);
        });
    });
</script>
<script>



</script>

<script>
// $('#add-customer').on('show.bs.modal', function () {
//     setTimeout(function () {
//         $('.select2-dropdown').hide();
//         $('.select2').select2('close');
//     }, 0);
// });



</script>
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
<script>
    $(document).on('ready', function () {

        if (window.localStorage.getItem('hs-builder-popover') === null) {
            $('#builderPopover').popover('show')
                .on('shown.bs.popover', function () {
                    $('.popover').last().addClass('popover-dark')
                });

            $(document).on('click', '#closeBuilderPopover', function () {
                window.localStorage.setItem('hs-builder-popover', true);
                $('#builderPopover').popover('dispose');
            });
        } else {
            $('#builderPopover').on('show.bs.popover', function () {
                return false
            });
        }

        $('.js-navbar-vertical-aside-toggle-invoker').click(function () {
            $('.js-navbar-vertical-aside-toggle-invoker i').tooltip('hide');
        });

        let sidebar = $('.js-navbar-vertical-aside').hsSideNav();

        $('.js-nav-tooltip-link').tooltip({boundary: 'window'})

        $(".js-nav-tooltip-link").on("show.bs.tooltip", function (e) {
            if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
                return false;
            }
        });

        $('.js-hs-unfold-invoker').each(function () {
            var unfold = new HSUnfold($(this)).init();
        });

        $('.js-form-search').each(function () {
            new HSFormSearch($(this)).init()
        });

        $('.js-select2-custom').each(function () {
            let $el = $(this);

            let select2 = $.HSCore.components.HSSelect2.init($el, {
                sorter: function(data) {
                let term = $('.select2-search__field').val() || '';

                if (!term.trim()) {
                    return data;
                }

                let termLower = term.toLowerCase();

                return data.sort(function(a, b) {
                    let aText = a.text.toLowerCase();
                    let bText = b.text.toLowerCase();

                    let aStarts = aText.startsWith(termLower) ? -1 : 0;
                    let bStarts = bText.startsWith(termLower) ? -1 : 0;

                    if (aStarts !== bStarts) {
                    return aStarts - bStarts;
                    }

                    return aText.localeCompare(bText);
                });
                }
            });
        });


        $('.js-daterangepicker').daterangepicker();

        $('.js-daterangepicker-times').daterangepicker({
            timePicker: true,
            startDate: moment().startOf('hour'),
            endDate: moment().startOf('hour').add(32, 'hour'),
            locale: {
                format: 'M/DD hh:mm A'
            }
        });

        let start = moment();
        let end = moment();

        $('.js-clipboard').each(function () {
            let clipboard = $.HSCore.components.HSClipboard.init(this);
        });
    });
</script>
<script>
    $(document).on('ready', function () {
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });
</script>

@stack('script_2')
<audio id="myAudio">
    <source src="{{asset('public/assets/admin/sound/notification.mp3')}}" type="audio/mpeg">
</audio>

<script>
    let audio = document.getElementById("myAudio");

    function playAudio() {
        audio.play();
    }

    function pauseAudio() {
        audio.pause();
    }
</script>
<script>
    setInterval(function () {
        $.get({
            url: '{{route('admin.get-restaurant-data')}}',
            dataType: 'json',
            success: function (response) {
                let data = response.data;
                if (data.new_order > 0) {
                    playAudio();
                    $('#popup-modal').appendTo("body").modal('show');
                }
            },
        });
    }, 10000);

    $('.ignore-order').on('click', function (){
        location.href = '{{route('admin.ignore-check-order')}}';
    })

    $('.check-order').on('click', function (){
        location.href = '{{route('admin.orders.list',['status'=>'all'])}}';
    })

    $('.route-alert').on('click', function (){
        let route = $(this).data('route');
        let message = $(this).data('message');
        route_alert(route, message)
    });

    function route_alert(route, message) {
        Swal.fire({
            title: '{{translate("Are you sure?")}}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#673ab7',
            cancelButtonText: '{{translate("No")}}',
            confirmButtonText:'{{translate("Yes")}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = route;
            }
        })
    }

    $('.form-alert').on('click', function (){
        let id = $(this).data('id');
        let message = $(this).data('message');
        form_alert(id, message)
    });

    function form_alert(id, message) {
        Swal.fire({
            title:'{{translate("Are you sure?")}}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#673ab7',
            cancelButtonText: '{{translate("No")}}',
            confirmButtonText: '{{translate("Yes")}}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $('#'+id).submit()
            }
        })
    }

    function call_demo(){
        toastr.info('Disabled for demo version!')
    }

    $('.change-status').on('click', function (){
        location.href = $(this).data('route');
    });

    let initialImages = [];
    $(window).on('load', function() {
        $("form").find('img').each(function (index, value) {
            initialImages.push(value.src);
        })
    })

    $(document).ready(function() {
        $('form').on('reset', function(e) {
            $("form").find('img').each(function (index, value) {
                $(value).attr('src', initialImages[index]);
            })
        });
    });

    $('.demo-form-submit').click(function() {
        if ('{{ env('APP_MODE') }}' === 'demo') {
            call_demo();
        }
    });

</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        replaceEmailsAndPhoneNumbersWithAsterisks(document.getElementById('demo'));

        // Disable mailto links
        document.querySelectorAll('a[href^="mailto:"]').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
            });
        });
        // Disable tel links
        document.querySelectorAll('a[href^="tel:"]').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
            });
        });
    });

    function replaceEmailsAndPhoneNumbersWithAsterisks(element) {
        if (element.nodeType === 3) {
            element.nodeValue = element.nodeValue
                .replace(/\b([A-Za-z0-9._%+-])[^@]*@([A-Za-z0-9.-]+)\.([A-Z|a-z]{2,})\b/g, function (match, firstLetter, domain, tld) {
                    var remainingAsterisks = '*'.repeat(Math.min(10, match.length - 1));
                    return firstLetter + remainingAsterisks + '@' + domain + '.' + tld;
                })
                .replace(/\b(?:\+\d{1,3}\s?)?\d{1,4}[-.\s]?\d{5,}\b/g, function (match) {
                    if (match.length >= 8) {
                        var firstChar = match.charAt(0) === '+' ? '+' : match.charAt(0);
                        var remainingAsterisks = '*'.repeat(Math.min(10, match.length - (firstChar === '+' ? 2 : 1)));
                        return firstChar + remainingAsterisks;
                    } else {
                        return match;
                    }
                });
        } else if (element.nodeType === 1) {
            for (var i = 0; i < element.childNodes.length; i++) {
                replaceEmailsAndPhoneNumbersWithAsterisks(element.childNodes[i]);
            }
        }
    }
</script>

{{--termalprint--}}
<script>
    $('.invoice-printing').on('click', function (){
        let orderId = $(this).data('id');
        print_invoice(orderId);
    })

    function print_invoice(order_id) {
        $.get({
            url: '{{url('/')}}/admin/pos/invoice/'+order_id,
            dataType: 'json',
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (data) {
                $('#print-invoice').modal('show');
                $('#printableArea').empty().html(data.view);
            },
            complete: function () {
                $('#loading').hide();
            },
        });
    }

    function round(value, decimals) {
        return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
    }


    // $('.print-div-button').on('click', function (){
    //     let name = $(this).data('name');
    //     printDiv(name);
    // })
    //
    // function printDiv(divName) {
    //     let printContents = document.getElementById(divName).innerHTML;
    //     document.body.innerHTML = printContents;
    //     window.print();
    //     location.reload();
    // }
</script>

<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('public/assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
