<div class="bg-color-common rounded p-10px pt-0 mb-20">
    <div class="table-responsive pos-cart-table rounded">
        <table class="table border-spacing-custom card-add-table_customize table-borderless table-align-middle mb-0">
            <thead class="bg-primary-light text-dark">
            <tr>
                <th class="border-bottom-0">{{translate('item')}}</th>
                <th class="border-bottom-0">{{translate('qty')}}</th>
                <th class="border-bottom-0">{{translate('price')}}</th>
                <th class="border-bottom-0 text-center">{{translate('delete')}}</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $subtotal = 0;
            $discount = 0;
            $discount_type = 'amount';
            $discount_on_product = 0;
            $total_tax = 0;
            ?>
            @if(session()->has('cart') && count(session()->get('cart')) > 0)
                @foreach(session()->get('cart') as $key => $cartItem)
                    @if(is_array($cartItem))
                            <?php
                            $product_subtotal = ($cartItem['price']) * $cartItem['quantity'];
                            $discount_on_product += ($cartItem['discount'] * $cartItem['quantity']);
                            $subtotal += $product_subtotal;

                            $product = \App\Models\Product::find($cartItem['id']);
                            $total_tax += Helpers::tax_calculate($product, ($cartItem['price']-$cartItem['discount'])) * $cartItem['quantity'];

                            ?>
                        <tr data-product-id="{{ $cartItem['id'] }}" data-product-quantity="{{ $cartItem['quantity'] }}">
                            <td class="media gap-2 align-items-center">
                                <div class="avatar-50 rounded border">
                                    <img class="img-fit rounded"
                                         src="{{$cartItem['image'][0]}}"
                                         alt="{{$cartItem['name']}} image">
                                </div>
                                <div class="media-body">
                                    <h5 class="mb-0 line--limit-1 max-w-145 min-w-120">{{Str::limit($cartItem['name'], 10)}}</h5>
                                    <small>{{Str::limit($cartItem['variant'], 20)}}</small>
                                </div>
                            </td>
                            <td>
                                <input type="number" data-key="{{$key}}" class="form-control border-title qty"
                                       value="{{$cartItem['quantity']}}"
                                       min="1"
                                       max="{{array_key_exists('total_stock',$cartItem)? $cartItem['total_stock'] : 0}}"
                                       onfocus="storeOldValue(this)"
                                       onkeyup="updateQuantity(event)">
                            </td>
                            <td>
                                <div class="fs-12">
                                    {{ Helpers::set_symbol($product_subtotal) }}
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="javascript:" onclick="removeFromCart({{ $key }}, this)" class="btn btn-sm btn-outline-danger"> <i
                                        class="tio-delete-outlined"></i></a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
    <?php
    $total = $subtotal;

    $session_subtotal = $subtotal;
    \Session::put('subtotal', $session_subtotal);

    $total -= $discount_on_product;
    \Session::put('total_after_product_discount', $total);

    $coupon_discount = session()->get('cart')['coupon_discount'] ?? 0;
    if ($coupon_discount) {
        $total -= $coupon_discount;
    }
    $totalToShowInDiscountModal = $total;
    \Session::put('total', ($totalToShowInDiscountModal));

    $extra_discount = session()->get('cart')['extra_discount'] ?? 0;
    $extra_discount_type = session()->get('cart')['extra_discount_type'] ?? 'amount';
    if ($extra_discount_type == 'percent' && $extra_discount > 0) {
        $extra_discount = ($total * $extra_discount) / 100;
    }
    if ($extra_discount) {
        $total -= $extra_discount;
    }

    \Session::put('total_to_show_in_payment_section', $total);
    \Session::put('total_tax', $total_tax);
    ?>
    <div class="bg-white rounded p-xxl-3 p-2 mt-2">
        <dl class="row">
            <dt class="col-6">{{translate('sub_total')}} :</dt>
            <dd class="col-6 text-right">{{ Helpers::set_symbol($subtotal) }}</dd>


            <dt class="col-6">{{translate('product')}} {{translate('discount')}}:
            </dt>
            <dd class="col-6 text-right"> - {{ Helpers::set_symbol(round($discount_on_product,2)) }}</dd>
            <dt class="col-6">{{translate('Coupon')}} {{translate('Discount')}}:
            </dt>
            <dd class="col-6 text-right text-base mb-0">
                @if(!empty($coupon_discount))
                    <button class="btn btn-sm p-0 text-danger" type="button" data-toggle="modal"
                            data-target="#delete-coupon">
                        <i class="tio-delete"></i>
                    </button>
                @endif
                <?php
                $customerId = session()->get('customer_id');
                $isGuest = $customerId == 0 || $customerId == null;
                $customerWalletBalance = App\Models\User::with('userAccount')->where('id', $customerId)->first()?->userAccount?->wallet_balance ?? 0;
                ?>
                <button
                    class="btn btn-sm px-1 text-base pos-open-coupon-discount-modal"
                    id="add-coupon-button"
                    type="button"
                    @if($isGuest)
                        disabled
                    @else
                        data-toggle="modal"
                    data-target="#couponAdd-discount"
                    @endif
                >
                    <i class="tio-edit mr-1"
                       @if($isGuest)
                           data-toggle="tooltip" title="{{ translate('Coupon discounts are available for registered users only.') }}"
                        @endif
                    ></i>
                </button>
                {{ Helpers::set_symbol($coupon_discount) }}
            </dd>
            <dt class="col-6">{{translate('extra')}} {{translate('discount')}}:
            </dt>
            <dd class="col-6 text-right text-base">
                @if(!empty($extra_discount))
                    <button class="btn btn-sm p-0 text-danger" type="button" data-toggle="modal"
                            data-target="#delete-discount">
                        <i class="tio-delete"></i>
                    </button>
                @endif

                <button class="btn btn-sm px-1 text-base pos-open-discount-modal" type="button" data-toggle="modal"
                        data-target="#add-discount">
                    <i class="tio-edit"></i>
                </button>

                - {{ Helpers::set_symbol($extra_discount) }}
            </dd>
            <dt class="col-6">{{translate('tax')}} :</dt>
            <dd class="col-6 text-right total-tax" data-total-tax="{{ $total_tax }}">{{ Helpers::set_symbol(round($total_tax,2)) }}</dd>

            <dt class="col-6 font-weight-bold fs-16 border-top pt-2">{{translate('total')}} :</dt>
            <dd class="col-6 text-right font-weight-bold fs-16 border-top m-0 pt-2 total-amount" data-total-amount="{{ $total + $total_tax }}">{{ Helpers::set_symbol(round($total+$total_tax, 2)) }}</dd>
        </dl>
    </div>
