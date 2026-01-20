<div style="width:410px;" class="mx-auto">

    <div class="text-center pt-2 mb-3">
        <h2>{{ Helpers::get_business_settings('restaurant_name') }}</h2>
        <h4>{{$order->branch?->name}}</h4>
        <h5>
            {{translate('Branch Address')}} : {{ \App\Models\Branch::find($order['branch_id'])?->address }}
        </h5>
        <p class="text-dark">
            {{translate('Phone')}} : {{Helpers::get_business_settings('phone')}}
        </p>
    </div>

    <div class="invoice-border"></div>
    <div class="row pt-3 pb-2">
        <div class="col-6">
            <h5>{{translate('Order ID')}} : {{$order['id']}}</h5>
        </div>
        <div class="col-6">
            <div class="text-right text-dark">
                {{date('d M Y h:i a',strtotime($order['created_at']))}}
            </div>
        </div>
        @if($order->customer)
            <div class="col-12 text-dark pb-2">
                <div>{{translate('Customer Name')}}
                    : {{$order->customer['f_name'].' '.$order->customer['l_name']}}</div>
                <div>{{translate('Phone')}} : {{$order->customer['phone']}}</div>
                @if($order->order_type != 'pos')
                    <div>
                        {{translate('Address')}}
                        : {{isset($order->delivery_address)?json_decode($order->delivery_address, true)['address']:''}}
                    </div>
                @endif
            </div>
        @endif
    </div>
    <div class="invoice-border"></div>
    <table class="table table-bordered mt-3 text-dark">
        <thead>
        <tr>
            <th class="border-bottom-0">{{translate('Qty')}}</th>
            <th class="border-bottom-0">{{translate('Desc')}}</th>
            <th class="border-bottom-0">{{translate('Price')}}</th>
        </tr>
        </thead>

        <tbody>
        @php($sub_total=0)
        @php($total_tax=0)
        @php($total_dis_on_pro=0)
        @foreach($order->details as $detail)
            @if($detail->product_details != null)
                @php($product = json_decode($detail->product_details, true))

                <tr>
                    <td>
                        {{$detail['quantity']}}
                    </td>
                    <td>
                        <div class="mb-1"> {{ Str::limit($product['name'], 200) }}</div>
                        @if(count(json_decode($detail['variation'],true))>0)
                            <strong><u>{{translate('Variation')}} : </u></strong>
                            @foreach(json_decode($detail['variation'],true)[0] ?? json_decode($detail['variation'],true) as $key1 =>$variation)
                                <div class="font-size-sm">
                                    <span>{{$key1}} :  </span>
                                    <strong>
                                        {{$variation}} {{$key1=='price'?Helpers::currency_symbol():''}}
                                    </strong>
                                </div>
                            @endforeach
                        @endif

                        <div>
                            {{translate('Discount')}} :
                            {{ Helpers::set_symbol($detail['discount_on_product']*$detail['quantity']) }}
                        </div>
                    </td>
                    <td>
                        @php($amount=($detail['price']-$detail['discount_on_product'])*$detail['quantity'])
                        {{ Helpers::set_symbol($amount) }}
                    </td>
                </tr>
                @php($sub_total+=$amount)
                @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
            @endif
        @endforeach
        </tbody>
    </table>
    <div class="invoice-border"></div>
    <dl class="row text-dark mt-2">
        <dt class="col-6">{{translate('Items Price')}}:</dt>
        <dd class="col-6 text-right">{{ Helpers::set_symbol($sub_total) }}</dd>

        <dt class="col-6">{{translate('Tax')}} / {{translate('VAT')}}:</dt>
        <dd class="col-6 text-right">{{Helpers::set_symbol($total_tax) }}</dd>

        <dt class="col-6">{{translate('Subtotal')}}:</dt>
        <dd class="col-6 text-right">{{ Helpers::set_symbol($sub_total + $total_tax) }}</dd>
        <dt class="col-6">{{translate('Coupon Discount')}}:</dt>
        <dd class="col-6 text-right">
            - {{ Helpers::set_symbol($order['coupon_discount_amount']) }}
        </dd>
        <dt class="col-6">{{translate('Extra Discount')}}:</dt>
        <dd class="col-6 text-right">
            - {{ Helpers::set_symbol($order['extra_discount']) }}
        </dd>
        <dt class="col-6 font-weight-bold">{{translate('Total')}}:</dt>
        <dd class="col-6 text-right font-weight-bold">{{ Helpers::set_symbol($order->order_amount) }}</dd>
        @if($order->order_type == 'pos' && $order->paid_amount >0)
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
            <dd class="col-6 text-right">{{ Helpers::set_symbol(($order->paid_amount - $order->order_amount)) }}</dd>
        @endif
    </dl>
    <div class="invoice-border mt-5"></div>
    <h5 class="text-center mb-0 py-3">
        """{{translate('THANK YOU')}}"""
    </h5>
    <div class="invoice-border"></div>
</div>
