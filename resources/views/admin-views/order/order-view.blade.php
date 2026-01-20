@extends('layouts.admin.app')
@section('title', translate('Order Details'))

@section('content')
<div class="content container-fluid">
    <div class="mb-3 pb-xl-1 d-flex align-items-center justify-content-between gap-2 flex-wrap">
        <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
            <img src="{{asset('public/assets/admin/img/icons/all_orders.png')}}" alt="{{ translate('order_details') }}">
            {{translate('order_details')}}
            <span class="badge badge-soft-dark rounded-50 fz-14">{{$order->details->count()}}</span>
        </h2>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if($order->type != 'pos' && in_array($order->order_status, ['pending', 'confirmed', 'processing']) && $order->payment_status == 'unpaid')
                <button type="button" class="btn btn--info offcanvas-trigger" data-target="#offcanvas__order_edit">
                    <i class="tio-edit"></i> {{ translate('Edit Order') }}
                </button>
            @endif

            <a class="btn btn-primary" target="_blank" href={{route('admin.orders.generate-invoice',[$order['id']])}}>
                <i class="tio-print"></i> {{translate('print_invoice')}}
            </a>
        </div>
    </div>
    @php($googleMapStatus = \App\CentralLogics\Helpers::get_business_settings('google_map_status'))
    @php($mapApiKey = \App\CentralLogics\Helpers::get_business_settings('map_api_key'))
    <div class="row">
        <div class="col-lg-{{$order->user_id == null ? 12 : 8}} mb-3 mb-lg-0">
            <div class="card mb-3 mb-lg-5">
                <div class="card-body">
                    <div class="mb-3 text-dark d-print-none">
                        <div class="row gy-3">
                            <div class="col-sm-6">
                                <div class="d-flex flex-column justify-content-between h-100">
                                    <div class="d-flex flex-column gap-2">
                                        <h2 class="page-header-title">{{translate('order')}} #{{$order['id']}}</h2>
                                        <div>
                                            <i class="tio-date-range"></i>
                                            {{date('d M Y h:i a',strtotime($order['created_at']))}}
                                        </div>
                                        <h5 class="mb-0 flex-wrap gap-2">
                                            <i class="tio-shop"></i>
                                            {{translate('branch')}} : <label
                                                class="badge badge-secondary">{{$order->branch?$order->branch->name:'Branch deleted!'}}</label>
                                            @if(isset($googleMapStatus) && $order->order_status == 'out_for_delivery')
                                                <button type="button"
                                                        class="map-rendering ml-2 btn btn-outline-primary d-inline-flex align-items-center w-auto py-1 px-2 fs-14"
                                                        data-toggle="modal" data-target="#location-map-modal">
                                                    <i class="tio-poi"></i> {{ translate('Location on Map') }}
                                                </button>
                                                <span class="map-location-for-delivery"
                                                      data-delivered-to-latitude="{{ $deliveredToLatitude }}"
                                                      data-delivered-to-longitude="{{ $deliveredToLongitude }}"
                                                      data-delivered-from-latitude="{{ $deliveredFromLatitude }}"
                                                      data-delivered-from-longitude="{{ $deliveredFromLongitude }}"
                                                ></span>
                                            @else
                                                <button type="button" disabled
                                                        class="map-rendering ml-2 btn btn-outline-primary d-inline-flex align-items-center w-auto py-1 px-2 fs-14"
                                                        data-toggle="tooltip" title="{{ translate('Map is only available when order is out for delivery') }}"
                                                >
                                                    <i class="tio-poi"></i> {{ translate('Location on Map') }}
                                                </button>
                                            @endif
                                        </h5>
                                    </div>

                                    @if($order['order_type'] != 'pos' && $order['order_note'])
                                    <div><strong>{{translate('note')}}:</strong> {{$order['order_note']}}
                                    </div>
                                    @endif
                                    @if($order['bring_change_amount'] >0)
                                        <div
                                            class="badge badge-soft-info p-2 d-flex align-items-center gap-1 text-wrap text-left lh-1.3 font-size-sm mt-3">
                                            <i class="tio-info"></i>
                                            <span class="text-dark opacity-lg">
                                            {{translate('Please_bring').' '. \App\CentralLogics\Helpers::set_symbol($order['bring_change_amount']) . ' '.  translate('in_change_for_the_customer_when_making_the_delivery')}}
                                        </span>
                                        </div>
                                    @endif


                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex flex-column gap-2 align-items-sm-end h-100">
                                    <div class="d-flex gap-2">

                                    </div>

                                    <div class="d-flex justify-content-sm-end gap-2">
                                        <div>{{translate('Order_Status')}}:</div>
                                        @if($order['order_status']=='pending')
                                        <span class="text-info text-capitalize">{{translate('pending')}}</span>
                                        @elseif($order['order_status']=='confirmed')
                                        <span class="text-info text-capitalize">{{translate('confirmed')}}</span>
                                        @elseif($order['order_status']=='processing')
                                        <span class="text-warning text-capitalize">{{translate('processing')}}</span>
                                        @elseif($order['order_status']=='out_for_delivery')
                                        <span
                                            class="text-warning text-capitalize">{{translate('out_for_delivery')}}</span>
                                        @elseif($order['order_status']=='delivered')
                                        <span class="text-success text-capitalize">{{translate('delivered')}}</span>
                                        @else
                                        <span
                                            class="text-danger text-capitalize">{{str_replace('_',' ',$order['order_status'])}}</span>
                                        @endif
                                    </div>

                                    <div class="d-flex justify-content-sm-end gap-2">
                                        <div>{{translate('payment_Method')}}:</div>
                                        <div>{{ $order->payment_method == 'multiple' ? ucwords(implode(', ', $order->additional_payment_method)) : ucfirst(str_replace('_', ' ', $order['payment_method'])) }}</div>
                                    </div>
                                    @if($order['payment_method'] != 'cash_on_delivery' && $order['payment_method'] !=
                                    'wallet')
                                    <div class="d-flex justify-content-sm-end align-items-center gap-2">
                                        @if($order['transaction_reference']==null && $order['order_type']!='pos')
                                        <div>{{translate('reference_Code')}}:</div>
                                        <button class="btn btn-outline-primary btn-sm py-1" data-toggle="modal"
                                            data-target=".bd-example-modal-sm">
                                            {{translate('add')}}
                                        </button>
                                        @elseif($order['order_type']!='pos')
                                        <div>{{translate('reference_Code')}}:</div>
                                        <div>{{$order['transaction_reference']}}</div>
                                        @endif
                                    </div>
                                    @endif

                                    <div class="d-flex justify-content-sm-end gap-2">
                                        <div>{{translate('Payment_Status')}}:</div>
                                        @if($order['payment_status']=='paid')
                                        <span class="text-success">{{translate('paid')}}</span>
                                        @else
                                        <span class="text-danger">{{translate('unpaid')}}</span>
                                        @endif
                                    </div>

                                    <div class="d-flex justify-content-sm-end gap-2">
                                        <div>{{translate('order_Type')}}:</div>
                                        <label
                                            class="text-primary">{{str_replace('_',' ',$order['order_type'])}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php($item_amount=0)
                    @php($sub_total=0)
                    @php($total_tax=0)
                    @php($total_dis_on_pro=0)
                    @php($total_item_discount=0)

                    <div class="table-responsive">
                        <table class="table table-borderless table-align-middle text-dark">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('SL') }}</th>
                                    <th>{{ translate('Item Description') }}</th>
                                    <th>{{ translate('Unit Price') }}</th>
                                    <th>{{ translate('Discount') }}</th>
                                    <th>{{ translate('Qty') }}</th>
                                    <th class="text-right">{{ translate('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $detail)
                                @if($detail->product_details != null)
                                @php($product = json_decode($detail->product_details, true))
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="media gap-3 max-content">
                                            <div class="avatar-60 overflow-hidden rounded">
                                                @if($detail->product && $detail->product['image'] != null )
                                                <img class="img-fit" src="{{$detail->product['image_fullpath'][0]}}"
                                                    alt="{{ translate('image') }}" >
                                                @else
                                                <img src="{{asset('public/assets/admin/img/160x160/img2.jpg')}}"
                                                    class="img-fit img-fluid rounded aspect-ratio-1 overflow-hidden"
                                                    alt="{{ translate('image') }}">
                                                @endif
                                            </div>
                                            <div class="media-body">
                                                <h6 class="mb-1 w-24ch">{{$product['name']}}</h6>
                                                @if(count(json_decode($detail['variation'],true))>0)
                                                    @foreach(json_decode($detail['variation'],true)[0] ??
                                                    json_decode($detail['variation'],true) as $key1 =>$variation)
                                                        <div class="font-size-sm text-body text-capitalize">
                                                            @if($variation != null)
                                                                <span>{{$key1}} : </span>
                                                            @endif
                                                            <span class="font-weight-bold">{{$variation}}</span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ Helpers::set_symbol($detail['price']) }}
                                    </td>
                                    <td>{{Helpers::set_symbol($detail['discount_on_product'])}}</td>
                                    <td>{{$detail['quantity']}}</td>
                                    <td class="text-right">
                                        @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                                        {{ Helpers::set_symbol($amount) }}
                                    </td>
                                </tr>
                                @php($item_amount+=$detail['price']*$detail['quantity'])
                                @php($sub_total+=$amount)
                                @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
                                @php($total_item_discount += $detail['discount_on_product'] * $detail['quantity'])
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-md-end mb-3 border-top pt-4">
                        <div class="col-md-9 col-lg-8 col-xl-7 col-xxl-5">
                            <dl class="row">
                                <dt class="col-6">{{translate('items')}} {{translate('price')}}:</dt>
                                <dd class="col-6 text-end">{{ Helpers::set_symbol($item_amount) }}</dd>

                                <dt class="col-6">{{translate('item_discount')}}:</dt>
                                <dd class="col-6 text-end">{{ Helpers::set_symbol($total_item_discount) }}</dd>

                                <dt class="col-6">{{translate('tax')}} / {{translate('vat')}}:</dt>
                                <dd class="col-6 text-end">{{ Helpers::set_symbol($total_tax) }}</dd>

                                <dt class="col-6">{{translate('subtotal')}}:</dt>
                                <dd class="col-6 text-end">{{ Helpers::set_symbol($sub_total+$total_tax) }}</dd>
                                <dt class="col-6">{{translate('coupon')}} {{translate('discount')}}:</dt>
                                <dd class="col-6 text-end">
                                    - {{ Helpers::set_symbol($order['coupon_discount_amount']) }}</dd>

                                @if($order['order_type'] == 'pos')
                                <dt class="col-6">{{translate('Extra Discount')}}:</dt>
                                <dd class="col-6 text-end">
                                    - {{ Helpers::set_symbol($order['extra_discount']) }}</dd>
                                @endif
                                <dt class="col-6">{{translate('delivery')}} {{translate('fee')}}:</dt>
                                <dd class="col-6 text-end">
                                    {{ Helpers::set_symbol($order['delivery_charge']) }}
                                </dd>

                                <dt class="col-6 border-top pt-2 font-weight-bold">{{translate('total')}}:</dt>
                                <dd class="col-6 text-end border-top pt-2 font-weight-bold">
                                    {{ Helpers::set_symbol($order['order_amount']) }}
                                </dd>
                                @if($order['order_type'] == 'pos' && $order['paid_amount'] >0)
                                    @if($order->payment_method == 'multiple')
                                        @foreach($order->additional_payment_amount as $key => $value)
                                            <dt class="col-6">{{ ucwords($key) . ' ' . translate('_payment')}}:</dt>
                                            <dd class="col-6 text-end"> {{ Helpers::set_symbol($value) }}</dd>
                                        @endforeach
                                    @else
                                        <dt class="col-6">{{translate('paid_amount')}}:</dt>
                                        <dd class="col-6 text-end"> {{ Helpers::set_symbol($order['paid_amount']) }}</dd>
                                    @endif
                                <dt class="col-6">{{translate('change_amount')}}:</dt>
                                <dd class="col-6 text-end">
                                    {{ Helpers::set_symbol($order['paid_amount']- $order['order_amount']) }}
                                </dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($order->user_id != null)
        <div class="col-lg-4">
            @if($order['order_type'] != 'pos')
            <div class="card mb-3">
                <h4 class="mb-0 py-3 px-2 border-bottom text-center">
                    {{ $order['order_type'] != 'pos' ? translate('Order & Shipping Info ') : translate('Order Info') }}
                </h4>
                <div class="card-body text-capitalize d-flex flex-column">

                    <div class="mt-2">
                        @if($order['order_type'] != 'pos')
                        <h6>{{translate('Order Status')}}</h6>
                        <select name="order_status"
                            onchange="route_alert('{{route('admin.orders.status',['id'=>$order['id']])}}'+'&order_status='+ this.value,'{{translate("Change the order status to ") }}'+  this.value.replace(/_/g, ' '))"
                            class="form-control">
                            <option value="pending" {{$order['order_status'] == 'pending'? 'selected' : ''}}>
                                {{translate('pending')}}</option>
                            <option value="confirmed" {{$order['order_status'] == 'confirmed'? 'selected' : ''}}>
                                {{translate('confirmed')}}</option>
                            <option value="processing" {{$order['order_status'] == 'processing'? 'selected' : ''}}>
                                {{translate('processing')}}</option>
                            <option value="out_for_delivery"
                                {{$order['order_status'] == 'out_for_delivery'? 'selected' : ''}}>
                                {{translate('Out_For_Delivery')}} </option>
                            <option value="delivered" {{$order['order_status'] == 'delivered'? 'selected' : ''}}>
                                {{translate('Delivered')}} </option>
                            <option value="returned" {{$order['order_status'] == 'returned'? 'selected' : ''}}>
                                {{translate('Returned')}}</option>
                            <option value="failed" {{$order['order_status'] == 'failed'? 'selected' : ''}}>
                                {{translate('Failed')}} </option>
                            <option value="canceled" {{$order['order_status'] == 'canceled'? 'selected' : ''}}>
                                {{translate('canceled')}} </option>
                        </select>
                        @endif
                    </div>

                    <div class="mt-3">
                        @if($order['order_type'] != 'pos')
                        <h6>{{translate('Payment Status')}}</h6>
                        <select name="order_status"
                            onchange="route_alert('{{route('admin.orders.payment-status',['id'=>$order['id']])}}'+'&payment_status='+ this.value,'{{translate("Change status to ")}}' + this.value)"
                            class="form-control">
                            <option value="paid" {{$order['payment_status'] == 'paid'? 'selected' : ''}}>
                                {{translate('paid')}}</option>
                            <option value="unpaid" {{$order['payment_status'] == 'unpaid'? 'selected' : ''}}>
                                {{translate('unpaid')}} </option>
                        </select>
                        @endif
                    </div>

                    @if($order['order_type']!='self_pickup' && $order['order_type'] != 'pos')
                    <div class="mt-3">
                        <h6>{{translate('Select Deliveryman')}}</h6>
                        <select class="form-control" name="delivery_man_id" id="select-delivery-man">
                            <option value="0">{{translate('select')}} {{translate('deliveryman')}}</option>
                            @foreach($deliverymen as $deliveryman)
                            <option value="{{$deliveryman['id']}}"
                                {{$order['delivery_man_id']==$deliveryman['id']?'selected':''}}>
                                {{$deliveryman['f_name'].' '.$deliveryman['l_name']}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-header-title"><i class="tio tio-user"></i> {{translate('Customer_Information')}}
                    </h4>
                </div>

                <div class="card-body">
                    <div class="media gap-3">
                        @if($order->is_guest == 1)
                        <div class="media-body d-flex flex-column gap-1 text-dark">
                            <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                {{translate('Guest Customer')}}
                            </span>
                        </div>
                        @else
                        @if($order->customer)
                        <div class="avatar-lg rounded-circle">
                            <img class="img-fit rounded-circle" src="{{$order->customer->image_fullpath}}"
                                alt="Image Description">
                        </div>
                        <div class="media-body d-flex flex-column gap-1 text-dark">
                            <div>{{$order->customer['f_name'].' '.$order->customer['l_name']}}</div>
                            <div>{{\App\Models\Order::where('user_id',$order['user_id'])->count()}}
                                {{translate('orders')}}</div>
                            <a class="text-dark"
                                href="tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                            <a class="text-dark"
                                href="mailto:{{$order->customer['email']}}">{{$order->customer['email']}}</a>
                        </div>
                        @else
                        <div class="media-body d-flex flex-column gap-1 text-dark">
                            <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                {{translate('Customer_deleted')}}
                            </span>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
            @if($order['order_type']!='self_pickup' && $order['order_type'] != 'pos')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-header-title">
                        <i class="tio tio-user"></i>
                        {{translate('Delivery_Address')}}
                    </h4>
                </div>

                <div class="card-body">
                    @php($address=\App\Models\CustomerAddress::find($order['delivery_address_id']))
                    <div class="d-flex justify-content-between gap-3">
                        @if(isset($address))
                        <div class="delivery--information-single flex-column flex-grow-1">
                            <div class="d-flex">
                                <div class="name">{{translate('name')}}</div>
                                <div class="info">{{$address['contact_person_name']}}</div>
                            </div>
                            <div class="d-flex">
                                <div class="name">{{translate('contact')}}</div>
                                <a href="tel:{{$address['contact_person_number']}}"
                                    class="info">{{$address['contact_person_number']}}</a>
                            </div>
                            @if($address['floor'])
                            <div class="d-flex">
                                <div class="name">{{translate('floor')}}</div>
                                <div class="info">#{{$address['floor']}}</div>
                            </div>
                            @endif
                            @if($address['house'])
                            <div class="d-flex">
                                <div class="name">{{translate('house')}}</div>
                                <div class="info">#{{$address['house']}}</div>
                            </div>
                            @endif
                            @if($address['road'])
                            <div class="d-flex">
                                <div class="name">{{translate('road')}}</div>
                                <div class="info">#{{$address['road'] }}</div>
                            </div>
                            @endif
                            @if($googleMapStatus == 1 && isset($address['latitude']) && isset($address['longitude']))
                            <hr class="w-100">
                            <div>
                                <a target="_blank" class="text-dark d-flex align-items-center gap-3"
                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                    <i class="tio-map"></i> {{$address['address']}}<br>
                                </a>
                            </div>
                            @else
                            <div class="d-flex">
                                <div class="name">{{translate('address')}}</div>
                                <div class="info">#{{$address['address'] }}</div>
                            </div>
                            @endif
                        </div>
                        @endif
                        @if(isset($address))
                        <a class="link" data-toggle="modal" data-target="#shipping-address-modal" href="javascript:"><i
                                class="tio tio-edit"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title h4" id="mySmallModalLabel">{{translate('reference')}} {{translate('code')}}
                    {{translate('add')}}</h5>
                <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                    aria-label="Close">
                    <i class="tio-clear tio-lg"></i>
                </button>
            </div>

            <form action="{{route('admin.orders.add-payment-ref-code',[$order['id']])}}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" name="transaction_reference" class="form-control" placeholder="EX : Code123"
                            required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<div id="shipping-address-modal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalTopCoverTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-top-cover bg-dark text-center">
                <figure class="position-absolute right-0 bottom-0 left-0 mb-minus-1px">
                    <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                        viewBox="0 0 1920 100.1">
                        <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z" />
                    </svg>
                </figure>

                <div class="modal-close">
                    <button type="button" class="btn btn-icon btn-sm btn-ghost-light" data-dismiss="modal"
                        aria-label="Close">
                        <svg width="16" height="16" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                            <path fill="currentColor"
                                d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="modal-top-cover-icon">
                <span class="icon icon-lg icon-light icon-circle icon-centered shadow-soft">
                    <i class="tio-location-search"></i>
                </span>
            </div>

            @php($address=\App\Models\CustomerAddress::find($order['delivery_address_id']))
            @if(isset($address))
            <form action="{{route('admin.orders.update-shipping',[$order['delivery_address_id']])}}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('type')}}
                        </label>
                        <div class="col-md-10 js-form-message">
                            <input type="text" class="form-control" name="address_type"
                                value="{{$address['address_type']}}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('contact')}}
                        </label>
                        <div class="col-md-10 js-form-message">
                            <input type="text" class="form-control" name="contact_person_number"
                                value="{{$address['contact_person_number']}}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('name')}}
                        </label>
                        <div class="col-md-10 js-form-message">
                            <input type="text" class="form-control" name="contact_person_name"
                                value="{{$address['contact_person_name']}}" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('address')}}
                        </label>
                        <div class="col-md-10 js-form-message">
                            <input type="text" class="form-control" name="address" value="{{$address['address']}}"
                                required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('road')}}
                        </label>
                        <div class="col-md-10 js-form-message">
                            <input type="text" class="form-control" name="road" value="{{$address['road']}}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('house')}}
                        </label>
                        <div class="col-md-4 js-form-message">
                            <input type="text" class="form-control" name="house" value="{{$address['house']}}">
                        </div>
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('floor')}}
                        </label>
                        <div class="col-md-4 js-form-message">
                            <input type="text" class="form-control" name="floor" value="{{$address['floor']}}">
                        </div>
                    </div>

                    @if($googleMapStatus)
                    <div class="row mb-3">
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('latitude')}}
                        </label>
                        <div class="col-md-4 js-form-message">
                            <input type="text" class="form-control" name="latitude" value="{{$address['latitude']}}"
                                required>
                        </div>
                        <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                            {{translate('longitude')}}
                        </label>
                        <div class="col-md-4 js-form-message">
                            <input type="text" class="form-control" name="longitude" value="{{$address['longitude']}}"
                                required>
                        </div>
                    </div>
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">{{translate('close')}}</button>
                    <button type="submit" class="btn btn-primary">{{translate('save')}}
                        {{translate('changes')}}</button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