</div>


<div class="box mb-3">
    <form action="{{route('branch.pos.order')}}" id='order_place' method="post">
        @csrf
        <div class="bg-color-common rounded py-3 px-xxl-4 px-3">
            <div class="mb-4">
                <div class="text-dark d-flex mb-3">{{ translate('Paid By') }}</div>
                <ul class="list-unstyled option-buttons">
                    <li>
                        <input type="radio" id="cash" value="cash" name="type" hidden=""  checked>
                        <label for="cash" class="btn border px-4 mb-0">{{ translate('Cash') }}</label>
                    </li>
                    <li>
                        <input type="radio" value="card" id="card" hidden="" name="type" >
                        <label for="card" class="btn border px-4 mb-0">{{ translate('Card') }}</label>
                    </li>
                    <li class="{{ (float)$customerWalletBalance <= 0.0 ? 'd-none' : '' }}">
                        <input type="radio" value="{{ (float)$customerWalletBalance < round($total+$total_tax, 2) ? 'multiple' : 'wallet' }}" id="wallet" hidden="" name="type" >
                        <label for="wallet" class="btn border px-4 mb-0">{{ translate('wallet') }}</label>
                    </li>
                </ul>
            </div>
            <div class="bg-white rounded py-3 px-3 rounded mb-20 available-balance-section d-none">
                <div class="d-flex flex-wrap align-items-center gap-1 text-title2">
                    {{ translate('Available Balance') }}
                    <div class="d-flex align-items-center gap-2">
                        <span class="font-semibold text-title fs-14 available-wallet-balance">{{ \App\CentralLogics\Helpers::set_symbol($customerWalletBalance ?? 0) }}</span>
                        <span class="text-danger fs-14 used-wallet-balance">{{ (float)$customerWalletBalance < round($total+$total_tax, 2) ? '(Used ' . \App\CentralLogics\Helpers::set_symbol($customerWalletBalance ?? 0) . ')' : '(Used ' . Helpers::set_symbol(round($total+$total_tax, 2)) . ')' }}</span>
                    </div>
                </div>
            </div>
            <div id="cart-payment-section">
                @include('branch-views.pos.partials.cart-payment-section', ['customerWalletBalance' => $customerWalletBalance, 'cartTotalAmount' => round($total+$total_tax, 2), 'paymentType' => 'cash'])
            </div>
        </div>

        <div class="pos-cart-bottom-btns bg-white shadow">
            <div class="row g-2">
                <div class="col-sm-6">
                    <a href="javascript:" class="btn btn-danger btn-block pos-empty-cart"><i
                            class="fa fa-times-circle"></i> {{translate('Cancel_Order')}} </a>
                </div>
                <div class="col-sm-6" id="placeOrder">
                    <button type="submit" class="btn  btn-primary btn-block" ><i class="fa fa-shopping-bag"></i>
                        {{translate('Place_Order')}} </button>
                </div>
                <div class="col-sm-6 d-none" id="disablePlaceOrder">
                    <button type="button" class="btn  btn-primary btn-block" disabled data-toggle="tooltip" title="Paid amount must be equal or greater than total amount."><i class="fa fa-shopping-bag"></i>
                        {{translate('Place_Order')}} </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="delete-coupon" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mxwidth">
        <div class="modal-content shadow-sm pb-sm-3">
            <div class="modal-header p-0">
                <button type="button" class="close w-35 h-35 rounded-circle d-flex align-items-center justify-content-center bg-light position-relative" data-dismiss="modal" aria-label="Close" style="top: 10px; inset-inline-end: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('public/assets/admin/img/delete-warning.png')}}" alt="" class="mb-3">
                <h3 class="mb-2">{{ translate('Remove Coupon') }}?</h3>
                <p class="m-0">{{ translate('Are you sure you want to remove this coupon') }}?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 gap-2">
                <button type="button" class="btn min-w-120 btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <a href="{{ route('branch.pos.delete-coupon') }}" class="btn min-w-120 btn-danger">{{ translate('Remove') }}</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete-discount" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-mxwidth">
        <div class="modal-content shadow-sm pb-sm-3">
            <div class="modal-header p-0">
                <button type="button" class="close w-35 h-35 rounded-circle d-flex align-items-center justify-content-center bg-light position-relative" data-dismiss="modal" aria-label="Close" style="top: 10px; inset-inline-end: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('public/assets/admin/img/delete-warning.png')}}" alt="" class="mb-3">
                <h3 class="mb-2">{{ translate('Remove Discount') }}?</h3>
                <p class="m-0">{{ translate(' Are you sure you want to remove this discount') }}?</p>
            </div>
            <div class="modal-footer justify-content-center border-0 gap-2">
                <button type="button" class="btn min-w-120 btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <a href="{{ route('branch.pos.delete-discount') }}" class="btn min-w-120 btn-danger">{{ translate('Remove') }}</a>
            </div>
        </div>
    </div>
