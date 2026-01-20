@if($paymentType == 'wallet' || $paymentType == 'card' || $paymentType == 'cash')
    <div class="collect-cash-section pb-80 pb-sm-3" style="display: block">
        <div class="form-group mb-2 d-flex align-items-center justify-content-between gap-2">
            <label class="w-50 mb-0">{{translate('paid_amount')}} :</label>
            <input type="number" class="form-control max-w-155 text-right" name="show_paid_amount" step="0.01" id="showPaidAmount" value="{{ round($cartTotalAmount, 2) }}"
                   required="" {{ $paymentType == 'cash' ? '' : 'readonly' }}>
            <input type="hidden" class="hidden-paid-amount" name="paid_amount" id="paidAmount" value="{{ round($cartTotalAmount, 2) }}">
            <input type="hidden" class="hidden-paid-amount" id="totalAmount" value="{{ round($cartTotalAmount, 2) }}">
        </div>
        <div class="form-group d-flex align-items-center justify-content-between gap-2">
            <label class="due-or-change-amount w-50 mb-0">{{translate('change_amount')}} :</label>
            <input type="number" class="form-control text-right w-50 border-0 shadow-none" id="amount-difference" value="0.00" step="0.01" readonly="" required="">
        </div>
    </div>
@else
    <div class="collect-cash-section pb-80 pb-sm-3" style="display: block">
        <div class="form-group mb-2 d-flex align-items-center justify-content-between gap-2">
            <label class="w-50 mb-0">{{translate('paid_amount')}} :</label>
            <input type="number" class="form-control max-w-155 text-right" name="" step="0.01" id="" value="{{ min((float)$customerWalletBalance, round($cartTotalAmount, 2)) }}"
                   readonly >
        </div>
        <div class="form-group d-flex align-items-center justify-content-between gap-2">
            <label class="due-or-change-amount w-50 mb-0">{{translate('remaining_balance')}} :</label>
            <input type="number" class="form-control text-right w-50 border-0 shadow-none" value="{{ round($cartTotalAmount, 2) - (float)$customerWalletBalance }}" step="0.01" readonly="" >
        </div>
    </div>
    <div class="bg-white rounded py-3 px-3 rounded mb-20 additional-payment-section">
        <div class="mb-4">
            <div class="text-dark d-flex mb-3">{{ translate('Pay Remaining Balance By') }}:</div>
            <ul class="list-unstyled option-buttons">
                <li>
                    <input type="radio" id="additional-payment-cash" value="cash" name="additional_payment_type" hidden=""  checked>
                    <label for="additional-payment-cash" class="btn border px-4 mb-0">{{ translate('Cash') }}</label>
                </li>
                <li>
                    <input type="radio" value="card" id="additional-payment-card" hidden="" name="additional_payment_type" >
                    <label for="additional-payment-card" class="btn border px-4 mb-0">{{ translate('Card') }}</label>
                </li>
            </ul>
        </div>
        <div class="collect-cash-section pb-80 pb-sm-3" style="display: block">
            <div class="form-group mb-2 d-flex align-items-center justify-content-between gap-2">
                <label class="w-50 mb-0">{{translate('paid_amount')}} :</label>
                <input type="number" class="form-control max-w-155 text-right" name="additional_amount" step="0.01" id="showPaidAmount" value="{{ round($cartTotalAmount, 2) - (float)$customerWalletBalance }}"
                       required="" >
                <input type="hidden" class="hidden-paid-amount" name="paid_amount" id="paidAmount" value="{{ round($cartTotalAmount, 2) - (float)$customerWalletBalance }}">
                <input type="hidden" class="hidden-paid-amount" id="totalAmount" value="{{ round($cartTotalAmount, 2) - (float)$customerWalletBalance }}">
            </div>
            <div class="form-group d-flex align-items-center justify-content-between gap-2">
                <label class="due-or-change-amount w-50 mb-0">{{translate('change_amount')}} :</label>
                <input type="number" class="form-control text-right w-50 border-0 shadow-none" id="amount-difference" value="0.00" step="0.01" readonly="" required="">
            </div>
        </div>
    </div>
@endif
