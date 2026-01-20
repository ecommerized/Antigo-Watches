@extends('layouts.branch.app')

@section('title', translate('Order List'))

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-3 align-items-center mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img src="{{asset('public/assets/admin/img/icons/all_orders.png')}}" alt="{{ translate('all_orders') }}">
                {{translate('all_orders')}}
            </h2>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form action="" id="form-data" class="filter-form" method="GET">
                    <div class="row align-items-end gy-2 gx-2">
                        <div class="col-12 pb-0">
                            <h4>{{translate('Select_Date_Range')}}</h4>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div>
                                <label for="form_date">{{translate('Start_Date')}}</label>
                                <input type="date" name="start_date" value="{{$startDate}}" id="from_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <div>
                                <label for="to_date">{{translate('End_date')}}</label>
                                <input type="date" name="end_date" value="{{$endDate}}" id="to_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 d-flex align-items-center gap-3 mt-2 __btn-row">
                            <a href="{{ route('branch.orders.list',[$status]) }}" id="" class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
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
                        <h6 class="m-0">{{ translate($status) }} {{translate('Order List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$orders->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
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
                                <i class="tio-download-to mt-1"></i>{{ translate('Export') }}<i
                                    class="tio-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right w-auto">
                                <li>
                                    <a type="submit" class="dropdown-item d-flex align-items-center gap-2"
                                       href="{{route('branch.orders.export', [$status, 'start_date'=>Request::get('start_date'), 'end_date'=>Request::get('end_date'), 'search'=>Request::get('search')])}}">
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
                <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table text-dark">
                    <thead class="thead-light">
                        <tr>
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('order_ID')}}</th>
                            <th>{{translate('order_date')}}</th>
                            <th>{{translate('customer_Info')}}</th>
                            <th>{{translate('total_amount')}}</th>
                            <th>{{translate('order')}} {{translate('status')}}</th>
                            <th>{{translate('order')}} {{translate('type')}}</th>
                            <th class="text-center">{{translate('actions')}}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($orders as $key=>$order)

                        <tr class="status-{{$order['order_status']}} class-all">
                            <td>{{$orders->firstitem()+$key}}</td>
                            <td>
                                <a class="text-dark" href="{{route('branch.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                            </td>
                            <td>
                                <div>{{date('d M Y',strtotime($order['created_at']))}}</div>
                                <div class="fs-12">{{date("h:i A",strtotime($order['created_at']))}}</div>
                            </td>
                            <td>
                                @if($order->is_guest == 0)
                                    @if($order->customer)
                                        <a class="text-capitalize" href="{{route('branch.orders.details',['id'=>$order['id']])}}">
                                            <h6 class="mb-0">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</h6>
                                        </a>
                                        <a class="text-dark fs-12" href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a>
                                    @else
                                        <h6 class="text-muted text-capitalize">{{translate('customer')}} {{translate('deleted')}}</h6>
                                    @endif
                                @else
                                    <h6 class="text-success">{{translate('Guest Customer')}}</h6>
                                @endif
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
                                @elseif($order['order_status']=='out_for_delivery')
                                    <span class="badge badge-soft-warning">{{translate('out_for_delivery')}}</span>
                                @elseif($order['order_status']=='delivered')
                                    <span class="badge badge-soft-success">{{translate('delivered')}}</span>
                                @else
                                    <span class="badge badge-soft-danger">{{str_replace('_',' ',$order['order_status'])}}</span>
                                @endif
                            </td>
                            <td class="text-capitalize">
                                @if($order['order_type']=='self_pickup')
                                    <span class="badge badge-soft-primary">{{translate('self_pickup')}}</span>
                                @elseif($order['order_type']=='pos')
                                    <span class="badge badge-soft-info">{{translate('POS')}}</span>
                                @else
                                    <span class="badge badge-soft-success">{{translate($order['order_type'])}}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a class="btn btn-outline--primary square-btn"
                                        href="{{route('branch.orders.details',['id'=>$order['id']])}}"><i class="tio-visible"></i>
                                    </a>
                                    <a class="btn btn-outline-info square-btn" target="_blank"
                                        href="{{route('branch.orders.generate-invoice',[$order['id']])}}"><i class="tio-download"></i>
                                    </a>
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
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection


@push('script_2')
    <script type="text/javascript" src="{{asset('public/assets/admin/js/filter-form-validation.js')}}"></script>
@endpush
