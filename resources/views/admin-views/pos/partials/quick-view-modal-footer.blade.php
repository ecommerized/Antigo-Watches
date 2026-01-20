<div class="w-100">
    <div class="row ">
        <div class="col-12">
            <div class="row no-gutters text-dark d-flex align-items-center" id="">
                <div class="col">
                    <div class="product-description-label h5 font-weight-light mb-0">{{translate('Total Amount')}}
                        :
                    </div>
                </div>
                <div class="col">
                    <div class="product-price text-right text-primary h2 font-weight-bold mb-0">
                        <strong id="chosen_price">{{ number_format(num: $price, decimals: 2, thousands_separator: '') }}</strong> {{ Helpers::currency_symbol() }}
                    </div>
                </div>
            </div>

            <div
                class="row no-gutters mt-3 text-dark flex-row-reverse d-flex align-items-center justify-content-center"
                id="">
                <div class="col text-right">
                    <div class="max-w-270 ml-auto">
                        <button
                            class="btn d-flex align-items-center gap-2 justify-content-center btn-primary font-weight-bold w-em-100 ml-auto {{ $stock < 1 ? 'disabled' : 'add-to-shopping-cart' }}"
                            type="button">
                            <i class="tio-shopping-cart"></i>
                            <div class="d-em-block d-none text-nowrap">
                                {{ $buttonText }}
                            </div>
                        </button>
                    </div>
                </div>
                <div class="col d-flex justify-content-center">
                    <div class="product-quantity d-flex align-items-center">
                        <div class="d-flex justify-content-center align-items-center gap-3" id="quantity_div">
                            <button class="btn btn-number py-1 px-2 text-dark" type="button"
                                    data-type="minus" data-field="quantity"
                                    {{ $quantity <= 1 ? 'disabled' : '' }}>
                                <i class="tio-remove font-weight-bold"></i>
                            </button>
                            <input type="text" name="quantity" id="quantity"
                                   class="form-control h-30px input-number text-center px-2 cart-qty-field min-w-35 w-25"
                                   placeholder="1" value="{{ $quantity }}" min="1" max="{{ $stock }}" onfocus="storeOldValue(this)">
                            <div class="tooltip-wrapper position-relative d-inline-block">
                                <button class="btn btn-number py-1 px-2 text-dark" type="button" data-type="plus"
                                        data-field="quantity">
                                    <i class="tio-add font-weight-bold"></i>
                                </button>

                                <!-- Tooltip -->
                                <div class="custom-tooltip">
                                    <div class="tooltip-body">
                                        <div class="tooltip-icon">⚠️</div>
                                        <div class="tooltip-content">
                                            <div class="h5 font-weight-light">{{translate("Warning")}}</div>
                                            <div class="fs-12">
                                                There isn’t enough quantity on stock.<br>Only <span
                                                    class="total-stock">{{ $stock }}</span> is
                                                available.
                                            </div>
                                        </div>
                                    </div>
                                    <span class="tooltip-close" onclick="this.parentElement.style.display='none'">&times;</span>
                                    <div class="tooltip-arrow"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
