@extends('layouts.admin.app')

@section('title', translate('Order List'))

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img src="{{asset('public/assets/admin/img/icons/all_orders.png')}}" alt="{{ translate('pos_order') }}">
                {{translate('pos_orders')}}
            </h2>
            <span class="badge badge-soft-dark rounded-50 fs-14">{{ $orders->total() }}</span>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form action="" id="form-data" class="filter-form" method="GET">
                    <div class="row align-items-end gy-3 gx-2">
                        <div class="col-12 pb-0">
                            <h4>{{translate('Select_Date_Range')}}</h4>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label for="filter">{{translate('Select_Branch')}}</label>
                            <select class="custom-select custom-select-sm min-h-40" name="branch_id">
                                <option disabled>--- {{translate('select')}} {{translate('branch')}} ---</option>
                                <option
                                    value="all" {{ $branchId == 'all' ? 'selected': ''}}>{{translate('all')}} {{translate('branch')}}</option>
                                @foreach(\App\Models\Branch::all() as $branch)
                                    <option
                                        value="{{$branch['id']}}" {{ $branch['id'] == $branchId ? 'selected' : ''}}>{{$branch['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div>
                                <label for="form_date">{{translate('Start_Date')}}</label>
                                <input type="date" id="start_date" name="start_date" value="{{$startDate ?? ""}}"
                                       class="js-flatpickr form-control flatpickr-custom min-h-40px"
                                       placeholder="yy-mm-dd" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 mt-2 mt-sm-0">
                            <div>
                                <label for="to_date">{{translate('End_date')}}</label>
                                <input type="date" id="end_date" name="end_date" value="{{$endDate ?? ""}}"
                                       class="js-flatpickr form-control flatpickr-custom min-h-40px"
                                       placeholder="yy-mm-dd" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 mt-2 mt-sm-0 __btn-row">
                            <a href="{{ route('admin.pos.orders') }}" id=""
                               class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
                            <button type="submit" class="btn btn-primary btn-block">{{translate('Show_Data')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">

            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Order List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$orders->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{translate('Search by order ID')}}" aria-label="Search"
                                       value="{{$search}}" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-2 py-1 min-h-35">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div>
                            <button type="button"
                                    class="btn btn-outline-primary gap-1 d-flex font-weight-bold align-items-center min-h-35 py-1 fs-12 cmn-border"
                                    data-toggle="dropdown" aria-expanded="false">
                                <i class="tio-download-to"></i>{{ translate('export') }}<i class="tio-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right w-auto">
                                <li>
                                    <a type="submit" class="dropdown-item d-flex align-items-center gap-2"
                                       href="{{route('admin.pos.orders.export', ['branch_id'=>Request::get('branch_id'), 'start_date'=>Request::get('start_date'), 'end_date'=>Request::get('end_date'), 'search'=>Request::get('search')])}}">
                                        <img width="14" src="{{asset('public/assets/admin/img/icons/excel.png')}}"
                                             alt="{{ translate('excel') }}">
                                        {{translate('excel')}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table text-dark">
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('SL')}}</th>
                        <th>{{translate('order_ID')}}</th>
                        <th>{{translate('Order_date')}}</th>
                        <th>{{translate('customer_info')}}</th>
                        <th>{{translate('branch')}}</th>
                        <th>{{translate('total_amount')}}</th>
                        <th>{{translate('order_status')}}</th>
                        <th>{{translate('order_type')}}</th>
                        <th class="text-center">{{translate('actions')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($orders as $key=>$order)
                        <tr class="status-{{$order['order_status']}} class-all">
                            <td>{{$key+$orders->firstItem()}}</td>
                            <td>
                                <a class="text-dark"
                                   href="{{route('admin.pos.order-details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                            </td>
                            <td>
                                <div>{{date('d M Y',strtotime($order['created_at']))}}</div>
                                <div class="fs-12">{{date("h:i A",strtotime($order['created_at']))}}</div>
                            </td>
                            <td>
                                @if($order->customer && $order->user_id != 0)
                                    <a class="text-dark text-capitalize"
                                       href="{{route('admin.customer.view',[$order['user_id']])}}">
                                        <h6 class="mb-0">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</h6>
                                    </a>
                                    <a class="text-dark fs-12"
                                       href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a>
                                @elseif($order->user_id != null && !isset($order->customer) && $order->user_id != 0)
                                    <h6 class="text-muted text-capitalize">{{translate('customer_deleted')}}</h6>
                                @else
                                    <h6 class="text-muted text-capitalize">{{translate('walk_in_customer')}}</h6>
                                @endif
                            </td>
                            <td>
                                <label
                                    class="badge badge-soft-primary">{{$order->branch?$order->branch->name:'Branch deleted!'}}</label>
                            </td>
                            <td>
                                <div>{{ Helpers::set_symbol($order['order_amount']) }}</div>
                                @if($order->payment_status=='paid')
                                    <span class="text-success">{{translate('paid')}}</span>
                                @else
                                    <span class="text-danger">{{translate('unpaid')}}</span>
                                @endif
                            </td>
                            <td class="text-capitalize">
                                @if($order['order_status']=='pending')
                                    <span class="badge badge-soft-info">{{translate('pending')}}</span>
                                @elseif($order['order_status']=='confirmed')
                                    <span class="badge badge-soft-info">{{translate('confirmed')}}</span>
                                @elseif($order['order_status']=='processing')
                                    <span class="badge badge-soft-warning">{{translate('processing')}}</span>
                                @elseif($order['order_status']=='picked_up')
                                    <span class="badge badge-soft-warning">{{translate('out_for_delivery')}}</span>
                                @elseif($order['order_status']=='delivered')
                                    <span class="badge badge-soft-success">{{translate('delivered')}}</span>
                                @else
                                    <span
                                        class="badge badge-soft-danger">{{str_replace('_',' ',$order['order_status'])}}</span>
                                @endif
                            </td>
                            <td class="text-capitalize">
                                <span
                                    class="badge-soft-success px-2 py-1 rounded">{{translate($order['order_type'])}}</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-primary square-btn"
                                       href="{{route('admin.pos.order-details',['id'=>$order['id']])}}">
                                        <i class="tio-visible"></i>
                                    </a>
                                    <button class="btn btn-outline-info square-btn invoice-printing" target="_blank"
                                            type="button"
                                            data-id="{{$order->id}}">
                                        <i class="tio-download"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $orders->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($orders)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}"
                         alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

    @include('admin-views.modal.print-invoice-modal')
@endsection

@push('script_2')
    <script type="text/javascript" src="{{asset('public/assets/admin/js/filter-form-validation.js')}}"></script>

    <script>
        "use strict"

        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });


    </script>
@endpush
