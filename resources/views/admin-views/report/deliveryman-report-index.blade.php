@extends('layouts.admin.app')

@section('title', translate('deliveryman_report'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/order_report.png')}}" alt="{{ translate('order-report') }}">
                {{translate('deliveryman_report')}}
            </h2>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="media gap-3 flex-column flex-sm-row align-items-sm-center">
                    <div class="avatar avatar-xl avatar-4by3 mr-2">
                        <img class="avatar-img" src="{{asset('public/assets/admin/svg/illustrations/order.png')}}" alt="{{ translate('image') }}">
                    </div>

                    <div class="media-body">
                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                            <div>
                                <h2 class="page-header-title">{{ translate('Deliveryman Report Overview') }}</h2>

                                <div class="meida flex-column gap-3">
                                    <span>{{translate('admin')}}:</span>
                                    <a href="#">{{auth('admin')->user()->f_name.' '.auth('admin')->user()->l_name}}</a>
                                </div>
                            </div>

                            <a class="btn btn-icon btn-primary rounded-circle" href="{{route('admin.dashboard')}}">
                                <i class="tio-home-outlined"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{request()->url()}}" id="form-data" class="filter-form" method="GET">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="form-label">{{translate('show')}} {{translate('data')}}
                                    {{translate('by')}} {{translate('date')}}{{translate('range')}}
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="mb-3">
                                <select class="form-control" name="delivery_man_id" id="delivery_man">
                                    <option value="all">{{translate('all')}} {{translate('deliveryman')}}</option>
                                    @foreach($deliverymen as $deliveryMan)
                                        <option value="{{$deliveryMan['id']}}" {{ $deliverymanId == $deliveryMan['id'] ? 'selected' : ''}}>{{$deliveryMan['f_name'].' '.$deliveryMan['l_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="mb-3">
                                <input type="date" name="start_date" id="from_date" value="{{ $startDate }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="mb-3">
                                <input type="date" name="end_date" id="to_date" value="{{ $endDate }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3 mb-3 __btn-row">
                            <a href="{{ route('admin.report.driver-report') }}" id="" class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
                            <button type="submit" class="btn btn-primary btn-block">{{translate('show')}}</button>
                        </div>
                    </div>
                </form>
                <div>
                    <strong>
                        {{translate('total')}}  {{translate('delivered')}} {{translate('qty')}} :
                        <span>{{$orders->total()}}</span>
                    </strong>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table id="datatable" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('SL')}}</th>
                        <th class="table-column-pl-0">{{translate('order')}}</th>
                        <th>{{translate('date')}}</th>
                        <th>{{translate('customer')}}</th>
                        <th>{{translate('branch')}}</th>
                         <th>{{translate('payment')}} {{translate('status')}}</th>
                        <th>{{translate('total')}}</th>
                        <th>{{translate('order')}} {{translate('status')}}</th>
                        <th>{{translate('actions')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                        @foreach($orders as $key=>$order)

                        <tr class="status-{{$order['order_status']}} class-all">
                            <td>{{$orders->firstitem()+$key}}</td>
                            <td class="table-column-pl-0">
                                <a href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                            </td>
                            <td>{{date('d M Y',strtotime($order['created_at']))}}</td>
                            <td>
                                @if($order->customer)
                                    <a class="text-body text-capitalize"
                                       href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                @else
                                    <label class="badge badge-danger">{{translate('invalid')}} {{translate('customer')}} {{translate('data')}}</label>
                                @endif
                            </td>
                            <td>
                                <label class="badge badge-soft-primary">{{$order->branch?$order->branch->name:'Branch deleted!'}}</label>
                            </td>
                            <td>
                                @if($order->payment_status=='paid')
                                    <span class="badge badge-soft-success">
                                      <span class="legend-indicator bg-success"></span>{{translate('paid')}}
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger">
                                      <span class="legend-indicator bg-danger"></span>{{translate('unpaid')}}
                                    </span>
                                @endif
                            </td>
                            <td>{{ Helpers::set_symbol($order['order_amount']) }}</td>
                            <td class="text-capitalize">
                                @if($order['order_status']=='pending')
                                    <span class="badge badge-soft-info ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-info"></span>{{translate('pending')}}
                                    </span>
                                @elseif($order['order_status']=='confirmed')
                                    <span class="badge badge-soft-info ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-info"></span>{{translate('confirmed')}}
                                    </span>
                                @elseif($order['order_status']=='processing')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-warning"></span>{{translate('processing')}}
                                    </span>
                                @elseif($order['order_status']=='out_for_delivery')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-warning"></span>{{translate('out_for_delivery')}}
                                    </span>
                                @elseif($order['order_status']=='delivered')
                                    <span class="badge badge-soft-success ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-success"></span>{{translate('delivered')}}
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger ml-2 ml-sm-3">
                                        <span class="legend-indicator bg-danger"></span>{{str_replace('_',' ',$order['order_status'])}}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        <i class="tio-settings"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item"
                                           href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i
                                                class="tio-visible"></i> {{translate('view')}}</a>
                                        <a class="dropdown-item" target="_blank"
                                           href="{{route('admin.orders.generate-invoice',[$order['id']])}}"><i
                                                class="tio-download"></i> {{translate('invoice')}}</a>
                                    </div>
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
        @endsection

        @push('script_2')
            <script type="text/javascript" src="{{asset('public/assets/admin/js/filter-form-validation.js')}}"></script>

            <script>
                "use strict"

                $('#from_date,#to_date').change(function () {
                    let from = $('#from_date').val();
                    let to = $('#to_date').val();
                    if (from !== '' && to !== '') {
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
