@extends('layouts.admin.app')

@section('title', translate('Customer Details'))

@section('content')
    <div class="content container-fluid">
        <div class="d-print-none pb-2">
            <div class="mb-3">
                <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                    <img width="20" src="{{asset('public/assets/admin/img/icons/customer.png')}}" alt="{{ translate('customer') }}">
                    {{translate('Customer Details')}}
                </h2>
            </div>

            <div class="border-top"></div>

            <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center py-3">
                <div>
                    <h3 class="page-header-title">{{translate('customer_ID')}} #{{$customer['id']}}</h3>
                    <div class="fs-12">
                        <i class="tio-date-range"></i>
                        {{translate('joined_at')}} : {{date('d M Y H:i:s',strtotime($customer['created_at']))}}
                    </div>
                </div>

                <a href="{{route('admin.dashboard')}}" class="btn btn-primary">
                    <i class="tio-home-outlined"></i> {{translate('dashboard')}}
                </a>
            </div>
        </div>

        <div class="row" id="printableArea">
            <div class="col-lg-8">
                <div class="card mb-3 mb-lg-0">
                    <div class="px-20 py-3">
                        <div class="row gy-2 align-items-center">
                            <div class="col-sm-4">
                                <h5 class="text-capitalize d-flex align-items-center gap-2 mb-0">
                                    {{translate('customer_table')}}
                                    <span class="badge badge-soft-dark rounded-50 fz-12">{{ $orders->total() }}</span>
                                </h5>
                            </div>
                            <div class="col-sm-8">
                                <div class="d-flex flex-wrap justify-content-sm-end gap-2">
                                    <form action="{{url()->current()}}" method="GET">
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control"
                                                placeholder="{{translate('Search by Order ID')}}" aria-label="Search"
                                                value="{{$search}}" required autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary">{{translate('search')}}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{translate('SL')}}</th>
                                    <th>{{translate('order_ID')}}</th>
                                    <th>{{translate('total')}}</th>
                                    <th class="text-center">{{translate('action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $key=>$order)
                                <tr>
                                    <td>{{$orders->firstitem()+$key}}</td>
                                    <td>
                                        <a class="text-dark" href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                    </td>
                                    <td>{{ Helpers::set_symbol($order['order_amount']) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline-info square-btn"
                                                href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                                <i class="tio-visible"></i>
                                            </a>
                                            <a class="btn btn-outline-primary square-btn" target="_blank"
                                                href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                                <i class="tio-download"></i>
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
                            <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}"
                                 alt="{{ translate('image') }}">
                            <p class="mb-0">{{ translate('No data to show') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    @if($customer)
                        <div class="card-body">
                            <div class="media gap-3 flex-wrap align-items-center">

                                <div class="media-body">
                                    <h4 class="text--primary"><strong>{{\App\CentralLogics\Helpers::set_symbol($customer?->userAccount?->wallet_balance??0)}}</strong></h4>
                                    <h5><strong>{{translate('Total_Wallet_Balance')}}</h5>
                                </div>
                                <div>
                                    <img src="{{ asset('public/assets/admin/svg/components/wallet.svg') }}"
                                        class="img-fit"
                                        alt="wallet image">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="mb-0 d-flex align-items-center gap-2"><i class="tio tio-user"></i> {{translate('customer_information')}}</h4>
                    </div>
                    @if($customer)
                        <div class="card-body">
                            <div class="media gap-3 flex-wrap align-items-center">
                                <div class="avatar-lg avatar-circle mr-3">
                                    <img src="{{$customer['image_fullpath']}}"
                                        class="img-fit rounded-circle"
                                        alt="Image Description">
                                </div>
                                <div class="media-body text-dark">
                                    <div>{{$customer['f_name'].' '.$customer['l_name']}}</div>
                                    <div><strong>{{$customer->orders->count()}}</strong> {{translate('orders')}}</div>
                                    <a class="text-dark d-flex" href="tel:{{$customer['phone']}}"><strong>{{$customer['phone']}}</strong></a>
                                    <a class="text-dark d-flex" href="mailto:{{$customer['email']}}">{{$customer['email']}}</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))

                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="mb-0 d-flex align-items-center gap-2"><i class="tio tio-user"></i> {{translate('addresses')}}</h4>
                    </div>

                    @if($customer)
                        <div class="card-body">
                            @forelse($customer->addresses as $key=>$address)
                                <ul class="list-unstyled list-unstyled-py-2 text-dark">
                                    <li>
                                        <i class="tio-tab mr-2"></i>
                                        {{$address['address_type']}}
                                    </li>
                                    <li>
                                        <i class="tio-android-phone-vs mr-2"></i>
                                        {{$address['contact_person_number']}}
                                    </li>
                                    <li>
                                        @if($googleMapStatus && $address['latitude'] && $address['longitude'])
                                            <a class="text-dark" target="_blank" href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                                <i class="tio-map mr-2"></i>
                                                {{$address['address']}}
                                            </a>
                                        @else
                                            <p>{{$address['address']}}</p>
                                        @endif
                                    </li>
                                </ul>
                                @if($key+1 < count($customer->addresses))
                                    <hr>
                                @endif
                            @empty
                                <div class="text-center">
                                    <p>{{ translate('No data to show') }}</p>
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