</div>


<div class="modal modal-custom-position fade" id="add-discount" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{translate('update_discount')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('branch.pos.discount')}}" method="post" class="row g-2" id="discount_form">
                    <div class="form-group col-sm-12 m-0">
                        <label for="">{{translate('discount')}}</label>
                        <div class="d-flex align-items-center gap-0 border rounded">
                            <input type="number"
                                   value="{{session()->get('cart')['extra_discount'] ?? 0}}"
                                   class="form-control border-0" name="discount"
                                   step="0.01"
                                   onkeydown="return !['e','E','+','-'].includes(event.key)"
                                   oninput="
                                                   if (this.value < 0) this.value = 0;
                                                   if (this.value.includes('.')) {this.value = this.value.split('.').map((part, index) => index === 1 ? part.slice(0, 2) : part).join('.');}
                                                   "
                            >
                            <select name="type" class="form-control bg-light w-auto custom-select border-0">
                                <option value="amount" {{$extra_discount_type=='amount'?'selected':''}}>{{translate('amount')}}({{Helpers::currency_symbol()}})</option>
                                <option value="percent" {{$extra_discount_type=='percent'?'selected':''}}>{{translate('percent')}}(%)</option>
                            </select>
                        </div>
                        <span class="error-text" data-error="discount_error"></span>
                    </div>

                    <div class="badge badge-soft-warning p-2 d-flex align-items-center gap-1 text-wrap text-left lh-1.3 mb-4 mt-3">
                        <i class="tio-lightbulb"></i>
                        <span class="text-dark opacity-lg font-weight-normal fs-13 text-for-amount {{$extra_discount_type=='amount' || $extra_discount_type== '' ? '':'d-none'}}">
                            {{ translate('Maximum discount') }} <strong>{{ Helpers::set_symbol(round($totalToShowInDiscountModal, 2)) }}</strong> {{ translate('is based on your total price amount') }}
                        </span>
                        <span class="text-dark opacity-lg font-weight-normal fs-13 text-for-percent {{$extra_discount_type=='percent'?'':'d-none'}}">
                            {{ translate('Extra discount can not be more than') }} <strong>{{ translate('100') }}</strong> {{ translate('percent') }}
                        </span>
                    </div>

                    <div class="d-flex justify-content-end gap-3 col-sm-12">
                        <button type="button" class="btn min-w-120 btn--reset" data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button class="btn min-w-120 btn-primary" type="submit">{{translate('Apply Discount')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