<!--- Edit Warning --->
<div class="modal fade" id="edit-product-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mxwidth">
        <div class="modal-content shadow-sm pb-sm-3">
            <div class="modal-header p-0">
                <button type="button"
                    class="close w-35 h-35 rounded-circle d-flex align-items-center justify-content-center bg-light position-relative"
                    data-dismiss="modal" aria-label="Close" style="top: 10px; inset-inline-end: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('public/assets/admin/img/delete-warning.png')}}" alt="" class="mb-3">
                <h3 class="mb-2">{{ translate('Are you sure') }}?</h3>
                <p class="m-0">{{ translate('You want to edit this order') }}?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 gap-2">
                <button type="button" class="btn min-w-120 btn--reset" data-dismiss="modal">{{ translate('No') }}</button>
                <button type="button" class="btn min-w-120 btn-primary">{{ translate('Yes') }}</button>
            </div>
        </div>
    </div>
</div>
<!--- Delete Warning --->
<div class="modal fade" id="delete-product-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mxwidth">
        <div class="modal-content shadow-sm pb-sm-3">
            <div class="modal-header p-0">
                <button type="button"
                    class="close w-35 h-35 rounded-circle d-flex align-items-center justify-content-center bg-light position-relative"
                    data-dismiss="modal" aria-label="Close" style="top: 10px; inset-inline-end: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('public/assets/admin/img/delete-warning.png')}}" alt="" class="mb-3">
                <h3 class="mb-2">{{ translate('Are you sure to delete this Product') }}?</h3>
                <p class="m-0">{{ translate('If once you delete this product, this will remove from product list.') }} </p>
            </div>
            <div class="modal-footer justify-content-center border-0 gap-2">
                <button type="button" class="btn min-w-120 btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <button type="button" class="btn min-w-120 btn-danger">{{ translate('Delete') }}</button>
            </div>
        </div>
    </div>
