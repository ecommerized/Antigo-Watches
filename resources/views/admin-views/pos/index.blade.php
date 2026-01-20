@extends('layouts.admin.app')
@section('title', translate('POS'))

@section('content')
    <div class="content container-fluid">
        <div class="row gy-3 gx-2">
            <div class="col-lg-7">
                <div class="card overflow-hidden card-h-100vh">
                    <div class="pos-title">
                        <h4 class="mb-0">{{translate('Product_Section')}}</h4>
                    </div>

                    <div class="d-flex flex-wrap flex-md-nowrap justify-content-between gap-3 gap-xl-4 px-4 py-4">
                        <div class="w-100 mr-xl-2">
                            <select name="category" id="category" class="form-control js-select2-custom mx-1"
                                    title="{{translate('select category')}}" onchange="set_category_filter(this.value)">
                                <option value="">{{translate('All Categories')}}</option>
                                @foreach ($categories as $item)
                                    <option
                                        value="{{$item->id}}" {{$category==$item->id?'selected':''}}>{{$item->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-100 mr-xl-2">
                            <form id="search-form" class="header-item">
                                <div class="input-group input-group-merge input-group-flush border rounded">
                                    <div class="input-group-prepend pl-2">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch" type="search" value="{{$keyword?$keyword:''}}"
                                           name="search"
                                           class="form-control border-0 pr-2"
                                           placeholder="{{translate('Search here')}}"
                                           aria-label="Search here">
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body pt-0" id="items">
                        <div class="pos-item-wrap justify-content-center">
                            <?php
                                $cartProducts = collect(session()->get('cart', []))->filter(fn($value, $key) => is_array($value))->values();
                            ?>
                            @foreach($products as $product)
                                @include('admin-views.pos._single_product',['product'=>$product, 'cartProducts' => $cartProducts])
                            @endforeach
                        </div>
                    </div>
                    <div class="px-3 d-flex justify-content-end">
                        {!!$products->withQueryString()->links()!!}
                    </div>
                    @if(count($products)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 width-7rem"
                                 src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}"
                                 alt="{{ translate('image') }}">
                            <p class="mb-0">{{ translate('No data to show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card card-h-100vh">
                    <div class="pos-title">
                        <h4 class="mb-0">{{translate('Billing_Section')}}</h4>
                    </div>
                    <div class="p-2 p-sm-4 max-h-100 overflow-y-auto">
                        <div class="bg-color-common rounded p-10px mb-20">
                            <div class="form-group d-flex gap-2 m-0 position-relative">
                                <i class="tio-search position-absolute search-icon-select"></i>
                                <select id='customer' name="customer_id" onchange="store_key('customer_id',this.value)"
                                        data-placeholder="{{translate('Walk In Customer')}}"
                                        class="js-data-example-ajax-2 form-control js-select2-custom customer-select-index m-1 border">
                                    @foreach($users as $user)
                                        <option
                                            value="{{$user['id']}}" {{ session()->get('customer_id') == $user['id'] ? 'selected' : '' }}>{{$user['f_name']. ' '. $user['l_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <?php
                            $customer = App\Models\User::where('id', session()->get('customer_id'))->with('userAccount')->get()->map(function ($data) {
                                return [...$data->toArray(), 'balance' => Helpers::set_symbol($data->userAccount->wallet_balance ?? 0)];
                            })->first();
                            $balance = \App\Models\User::with('userAccount')->where('id', session()->get('customer_id'))->first()?->userAccount?->wallet_balance ?? 0
                            ?>
                            <div class="bg-white rounded p-3 customer-details mt-2 {{ ($customer && $customer['id'] !=0) ? '' : 'd-none' }}">
                                <div class="d-flex flex-column gap-1">
                                    <div class="d-flex align-items-center justify-content-between gap-1 flex-wrap">
                                        <div class="d-flex align-items-center text-title2 gap-3">
                                            <span class="fs-13 min-w-6 w-50px min-w-50px">{{ translate('Name') }}</span>
                                            <span>:</span> <span
                                                class="text-title fs-14 pos-customer-name">{{ $customer['f_name'] ?? '' }} {{ $customer['l_name'] ?? '' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center text-title2 gap-3">
                                        <span class="fs-13 min-w-6 w-50px min-w-50px">{{ translate('Contact') }}</span> <span>:</span>
                                        <span
                                            class="text-title fs-14 pos-customer-phone">{{ $customer['phone'] ?? '' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center text-title2 gap-3">
                                        <span class="fs-13 min-w-6 w-50px min-w-50px">{{ translate('Email') }}</span> <span>:</span>
                                        <span
                                            class="text-title fs-14 pos-customer-email">{{ $customer['email'] ?? '' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center text-title2 gap-3">
                                        <span class="fs-13 min-w-6 w-50px min-w-50px">{{ translate('Wallet') }} </span> <span>:</span>
                                        <span
                                            class=" fs-14 font-weight-bold text-primary pos-customer-wallet" data-customer-wallet="{{ $balance }}">{{ $customer['balance'] ?? \App\CentralLogics\Helpers::set_symbol(0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class=" mb-20">
                            <div class="form-group d-flex m-0">
                                <select onchange="store_key('branch_id',this.value)" id='branch' name="branch_id"
                                        class="js-data-example-ajax-2 form-control js-select2-custom">
                                    @foreach($branches as $branch)
                                        <option
                                            value="{{$branch['id']}}" {{ session()->get('branch_id') == $branch['id'] ? 'selected' : '' }}>{{$branch['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="cart">
                            @include('admin-views.pos._cart')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal cmn__quick-modal fade" id="quick-view" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="--modal-mxwidth: 650px; max-width: 650px !important;">
            <div class="modal-content" id="quick-view-modal">

            </div>
        </div>
    </div>

    <div class="modal fade" id="add-customer" tabindex="-1">
        <div class="modal-dialog max-w-650px modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{translate('Customer Info')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body pt-3">
                    <form action="{{route('admin.pos.customer-store')}}" method="post" id="customer-form">
                        @csrf
                        <div class="bg-color-common rounded py-3 px-3 mb-20">
                            <div class="row pl-2">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('First_Name')}}<span
                                                class="input-label-secondary text-danger">*</span></label>
                                        <input type="text" name="f_name" class="form-control" value=""
                                               placeholder="{{ translate('First name') }}" required="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Last_Name')}}<span
                                                class="input-label-secondary text-danger">*</span></label>
                                        <input type="text" name="l_name" class="form-control" value=""
                                               placeholder="{{ translate('Last name') }}" required="">
                                    </div>
                                </div>
                            </div>
                            <div class="row pl-2">
                                <div class="col-lg-6">
                                    <div class="form-group m-0">
                                        <label class="input-label">{{translate('Email')}}<span
                                                class="input-label-secondary text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" value=""
                                               placeholder="{{ translate('Ex : ex@example.com') }}" required="">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group m-0">
                                        <label for="phone_number"  class="input-label text-title d-block mb-2">{{translate('Phone Number')}}
                                            <span class="input-label-secondary text-danger">*</span>
                                            <i class="tio-info text-gray-info fs-12" data-toggle="tooltip" data-placement="top" title="{{ translate('Enter your contact number') }}"></i>
                                        </label>
                                        <input type="tel" pattern="[0-9]{1,14}" value="{{ old('phone') }}" id="phone_number"
                                               class="phone form-control bg-white w-100 overflow-hidden"
                                               placeholder="{{translate('Ex: xxxxx xxxxxx')}}" required>
                                        <input type="hidden" id="phone_number-hidden-element" name="phone">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" id="" class="btn min-w-120 btn--reset" data-dismiss="modal">{{translate('cancel')}}</button>
                            <button type="submit" id="" class="btn min-w-120 btn-primary">{{translate('Add')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-custom-position fade" id="couponAdd-discount" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="--modal-mxwidth: 600px; max-width: 600px !important;">
            <div class="modal-content">
                <div class="modal-header align-items-start">
                    <h4 class="modal-title text-title mr-3">{{translate('Coupon Discount')}}
                        <small class="fs-14 d-block">{{ translate('Select from available coupon or input code') }}</small>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-3">
                    <form action="{{ route('admin.pos.apply-coupon') }}" method="post" id="coupon_form">
                        @csrf
                        <div class="modal-bg-color p--20 rounded">
                            <div id="pos-coupon-list">
                                @include('admin-views.pos.partials.coupon-list')
                            </div>
                            <div class="form-group m-0">
                                <label for="coupon_code" class="text-title fs-14 mb-2">{{translate('Coupon Code')}}</label>
                                <input type="text" id="coupon_code" value="" class="form-control bg-white" placeholder="Enter coupon code">
                                <span class="error-text" data-error="pos_coupon_code"></span>
                            </div>
                            <input type="hidden" name="pos_coupon_code" value="{{ session()->get('cart')['coupon_code'] ?? ''}}">
                        </div>

                        <div class="d-flex justify-content-end gap-3 col-sm-12 mt-4">
                            <button type="button" class="btn min-w-120 btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                            <button class="btn min-w-120 btn-primary" type="submit">{{translate('Apply')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php($order=\App\Models\Order::find(session('last_order')))
    @if($order)
        @php(session(['last_order'=> false]))
        <div class="modal fade" id="print-invoice" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{translate('Print Invoice')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body row front-emoji">
                        <div class="col-md-12 text-center">
                            <div>
                                <input type="button" class="btn btn-primary non-printable" id="print-invoice-div"
                                       data-name="printableArea"
                                       value="{{translate('Proceed, If thermal printer is ready.')}}"/>
                                <a href="{{url()->previous()}}"
                                   class="btn btn-danger non-printable">{{translate('Back')}}</a>
                            </div>
                            <hr class="non-printable">
                        </div>
                        <div class="row m-auto" id="printableArea">
                            @include('admin-views.pos.order.invoice')
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script_2')
    <script type="text/javascript" src="{{asset('public/assets/admin/js/offcanvas.js')}}"></script>
    <script>
        "use strict";
        $(document).ready(function () {
            initializePhoneInput('#phone_number', '#phone_number-hidden-element');
        });
        $(document).ready(function () {
            @if($order)
                $('#print-invoice').modal('show');
            @endif

            // Initialize Select2 with all required options
            $('#customer.customer-select-index').select2({
                placeholder: "Select a customer",
                allowClear: true,
                dropdownCssClass: "select2-dropdown-index custom-select2-dropdown"
            })
            .on('change', function () {
                toggleClearButton();
            })
            .on('select2:open', function () {
                let dropdown = $('.select2-dropdown.custom-select2-dropdown');
                if (dropdown.find('.custom-add-button').length === 0) {
                    let $searchfield = dropdown.find('.select2-search.select2-search--dropdown');
                    let $button = $('<button type="button" class="custom-add-button d-flex align-items-center justify-content-end gap-1 btn p-0 border-0 text-base fs-14" style="width: 100%; margin-top: 12px; margin-bottom: 8px; text-decoration: underline" data-toggle="modal" data-target="#add-customer" title="Add Customer" id="add_new_customer">+ Add New Customer</button>');
                    $searchfield.append($button);
                }
            });

            toggleClearButton();

            function toggleClearButton() {
                let val = $('select[name="customer_id"]').val();
                if (val == 0 || val === null) {
                    $('.select2-selection__clear').addClass('d-none');
                    $('.customer-details').addClass('d-none');
                } else {
                    $('.select2-selection__clear').removeClass('d-none');
                }
            }
        });


        $("#print-invoice-div").on('click', function () {
            let name = $(this).data('name');
            printDiv(name);
        });

        function printDiv(divName) {
            let printContents = document.getElementById(divName).innerHTML;
            let originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }

        function set_category_filter(id) {
            let nurl = new URL('{!!url()->full()!!}');
            nurl.searchParams.set('category_id', id);
            location.href = nurl;
        }

        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            let keyword = $('#datatableSearch').val();
            let nurl = new URL('{!!url()->full()!!}');
            nurl.searchParams.set('keyword', keyword);
            location.href = nurl;
        });

        $('.pos-single-product-card').on('click', function () {
            let productId = $(this).data('id');
            quickView(productId);
        });

        function quickView(product_id) {
            $.ajax({
                url: '{{route('admin.pos.quick-view')}}',
                type: 'GET',
                data: {
                    product_id: product_id
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#quick-view').modal('show');
                    $('#quick-view-modal').empty().html(data.view);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        function checkAddToCartValidity() {
            return true;
        }

        $(document).on('click', '#add-to-cart-form .btn-number', function (e) {
            e.preventDefault();
            let $btn = $(this);
            let fieldName = $btn.attr('data-field');
            let type = $btn.attr('data-type');
            let $input = $("#add-to-cart-form input[name='" + fieldName + "']");
            let currentVal = parseInt($input.val());
            let $tooltip = $('.custom-tooltip');

            if (!isNaN(currentVal)) {
                if (type === 'minus') {
                    if (currentVal > $input.attr('min')) {
                        $input.val(currentVal - 1).trigger('change');
                        $tooltip.hide();
                        $("[data-type='plus'][data-field='" + fieldName + "']").prop('disabled', false);
                    }
                    if (parseInt($input.val()) <= $input.attr('min')) {
                        $btn.attr('disabled', true);
                    }
                } else if (type === 'plus') {
                    if (currentVal < parseInt($input.attr('max'))) {
                        $input.val(currentVal + 1).trigger('change');
                        $("[data-type='minus'][data-field='" + fieldName + "']").prop('disabled', false);
                    }
                    if (currentVal >= parseInt($input.attr('max'))) {
                        $tooltip.css('display', 'flex');
                        $btn.prop('disabled', true);
                    }
                }
            } else {
                $input.val(0);
            }
        });

        $(document).on('focusin', '#add-to-cart-form .input-number', function () {
            $(this).data('oldValue', $(this).val());
        });

        $(document).on('change', '#add-to-cart-form .input-number', function () {
            let $input = $(this);
            let name = $input.attr('name');
            let minValue = parseInt($input.attr('min')) || 0;
            let maxValue = parseInt($input.attr('max')) || 100;
            let valueCurrent = parseInt($input.val());
            let $tooltip = $('.custom-tooltip');

            if (isNaN(valueCurrent)) {
                $input.val($input.data('oldValue'));
                return;
            }
            if (valueCurrent <= minValue) {
                $input.val(minValue);
                $("[data-type='minus'][data-field='" + name + "']").attr('disabled', true);
                $("[data-type='plus'][data-field='" + name + "']").removeAttr('disabled');
            } else if (valueCurrent >= maxValue) {
                $input.val(maxValue);
                $("[data-type='plus'][data-field='" + name + "']").attr('disabled', true);
                $("[data-type='minus'][data-field='" + name + "']").removeAttr('disabled');
                $tooltip.css('display', 'flex');
            } else {
                $("[data-type='minus'][data-field='" + name + "']").removeAttr('disabled');
                $("[data-type='plus'][data-field='" + name + "']").removeAttr('disabled');
                $tooltip.hide();
            }
        });

        $(document).on('keydown', '#add-to-cart-form .input-number', function (e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) &&
                (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        function getVariantPrice(initial = false) {
            let $form = $('#add-to-cart-form');
            let $quantityInput = $('#quantity');
            let quantity = parseInt($quantityInput.val()) || 1;
            if (quantity <= 0 || !checkAddToCartValidity()) return;

            let formData = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: $('input[name="id"]').val(),
                quantity: quantity
            };

            $form.find('input[type=radio]:checked').each(function () {
                formData[$(this).attr('name')] = $(this).val();
            });

            if (initial) {
                $.ajax({
                    type: 'GET',
                    url: '{{ route('admin.pos.quick-view-modal-footer') }}',
                    data: formData,
                    success: function (data) {
                        $('#quick-view-modal-footer').html(data.view);
                        $form.find('.total-stock').text(data.stock);
                    },
                    error: function (xhr) {
                        console.error(xhr.responseJSON || xhr.responseText);
                    }
                });
            } else {
                $.ajax({
                    type: "POST",
                    url: '{{ route('admin.pos.variant_price') }}',
                    data: formData,
                    success: function (data) {
                        $('#chosen_price_div').removeClass('d-none');
                        $('#chosen_price').html(round(data.price, 2).toFixed(2));
                        $(".total-stock").html(data.stock);
                        $quantityInput.attr("max", data.stock);
                        if (parseInt($quantityInput.val()) > data.stock) {
                            $quantityInput.val(data.stock).trigger('change');
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseJSON || xhr.responseText);
                    }
                });
            }
        }

        function addToCart(form_id = 'add-to-cart-form') {
            if (checkAddToCartValidity()) {
                let formData = {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: $('input[name="id"]').val(),
                    quantity: $('input[name="quantity"]').val()
                };

                // Collect selected choices (radio inputs)
                $('[type=radio]:checked').each(function () {
                    formData[$(this).attr('name')] = $(this).val();
                });
                $.post({
                    url: '{{ route('admin.pos.add-to-cart') }}',
                    data: formData,
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (data) {
                        if (data.data == 1) {
                            Swal.fire({
                                icon: 'info',
                                title: '{{translate('Cart')}}',
                                confirmButtonText: '{{translate("Ok")}}',
                                text: "{{translate('Product already added in cart')}}"
                            });
                            return false;
                        } else if (data.data == 0) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{translate('Cart')}}',
                                confirmButtonText: '{{translate("Ok")}}',
                                text: '{{translate('Sorry, product out of stock')}}.'
                            });
                            return false;
                        }
                        $('.call-when-done').click();
                        toastr.success('{{translate('Item has been added in your cart')}}!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        updateUI($('input[name="id"]').val());
                    },
                    complete: function () {
                        $('#loading').hide();
                    }
                });
            } else {
                Swal.fire({
                    type: 'info',
                    title: '{{translate('Cart')}}',
                    confirmButtonText: '{{translate("Ok")}}',
                    text: '{{translate('Please choose all the options')}}'
                });
            }
        }

        function removeFromCart(key, el) {
            const $row = $(el).closest('tr');
            const productId = $row.data('product-id');

            $.post('{{ route('admin.pos.remove-from-cart') }}', {
                _token: '{{ csrf_token() }}',
                key: key
            }).done(function (data) {
                if (data.errors) {
                    data.errors.forEach(error =>
                        toastr.error(error.message, {
                            CloseButton: true,
                            ProgressBar: true
                        })
                    );
                } else {
                    updateUI(productId);
                    toastr.info('{{ translate("Item has been removed from cart") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        $(document).on('click', '.pos-empty-cart', function () {
            emptyCart();
        })

        function emptyCart() {
            $.post('{{ route('admin.pos.emptyCart') }}', {_token: '{{ csrf_token() }}'}, function (data) {
                updateCart();
                toastr.info('{{translate('Item has been removed from cart')}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
                location.reload();
            });
        }

        function updateCart() {
            $.post('<?php echo e(route('admin.pos.cart_items')); ?>', {_token: '<?php echo e(csrf_token()); ?>'}, function (data) {
                $('#cart').empty().html(data);
                calculateAmountDifference();
            });
        }

        function store_key(key, value) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                }
            });
            $.post({
                url: '{{route('admin.pos.store-keys')}}',
                data: {
                    key: key,
                    value: value,
                },
                success: function (data) {
                    if (key == 'customer_id') {
                        let customer = data.customer;
                        let balance = parseFloat(customer.balance.replace(/[^0-9.-]+/g,""));
                        let total = parseFloat($('.total-amount').data('total-amount'));
                        if (customer.id == 0 || customer.id == null) {
                            $('.select2-selection__clear').addClass('d-none');
                            $('.customer-details').addClass('d-none');
                            $('#add-coupon-button')
                                .prop('disabled', true)
                                .removeAttr('data-toggle data-target')
                            $('#add-coupon-button i').attr('title', 'This button is disabled').tooltip({trigger: 'hover'});
                        } else {
                            $('.select2-selection__clear').removeClass('d-none');
                            $('.customer-details').removeClass('d-none');
                            $('#add-coupon-button')
                                .prop('disabled', false)
                                .attr('data-toggle', 'modal')
                                .attr('data-target', '#couponAdd-discount');
                            $('#add-coupon-button i')
                                .tooltip('dispose')
                                .removeAttr('title');
                        }

                        if (customer.id == 0 || customer.id == null || balance <= 0)
                        {
                            $('input[id="wallet"]').parent().addClass('d-none');
                            if (!$('.available-balance-section').hasClass('d-none')){
                                $('.available-balance-section').addClass('d-none');
                            }
                        } else {
                            $('input[id="wallet"]').parent().removeClass('d-none');
                            $('.available-wallet-balance').text(customer.balance);
                            if (total > balance) {
                                $('.used-wallet-balance').text('{{ translate('(Used ') }}' + customer.balance + ')');
                                $('input[id="wallet"]').val('multiple');
                            } else {
                                $('.used-wallet-balance').text('{{ translate('(Used ') }}' + $('.total-amount').text() + ')');
                                $('input[id="wallet"]').val('wallet');
                            }
                        }
                        $('input[type="radio"]#cash').prop('checked', true).trigger('change');
                        $('.pos-customer-name').text(customer.f_name + ' ' + customer.l_name);
                        $('.pos-customer-phone').text(customer.phone);
                        $('.pos-customer-email').text(customer.email);
                        $('.pos-customer-wallet')
                            .text(customer.balance)
                            .data('customer-wallet', balance);
                        getCustomerCouponList();

                    }

                    let selected_field_text = key;
                    var selected_field = selected_field_text.replace("_", " ");
                    var selected_field = selected_field.replace("id", " ");
                    var message = selected_field + ' ' + 'selected!';
                    var new_message = message.charAt(0).toUpperCase() + message.slice(1);
                    toastr.success((new_message), {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
            });
        }

        function getCustomerCouponList() {
            $.ajax({
                url: '{{route('admin.pos.get-customer-coupon-list')}}',
                type: 'GET',
                success: function (data) {
                    $('#pos-coupon-list').empty().html(data.view);
                     initCouponSlider();
                },
                error: function (xhr) {
                    console.error(xhr.responseJSON || xhr.responseText);
                }
            });
        }


        function initCouponSlider() {
            const $container = $('.coupon-inner');
            const $btnPrevWrap = $('.button-prev');
            const $btnNextWrap = $('.button-next');
            const $prevBtn = $('.btn-click-prev');
            const $nextBtn = $('.btn-click-next');
            const $item = $('.coupon-slide_items').first();

            if (!$container.length) return;
            const show = $el => $el.css('display', 'flex');
            const hide = $el => $el.css('display', 'none');

            hide($btnPrevWrap);
            hide($btnNextWrap);
            function updateArrows() {
                if (!$container[0]) return;
                const scrollLeft = Math.ceil($container.scrollLeft());
                const clientWidth = $container[0].clientWidth;
                const scrollWidth = $container[0].scrollWidth;
                const maxScroll = Math.max(0, scrollWidth - clientWidth);

                if (maxScroll <= 0) {
                    hide($btnPrevWrap);
                    hide($btnNextWrap);
                    return;
                }

                if (scrollLeft > 0) show($btnPrevWrap);
                else hide($btnPrevWrap);

                if (scrollLeft < maxScroll - 1) show($btnNextWrap);
                else hide($btnNextWrap);
            }
            function getItemWidth() {
                if ($item.length) return $item.outerWidth() || 0;
                return Math.round($container.innerWidth() * 0.48);
            }
            $prevBtn.off('click').on('click', function () {
                const w = getItemWidth();
                const target = Math.max(0, $container.scrollLeft() - w);
                $container.animate({ scrollLeft: target }, 300, updateArrows);
            });

            $nextBtn.off('click').on('click', function () {
                const w = getItemWidth();
                const max = Math.max(0, $container[0].scrollWidth - $container.innerWidth());
                const target = Math.min(max, $container.scrollLeft() + w);
                $container.animate({ scrollLeft: target }, 300, updateArrows);
            });
            $container.on('scroll', updateArrows);
            let resizeTimer;
            $(window).off('resize.coupon').on('resize.coupon', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(updateArrows, 80);
            });

            try {
                const mo = new MutationObserver(() => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(updateArrows, 80);
                });
                mo.observe($container[0], { childList: true, subtree: true });
            } catch (e) {  }

            try {
                const ro = new ResizeObserver(() => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(updateArrows, 80);
                });
                ro.observe($container[0]);
            } catch (e) {  }

            requestAnimationFrame(() => requestAnimationFrame(updateArrows));
        }
        $(document).off('click', '.pos-open-coupon-discount-modal').on('click', '.pos-open-coupon-discount-modal', function () {
            if (!$(this).prop('disabled')) {
                getCustomerCouponList();
            }
        });

        $(document).on('click', '.coupon-slide_items', function () {
            $(this).siblings('.coupon-slide_items').removeClass('active');
            $(this).addClass('active');
            $('#coupon_form').find('input[name="pos_coupon_code"]').val($(this).find('.coupon-code').data('coupon-code'));
        });

        $(document).on('input', '#coupon_form #coupon_code', function(){
            $('#coupon_form').find('input[name="pos_coupon_code"]').val($(this).val());
        })

        $(function () {
            $(document).on('click', 'input[type=number]', function () {
                this.select();
            });
        });

        function storeOldValue(input) {
            $(input).data('old', $(input).val());
        }

        function updateQuantity(e) {
            const element = $(e.target);
            const minValue = parseInt(element.attr('min'));
            const maxValue = parseInt(element.attr('max'));
            const valueCurrent = parseInt(element.val());
            const key = element.data('key');
            const $row = element.closest('tr');
            const productId = $row.data('product-id');

            if (valueCurrent >= minValue && valueCurrent <= maxValue) {
                $.post('{{ route('admin.pos.updateQuantity') }}', {
                    _token: '{{ csrf_token() }}',
                    key: key,
                    quantity: valueCurrent
                }).done(function () {
                    updateUI(productId);
                });

            } else if (valueCurrent >= maxValue) {
                $.post('{{ route('admin.pos.updateQuantity') }}', {
                    _token: '{{ csrf_token() }}',
                    key: key,
                    quantity: maxValue
                }).done(function () {
                    updateUI(productId);
                    const message = '{{ translate("There isn’t enough quantity on stock. Only :stock is available.") }}'.replace(':stock', maxValue);
                    Swal.fire({
                        icon: 'error',
                        title: '{{translate("Product out of stock")}}',
                        text: message,
                        confirmButtonText: '{{translate("Yes")}}',
                    });
                });

                element.val(maxValue);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '{{translate("Cart")}}',
                    text: '{{translate('Sorry, the minimum value was reached')}}',
                    confirmButtonText: '{{translate("Yes")}}',
                });

                element.val(element.data('old'));
                updateUI(productId);
            }

            if (e.type === 'keydown') {
                const allowedKeys = [46, 8, 9, 27, 13, 190];
                if (
                    allowedKeys.includes(e.keyCode) ||
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 39)
                ) {
                    return;
                }

                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) &&
                    (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            }
        }

        function updateUI(productId) {
            return $.post('{{ route('admin.pos.cart_items') }}', {
                _token: '{{ csrf_token() }}'
            }).done(function (data) {
                $('#cart').empty().html(data);
                calculateAmountDifference();

                const $updatedRow = $('#cart').find('tr[data-product-id="' + productId + '"]');
                let updatedQuantity = 0;

                if ($updatedRow.length > 0) {
                    updatedQuantity = $updatedRow.toArray().reduce((sum, row) => {
                        const qty = parseInt($(row).data('product-quantity'));
                        return sum + (isNaN(qty) ? 0 : qty);
                    }, 0);
                }

                const $productCard = $('.product-id-' + productId);

                if (updatedQuantity > 0) {
                    $productCard.find('.count-product').text(updatedQuantity);
                    $productCard.find('.text-add-to-cart').addClass('d-none');
                    $productCard.find('.total-cart-count').removeClass('d-none');
                } else {
                    $productCard.find('.text-add-to-cart').removeClass('d-none');
                    $productCard.find('.total-cart-count').addClass('d-none');
                }
            });
        }


        $('.js-select2-custom').each(function () {
            let select2 = $.HSCore.components.HSSelect2.init($(this));
        });

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{route('admin.pos.customers')}}',
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                __port: function (params, success, failure) {
                    let $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });


        $('.js-data-example-ajax-2').select2()

        $('#order_place').submit(function (eventObj) {
            if ($('#customer').val()) {
                $(this).append('<input type="hidden" name="user_id" value="' + $('#customer').val() + '" /> ');
            }
            return true;
        });

        $(document).off('input', '#showPaidAmount').on('input', '#showPaidAmount', function () {
            calculateAmountDifference();
        });

        $(document).on('input', '#yourInputId', function () {

            let value = this.value.replace(/^0+(?=\d)/, '');
            if (value.startsWith('.')) {
                value = '0' + value;
            }
            if ((value.match(/\./g) || []).length > 1) {
                value = value.replace(/\.+$/, '');
            }
            this.value = value;
        });

        function calculateAmountDifference() {
            const showPaidAmountInput = document.getElementById('showPaidAmount');
            const cashRadio = document.getElementById('cash');
            const cardRadio = document.getElementById('card');
            const walletRadio = document.getElementById('wallet');
            const amountDiffInput = document.getElementById('amount-difference');
            const paidAmountInput = document.getElementById('paidAmount');
            const totalAmountInput = document.getElementById('totalAmount');
            const placeOrderWrapper = document.getElementById('placeOrder');
            const disablePlaceOrderWrapper = document.getElementById('disablePlaceOrder');
            const totalAmount = parseFloat(totalAmountInput.value) || 0;
            const paidAmount = parseFloat(showPaidAmountInput.value) || 0;

            if (cardRadio.checked) {
                paidAmountInput.value = totalAmount.toFixed(2);
                showPaidAmountInput.value = totalAmount.toFixed(2);
                amountDiffInput.value = '0.00';

                $(placeOrderWrapper).removeClass('d-none');
                $(disablePlaceOrderWrapper).addClass('d-none');
            } else if (cashRadio.checked) {
                const difference = paidAmount - totalAmount;

                paidAmountInput.value = paidAmount.toFixed(2);
                amountDiffInput.value = difference.toFixed(2);

                const isAmountSufficient = difference >= 0;

                $(placeOrderWrapper).toggleClass('d-none', !isAmountSufficient);
                $(disablePlaceOrderWrapper).toggleClass('d-none', isAmountSufficient);

                if (!isAmountSufficient) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            } else if(walletRadio.checked) {
                const additionalPaymentCashRadio = document.getElementById('additional-payment-cash');
                const additionalPaymentCardRadio = document.getElementById('additional-payment-card');

                if (additionalPaymentCardRadio && additionalPaymentCardRadio.checked) {
                    paidAmountInput.value = totalAmount.toFixed(2);
                    showPaidAmountInput.value = totalAmount.toFixed(2);
                    amountDiffInput.value = '0.00';

                    $(placeOrderWrapper).removeClass('d-none');
                    $(disablePlaceOrderWrapper).addClass('d-none');
                    $(paidAmountInput).attr('readonly', true);
                } else if (additionalPaymentCashRadio && additionalPaymentCashRadio.checked) {
                    const difference = paidAmount - totalAmount;
                    paidAmountInput.value = paidAmount.toFixed(2);
                    amountDiffInput.value = difference.toFixed(2);

                    const isAmountSufficient = difference >= 0;

                    $(placeOrderWrapper).toggleClass('d-none', !isAmountSufficient);
                    $(disablePlaceOrderWrapper).toggleClass('d-none', isAmountSufficient);
                    $(paidAmountInput).attr('readonly', false);
                    if (!isAmountSufficient) {
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                }
            }
        }

        submitByAjax('#discount_form', {
            hasEditors: false,
            languages: @json([]),
            redirectUrl:  '{{ route('admin.pos.index') }}',
            redirectDelay: 150
        });
        submitByAjax('#coupon_form', {
            hasEditors: false,
            languages: @json([]),
            redirectUrl:  '{{ route('admin.pos.index') }}',
            redirectDelay: 150
        });

        $(document).on('click', '.pos-open-discount-modal', function(){
            $('#coupon_form').find('.error-text').text('');
            $('#discount_form').find('.error-text').text('');
        });

        $(document).off('change', '#discount_form select[name="type"]').on('change', '#discount_form select[name="type"]', function () {
            if ($('#discount_form select[name="type"]').val() === 'percent')
            {
                $('#discount_form').find('.text-for-percent').removeClass('d-none');
                $('#discount_form').find('.text-for-amount').addClass('d-none');
            } else {
                $('#discount_form').find('.text-for-percent').addClass('d-none');
                $('#discount_form').find('.text-for-amount').removeClass('d-none');
            }
        });

        $(document).off('change', 'input[name="type"]').on('change', 'input[name="type"]', function() {
            if ($('#wallet').length && $('#wallet').is(':checked')) {
                $('.available-balance-section').removeClass('d-none');
            } else {
                $('.available-balance-section').addClass('d-none');
            }

            $.ajax({
                url: '{{ route('admin.pos.get-cart-payment-section') }}',
                type: 'GET',
                data: {
                    'payment_type': $(this).val(),
                    'total_amount': $('.total-amount').data('total-amount'),
                    'customer_wallet_balance': $('.pos-customer-wallet').data('customer-wallet') ?? 0,
                },
                success: function (data) {
                    $('#cart-payment-section').empty().html(data.view);
                    let total = parseFloat($('.total-amount').data('total-amount'));
                    let wallet = parseFloat($('.pos-customer-wallet').data('customer-wallet'));

                    if (total > wallet && $('#wallet').is(':checked')) {
                        $('.regular-change-amount').removeClass('d-flex').addClass('d-none');
                    } else {
                        $('.regular-change-amount').removeClass('d-none').addClass('d-flex');
                        $('.additional-payment-section').addClass('d-none');
                    }
                }
            });
        })
        $(document).off('change', 'input[name="additional_payment_type"]').on('change', 'input[name="additional_payment_type"]', function () {
            calculateAmountDifference();
        });
    </script>
    <script>
        if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('public/assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
    </script>
@endpush
