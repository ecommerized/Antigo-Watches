@extends('layouts.admin.app')

@section('title', translate('Product Report'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="media mb-3">
                <div class="avatar avatar-xl avatar-4by3 mr-2">
                    <img class="avatar-img" src="{{asset('public/assets/admin/svg/illustrations/order.png')}}"
                         alt="{{ translate('image') }}">
                </div>

                <div class="media-body">
                    <div class="row">
                        <div class="col-lg mb-3 mb-lg-0">
                            <h1 class="page-header-title">{{translate('product')}} {{translate('report')}} {{translate('overview')}}</h1>

                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span>{{translate('admin')}}:</span>
                                    <a href="#">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-auto">
                            <div class="d-flex">
                                <a class="btn btn-icon btn-primary rounded-circle" href="{{route('admin.dashboard')}}">
                                    <i class="tio-home-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header">
                    <div class="col-lg-12 pt-3">
                        <form action="#" id="form-data" method="GET">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6 col-xl-2 mb-3">
                                    <select class="custom-select custom-select" name="branch_id" id="branch_id">
                                    <option value="all">{{translate('all')}} {{translate('branch')}}</option>
                                    @foreach($branches as $branch)
                                            <option value="{{$branch['id']}}" {{ $branchId==$branch['id']?'selected':''}}>{{$branch['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6 col-xl-3 mb-3">
                                    <select class="form-control js-select2-custom" name="product_id" id="product_id" required>
                                        <option value="0">{{translate('select')}} {{translate('product')}}</option>
                                        @foreach($products as $product)
                                            <option value="{{$product['id']}}" {{ $productId==$product['id']?'selected':''}}>{{$product['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6 col-xl-2 mb-3">
                                    <input type="date" name="start_date" id="from_date" value="{{ $startDate }}" class="form-control">
                                </div>
                                <div class="col-sm-6 col-xl-2 mb-3">
                                    <input type="date" name="end_date" id="to_date" value="{{ $endDate }}" class="form-control">
                                </div>
                                <div class="col-sm-6 col-xl-3 mb-3 __btn-row">
                                    <a href="{{ route('admin.report.product-report') }}" id="" class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
                                    <button type="submit" class="btn btn-primary btn-block">{{translate('show')}}</button>
                                </div>

                                <div class="col-md-6 pt-4">
                                    <strong>{{translate('total')}} {{translate('orders')}} :
                                        <span id=""> {{ count($data) }}</span>
                                    </strong><br>
                                    <strong>
                                        {{translate('total')}} {{translate('item')}} {{translate('qty')}} :
                                        <span id="">{{ $totalQuantity }}</span>
                                    </strong><br>
                                    <strong>{{translate('total')}}  {{translate('amount')}} :
                                        <span id="">{{ Helpers::set_symbol($totalSold) }}</span>
                                    </strong>
                                </div>
                                <div class="col-6 pt-4">

                                    <div class="hs-unfold mr-5 float-right">
                                        <a class="js-hs-unfold-invoker btn btn-sm btn-white"
                                           href="{{route('admin.report.export-product-report', ['branch_id'=>Request::get('branch_id'), 'start_date'=>Request::get('start_date'), 'end_date'=>Request::get('end_date'), 'product_id'=>Request::get('product_id')])}}">
                                            <i class="tio-download-to mr-1"></i> {{translate('export')}}
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table">
                    <div class="row">
                        <div class="col-12 pr-4 pl-4">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{translate('#')}} </th>
                                    <th>{{translate('order')}}</th>
                                    <th>{{translate('date')}}</th>
                                    <th>{{translate('qty')}}</th>
                                    <th>{{translate('amount')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($data as $key=>$row)
                                    <tr>
                                        <td>{{$key+1}}</td>
                                        <td>
                                            <a href="{{route('admin.orders.details',['id'=>$row['order_id']])}}">{{$row['order_id']}}</a>
                                        </td>
                                        <td>{{date('d M Y',strtotime($row['date']))}}</td>
                                        <td>{{$row['quantity']}}</td>
                                        <td>{{ Helpers::set_symbol($row['price']) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if(count($data)==0)
                        <div class="text-center p-4">
                            <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                            <p class="mb-0">{{ translate('No data to show') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endsection

        @push('script_2')
            <script>
                "use strict";

                $(document).on('ready', function () {

                    $('.js-nav-scroller').each(function () {
                        new HsNavScroller($(this)).init()
                    });

                    $('.js-select2-custom').each(function () {
                        var select2 = $.HSCore.components.HSSelect2.init($(this));
                    });

                    var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'copy',
                                className: 'd-none'
                            },
                            {
                                extend: 'excel',
                                className: 'd-none'
                            },
                            {
                                extend: 'csv',
                                className: 'd-none'
                            },
                            {
                                extend: 'pdf',
                                className: 'd-none'
                            },
                            {
                                extend: 'print',
                                className: 'd-none'
                            },
                        ],
                        select: {
                            style: 'multi',
                            selector: 'td:first-child input[type="checkbox"]',
                            classMap: {
                                checkAll: '#datatableCheckAll',
                                counter: '#datatableCounter',
                                counterInfo: '#datatableCounterInfo'
                            }
                        },
                        language: {
                            zeroRecords: '<div class="text-center p-4">' +
                                '<img class="mb-3 width-7rem" src="{{asset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">' +
                                '<p class="mb-0">{{translate('No data to show')}}</p>' +
                                '</div>'
                        }
                    });

                    $('.js-tagify').each(function () {
                        var tagify = $.HSCore.components.HSTagify.init($(this));
                    });
                });

                $('#from_date,#to_date').change(function () {
                    let from = $('#from_date').val();
                    let to = $('#to_date').val();
                    if (from != '' && to != '') {
                        if (from > to) {
                            $('#from_date').val('');
                            $('#to_date').val('');
                            toastr.error({{ translate('Invalid date range!') }}, Error, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    }
                });
            </script>
    @endpush
