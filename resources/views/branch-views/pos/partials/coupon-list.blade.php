@if($coupons->isNotEmpty())
    <h5 class="font-semibold mb-2 text-title">{{ translate('Select Coupons') }}</h5>
    <div class="coupon-slide-wrap position-relative">
        <div class="coupon-inner pt-2 d-flex align-items-center gap-3 flex-nowrap text-nowrap">
            @foreach($coupons as $key => $coupon)
                <a href="javascript:" class="coupon-slide_items w-100 position-relative d-flex justify-content-between gap-4 align-items-center {{ isset(session('cart')['coupon_code']) ? 'x' : 'y' }} {{ (isset(session('cart')['coupon_code']) && $coupon->code == session('cart')['coupon_code']) ? 'active' : '' }}">
                    <div class="check-badge position-absolute">
                        <i class="tio-checkmark-circle text-primary fs-18"></i>
                    </div>
                    <div class="w-60">
                        <h5 class="mb-1 font-weight-lighter coupon-code" data-coupon-code="{{ $coupon->code }}">{{ translate('Code') }}: {{ $coupon->code }}</h5>
                        @if($coupon->coupon_type == 'first_order')
                            <p class="m-0 text-price fs-12">{{ translate('Use it in 1st order') }}</p>
                        @endif
                    </div>
                    <div class="line"></div>
                    <div class="text-center min-w-60px mr-1">
                        <h4 class="text-primary m-0 font-800 lh-1">{{ $coupon->discount }}{{ $coupon->discount_type == 'percent' ? '%' :\App\CentralLogics\Helpers::currency_symbol() }}</h4>
                        <span class="text-primary fs-10 m-0 d-block font-weight-bold">{{ translate('Discount') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="arrow-area">
            <div class="button-prev align-items-center">
                <button type="button" class="btn btn-click-prev mr-auto btn-primary w-25px h-25px min-w-25px rounded-circle fs-12 p-2 d-center">
                    <i class="tio-arrow-backward top-02"></i>
                </button>
            </div>
            <div class="button-next align-items-center">
                <button type="button" class="btn btn-click-next ml-auto btn-primary w-25px h-25px min-w-25px rounded-circle fs-12 p-2 d-center">
                    <i class="tio-arrow-forward top-02"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="text-base text-center mt-3 mb-20 fs-14">{{ translate('Or enter a coupon code') }}</div>
@endif