</div>

<!--- Location Map Modal --->
<div class="modal fade" id="location-map-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mxwidth" style="--modal-mxwidth: 710px;">
        <div class="modal-content shadow-sm">
            <div class="modal-header pt-3 px-3 pb-0">
                <h3 class="mb-2">Location data</h3>
                <button type="button"
                    class="close w-35 h-35 rounded-circle d-flex align-items-center justify-content-center bg--reset position-relative"
                    data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center pt-3">
                <input type="text" id="latitude" placeholder="Latitude" class="form-control mb-2" hidden>
                <input type="text" id="longitude" placeholder="Longitude" class="form-control mb-2" hidden>
                <div id="address" class="form-control d-none mb-2"></div>
                <div id="location_map_div">
                    <div id="location_map_canvas"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($order->type != 'pos' && in_array($order->order_status, ['pending', 'confirmed', 'processing']) && $order->payment_status == 'unpaid')
    <div id="offcanvas__order_edit" class="custom-offcanvas d-flex flex-column justify-content-between"
         style="--offcanvas-width: 750px">
        <div>
            <span class="data-to-js"
                  data-order-id="{{ $order['id'] }}"
                  data-product-details="{{ json_encode($orderedProducts) }}"
                  data-max-limit-message="{{ translate('maximum stock limit reached') }}"
                  data-min-limit-message="{{ translate('minimum 1 quantity is required') }}"
                  data-search-product-route="{{ route('admin.orders.search-product') }}"
                  data-default-image="{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                  data-currency-symbol="{{ Helpers::currency_symbol() }}"
                  data-currency-symbol-position="{{ Helpers::get_business_settings('currency_symbol_position') }}"
            >

            </span>
            <div class="custom-offcanvas-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <div class="bg-white px-3 py-3 d-flex justify-content-between w-100">
                    <div>
                        <h2 class="mb-1">{{ translate('Edit Order') }}</h2>
                        <div class="d-flex flex-wrap align-items-center gapy_30px">
                            <h3 class="page-header-title d-flex align-items-center gap-2">
                                <span class="font--max-sm fs-14">{{ translate('Order') }} #{{ $order['id'] }}</span>
                                <span class="badge badge-soft-info font-regular m-0">{{ translate($order->order_status) }}</span>
                            </h3>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fs-14 font-regular d-block text-dark">{{ translate('Order Placed') }} :</span>
                                <span class="fs-14 font-weight-bolder d-block text-dark"> {{ date('d M Y h:i a', strtotime($order->created_at)) }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button"
                            class="btn-close w-35 h-35 min-w-35 rounded-circle d-flex align-items-center justify-content-center bg--secondary position-relative offcanvas-close border-0 fs-18"
                            aria-label="Close">&times;
                    </button>
                </div>
            </div>
            <div class="custom-offcanvas-body p-20">
                <div class="mb-20 position-relative edit-search-form">
                    <div class="form-control bg-white d-flex align-items-center gap-2">
                        <i class="tio-search"></i>
                        <input type="text" name="search" class="h-100 fs-12 bg-transparent w-100 border-0 rounded-0"
                               value="" placeholder="{{ translate('Search by name and id, press enter to add') }}" autocomplete="off">
                        <!--- After Search -->
                        <div class="search-wrap-manage w-100 d-none">
                            <div class="search-items-wrap p-sm-3 p-2 rounded bg-white d-flex flex-column gap-2">
                               @include('admin-views.order.partials.product-search-result')
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3 align-items-center mb-10px">
                    <h6 class="m-0">{{ translate('Products List') }} </h6>
                    <span class="badge badge-soft-dark rounded-50 fz-10 count-total-products">{{ $order->details->count() }}</span>
                </div>
                <div class="table-responsive pt-0 card mb-20">
                    <table
                        class="table table-thead-bordered table-nowrap table-align-middle card-table dataTable no-footer mb-0" id="productListToUpdate">
                        <thead class="border-0 table-th-bg p-0">
                        <tr>
                            <th class="border-0">{{ translate('Sl') }}</th>
                            <th class="border-0">{{ translate('Item Description') }}</th>
                            <th class="border-0 text-center">{{ translate('Qty') }}</th>
                            <th class="border-0 text-right">{{ translate('Total') }}</th>
                            <th class="border-0">{{ translate('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="offcanvas-footer w-100 bg-white p-3 d-flex align-items-center justify-content-end gap-3">
                <button type="button" class="btn min-w-120 btn--secondary h--40px reset offcanvas-close">{{ translate('Cancel') }}</button>
                <button type="submit" class="btn min-w-120 btn-primary h--40px update-product-list">{{ translate('Update Cart ') }}</button>
            </div>
        </div>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>
    <div class="modal cmn__quick-modal fade" id="quick-view" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" style="--modal-mxwidth: 650px; max-width: 650px !important;">
            <div class="modal-content" id="quick-view-modal">

            </div>
        </div>
    </div>
@endif
@include('admin-views.modal.print-invoice-modal')
@endsection
@push('script_2')
    @if(!empty($googleMapStatus) && $order->order_status == 'out_for_delivery')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $mapApiKey }}&callback=googleMapInitialize&loading=async&libraries=places&v=3.62" defer></script>
        <script>
            $(window).load(function() {
                function initMap() {
                    const deliveredToLatitude = $('.map-location-for-delivery').data('delivered-to-latitude');
                    const deliveredToLongitude = $('.map-location-for-delivery').data('delivered-to-longitude');
                    const deliveredFromLatitude = $('.map-location-for-delivery').data('delivered-from-latitude');
                    const deliveredFromLongitude = $('.map-location-for-delivery').data('delivered-from-longitude');
                    const deliveredToLocation = {
                        lat: deliveredToLatitude,
                        lng: deliveredToLongitude
                    }
                    const deliveredFromLocation = {
                        lat: deliveredFromLatitude,
                        lng: deliveredFromLongitude
                    }
                    const deliveredToIcon = {
                        url: "{{ asset('public/assets/admin/img/icons/reesaturant-icon.png') }}",
                        scaledSize: new google.maps.Size(40, 40),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(20, 40)
                    };

                    const deliveredFromIcon = {
                        url: "{{ asset('public/assets/admin/img/icons/driver-icon.png') }}",
                        scaledSize: new google.maps.Size(40, 40),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(20, 40)
                    };

                    const map = new google.maps.Map(document.getElementById("location_map_canvas"), {
                        center: deliveredToLocation,
                        zoom: 7,
                        mapTypeId: "roadmap",
                    });

                    const DeliveredToMarker = new google.maps.Marker({
                        position: deliveredToLocation,
                        map: map,
                        icon: deliveredToIcon,
                        title: "Customer location",
                    });

                    const deliveredFromMarker = new google.maps.Marker({
                        position: deliveredFromLocation,
                        map: map,
                        icon: deliveredFromIcon,
                        title: "Delivery man location",
                    });

                    const bounds = new google.maps.LatLngBounds();
                    bounds.extend(deliveredToLocation);
                    bounds.extend(deliveredFromLocation);
                    map.fitBounds(bounds);
                }

                initMap();
            });
        </script>
    @endif

    <script type="text/javascript" src="{{asset('public/assets/admin/js/offcanvas.js')}}"></script>

    <script>
        "use strict"

        $('#select-delivery-man').on('change', function() {
            let id = $(this).val();
            addDeliveryMan(id);
        })

function addDeliveryMan(id) {
    $.ajax({
        type: "GET",
        url: '{{ route('admin.orders.add-delivery-man', [$order['id'], ':delivery_man_id']) }}'.replace(':delivery_man_id', id),
        data: $('#product_form').serialize(),
        success: function(data) {
            if (data.status == true) {
                toastr.success('{{translate("Delivery man successfully assigned/changed")}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            } else {
                toastr.error('{{translate("Deliveryman man can not assign/change in that status")}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            }
        },
        error: function() {
            toastr.error('{{translate("Add valid data")}}', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    });
}

$('.last_location_view').on('click', function() {
    toastr.warning('{{translate("Only available when order is out for delivery!")}}', {
        CloseButton: true,
        ProgressBar: true
    });
})

$('input[name="search"]').on('keyup', function() {
    let value = $(this).val().toLowerCase();
    if (value.length > 0) {
        $('.search-wrap-manage').removeClass('d-none');
        $.ajax({
            url: $('.data-to-js').data('search-product-route'),
            method: 'GET',
            data: {
                'search': value,
                'order_id': $('.data-to-js').data('order-id'),
            },
            success: function (data) {
                $('.search-items-wrap').empty().html(data.view);
            }
        })
    } else {
        $('.search-wrap-manage').addClass('d-none');
    }
});

function renderProductDetailsToUpdate() {
    var $dataToJs = $('.data-to-js');
    var products = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    var $tbody = $('#productListToUpdate tbody');
    $tbody.empty();
    $.each(products, function (key, product) {
        var rowClass = (product.total_stock == product.quantity) ? 'max-limit' : '';
        var img = product.image ? product.image : $('.data-to-js').data('default-image');
        var variantHtml = product.variant ? `<div class="d-flex align-items-center gap-1 fs-12">{{ translate('variation') }} : <span class="text-dark">${product.variant}</span></div>` : '';
        var row = `
            <tr class="custom__tr ${rowClass} ${product.newly_added ? 'active' : '' } " data-id="${key}">
                <td>${key + 1}</td>
                <td>
                    <div class="list-items-media cursor-pointer d-flex align-items-center gap-2 quick-View" data-id="${product.id || ''}">
                        <img width="50" height="50" src="${img}" class="rounded" alt="image">
                        <div class="cont d-flex align-justify-content-center flex-column gap-0">
                            <p class="fs-12 text-dark mb-1 max-w-220 line--limit-1">${product.name}</p>
                            <div class="d-flex align-items-center gap-1 fs-12">
                                {{ translate('Unit Price') }} : <span class="text-dark">${product.price_with_symbol}</span>
                            </div>
                            ${variantHtml}
                        </div>
                    </div>
                </td>
                <td>
                    <div class="product-quantity min-w-100 mx-auto">
                        <div class="input-group bg-white rounded border d-flex justify-content-center align-items-center">
                            <span class="input-group-btn w-30px">
                                <button class="btn px-2 btn-number bg-transparent w-30px" type="button" data-type="minus">
                                    <i class="tio-remove font-weight-bold"></i>
                                </button>
                            </span>
                            <input type="text"
                                   class="w-25px input-number form-control p-0 border-0 text-center text-dark"
                                   value="${product.quantity}"
                                   placeholder="${product.quantity}" min="1" data-maximum_quantity="${product.total_stock}" data-base-price="${product.base_price}" data-discount-price="${product.product_discount}">
                            <span class="input-group-btn w-30px">
                                <button class="btn px-2 btn-number bg-transparent w-30px" type="button" data-type="plus">
                                    <i class="tio-add font-weight-bold"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </td>
                <td class="fs-14 text-dark text-right">
                <span class="product-total-price fs-12 text-decoration-line-through ${product.discount == 0 ? 'd-none' : ''}">
                ${product.total_price}
                </span>
                ${product.discount == 0 ? '' : '<br>'}
                <span class="product-total-discount-price">${ product.discount == 0 ? product.total_price : product.total_discount_price }</span>
                </td>
                <td class="text-center">
                    <a class="btn btn-danger rounded-circle square-btn" href="javascript:" data-toggle="modal" data-target="#delete-product-modal">
                        <i class="tio tio-delete"></i>
                    </a>
                </td>
            </tr>
        `;
        $tbody.append(row);
    });
    manageQuantity();
    $('.count-total-products').text(products.length)
}
renderProductDetailsToUpdate()

$(document).on('click', '.add-searched-product', function(){
    let $dataToJs = $('.data-to-js');
    let existedProducts = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    let exist = existedProducts.some(product => product.id == $(this).data('id') && product.variant == $(this).data('variant'));
    if (exist) {
        toastr.error('{{ translate("Product already added!") }}', {
            CloseButton: true,
            ProgressBar: true
        });
    } else {
        let newProduct = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            quantity: 1,
            variant: $(this).data('variant'),
            price_with_symbol: $(this).data('price'),
            product_discount: $(this).data('discount-price'),
            discount: $(this).data('discount'),
            image: $(this).data('image'),
            total_stock: $(this).data('stock'),
            total_price: $(this).data('price'),
            base_price: $(this).data('base-price'),
            price: $(this).data('base-price'),
            total_discount_price: $(this).data('total-discount-price'),
            newly_added: true,
        }
        existedProducts.push(newProduct);
        $dataToJs.attr('data-product-details', JSON.stringify(existedProducts));
    }

    renderProductDetailsToUpdate();

    $('input[name="search"]').val('');
    $('.search-wrap-manage').addClass('d-none');
});

let deleteRowIndex = null;
$(document).on('click', '.btn-danger[data-target="#delete-product-modal"]', function() {
    const $row = $(this).closest('tr');
    deleteRowIndex = $row.data('id');
});
$('#delete-product-modal .btn-danger').on('click', function() {
    if (deleteRowIndex !== null) {
        let $dataToJs = $('.data-to-js');
        let existedProducts = JSON.parse($dataToJs.attr('data-product-details') || '[]');
        existedProducts.splice(deleteRowIndex, 1);
        $dataToJs.attr('data-product-details', JSON.stringify(existedProducts));

        renderProductDetailsToUpdate();

        deleteRowIndex = null;
        $('#delete-product-modal').modal('hide');
    }
});

$(document).off('click', '.quick-view').on('click', '.quick-view', function(){
    let productId = $(this).data('id');
    quickView(productId);
    $('input[name="search"]').val('');
    $('.search-wrap-manage').addClass('d-none');
});

function quickView(product_id) {
    let $dataToJs = $('.data-to-js');
    let productList = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    $.ajax({
        url: '{{route('admin.pos.quick-view')}}',
        type: 'GET',
        data: {
            product_id: product_id,
            product_list: productList,
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
    let $dataToJs = $('.data-to-js');
    let productList = JSON.parse($dataToJs.attr('data-product-details') || '[]');

    let formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        id: $('input[name="id"]').val(),
        quantity: quantity,
        product_list: productList,
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
    if (!checkAddToCartValidity()) {
        Swal.fire({
            type: 'info',
            title: '{{translate('Edit Order')}}',
            confirmButtonText: '{{translate("Ok")}}',
            text: '{{translate('Please choose all the options')}}'
        });
        return;
    }
    let $dataToJs = $('.data-to-js');
    let existedProducts = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    let currencySymbol = $dataToJs.data('currency-symbol');
    let currencySymbolPosition = $dataToJs.data('currency-symbol-position');
    let formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        id: $('input[name="id"]').val(),
        quantity: $('input[name="quantity"]').val(),
        product_list: existedProducts,
    };
    $('[type=radio]:checked').each(function () {
        formData[$(this).attr('name')] = $(this).val();
    });

    $.post({
        url: '{{ route('admin.pos.add-to-cart') }}',
        data: formData,
        beforeSend: function () { $('#loading').show(); },
        success: function (data) {
            let productData = data.data;
            let existingIndex = existedProducts.findIndex(p => p.id == productData.id && p.variant == productData.variant);

            if (existingIndex !== -1) {
                existedProducts[existingIndex].quantity = parseInt(productData.quantity);
                existedProducts[existingIndex].total_price = currencySymbolPosition == 'left'
                    ? currencySymbol + (existedProducts[existingIndex].base_price * existedProducts[existingIndex].quantity).toFixed(2)
                    : (existedProducts[existingIndex].base_price * existedProducts[existingIndex].quantity).toFixed(2) + currencySymbol;
            } else {
                let newProduct = {
                    id: productData.id,
                    name: productData.name,
                    quantity: parseInt(productData.quantity),
                    variant: productData.variant,
                    price_with_symbol: currencySymbolPosition == 'left'
                        ? currencySymbol + productData.price.toFixed(2)
                        : productData.price.toFixed(2) + currencySymbol,
                    image: productData.image?.[0] ?? null,
                    total_stock: productData.total_stock,
                    total_price: currencySymbolPosition == 'left'
                        ? currencySymbol + (productData.price * productData.quantity).toFixed(2)
                        : (productData.price * productData.quantity).toFixed(2) + currencySymbol,
                    base_price: productData.price,
                    price: productData.price,
                    product_discount: productData.price - productData.discount,
                    discount: productData.discount,
                    total_discount_price: currencySymbolPosition == 'left'
                        ? currencySymbol + ((productData.price - productData.discount) * productData.quantity).toFixed(2)
                        : ((productData.price - productData.discount) * productData.quantity).toFixed(2) + currencySymbol,
                    newly_added: true,
                };
                existedProducts.push(newProduct);
            }

            $dataToJs.attr('data-product-details', JSON.stringify(existedProducts));

            renderProductDetailsToUpdate();
            $('.call-when-done').click();

            toastr.success('{{translate('Item has been added to the list')}}!', {
                CloseButton: true,
                ProgressBar: true
            });
        },
        complete: function () { $('#loading').hide(); }
    });
}

function checkAddToCartValidity() {
    return true;
}

function updateProductList() {
    let $dataToJs = $('.data-to-js');
    let orderId = $dataToJs.data('order-id');
    let products = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    $.ajax({
        type: "POST",
        url: '{{ route('admin.orders.update-product-list', ':id') }}'.replace(':id', orderId),
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            products: products,
        },
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            if (data.status == true) {
                toastr.success('{{translate("Product list updated successfully")}}', {
                    CloseButton: true,
                    ProgressBar: true
                });
                $('.update-product-list').attr('disabled', true);
                setTimeout(function () {
                    location.reload();
                }, 1200);
            }
        },
        error: function () {
            toastr.error('{{translate("Something went wrong")}}', {
                CloseButton: true,
                ProgressBar: true
            });
            $('.update-product-list').attr('disabled', false);
        },
        complete: function () {
            $('#loading').hide();
        }
    });
}

$('.update-product-list').on('click', function() {
    updateProductList();
});
</script>
@endpush
