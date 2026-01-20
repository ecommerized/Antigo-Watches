$(document).ready(function () {
    let targetOffcanvas = null;
    $(document).on('click', '.offcanvas-trigger', function (e) {
        e.preventDefault();
        targetOffcanvas = $(this).data('target');
        $('#edit-product-modal').modal('show');
    });

    $('#edit-product-modal .btn-primary').on('click', function () {
        $('#edit-product-modal').modal('hide');
        if (targetOffcanvas) {
            $(targetOffcanvas).addClass('open');
            $('#offcanvasOverlay').addClass('show');
        }
    });

    $(document).on('click', '.offcanvas-close, #offcanvasOverlay', function () {
        location.reload();
    });
});

function manageQuantity() {
    $('.btn-number').click(function () {
        var $btn = $(this);
        var $input = $btn.closest('.input-group').find('.input-number');
        var $row = $input.closest('tr');
        var val = parseInt($input.val()) || 1;
        var min = parseInt($input.attr('min')) || 1;
        var max = parseInt($input.data('maximum_quantity')) || 9999;

        if ($btn.find('i').hasClass('tio-add')) {
            if (val < max) {
                val++;
            } else {
                toastr.error($('.data-to-js').data('max-limit-message'), {
                    CloseButton: true,
                    ProgressBar: true
                });
            }
        } else {
            if (val > min) {
                val--;
            } else {
                toastr.error($('.data-to-js').data('min-limit-message'), {
                    CloseButton: true,
                    ProgressBar: true
                });
            }
        }

        $input.val(val);
        updateTotal(val, $input);
        if (val == max) {
            $row.addClass('max-limit');
        } else {
            $row.removeClass('max-limit');
        }
    });

    $('.input-number').on('change', function () {
        let $input = $(this);
        var $row = $input.closest('tr');
        let minValue = parseInt($input.attr('min')) || 0;
        let maxValue = parseInt($input.data('maximum_quantity')) || 9999;
        let valueCurrent = parseInt($input.val());

        if (isNaN(valueCurrent)) {
            $input.val(maxValue);
            updateTotal(maxValue);
            $row.addClass('max-limit');
            return;
        }

        if (valueCurrent < minValue) {
            $input.val(minValue);
            updateTotal(minValue, $input);
            toastr.error($('.data-to-js').data('min-limit-message'), {
                CloseButton: true,
                ProgressBar: true
            });
        } else if (valueCurrent > maxValue) {
            $input.val(maxValue);
            updateTotal(maxValue, $input);
            $row.addClass('max-limit');
            toastr.error($('.data-to-js').data('max-limit-message'), {
                CloseButton: true,
                ProgressBar: true
            });
        } else {
            updateTotal(valueCurrent, $input);
            $row.removeClass('max-limit');
        }
    });


    $(".input-number").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190]) !== -1 ||
            // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
            // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
}

function updateTotal(quantity, $input) {
    var $row = $input.closest('tr');
    let rowIndex = $row.data('id');
    var currencySymbol = $('.data-to-js').data('currency-symbol') || '$';
    var symbolAtEnd = $('.data-to-js').data('currency-symbol-position') == 'right';
    var newTotal = ($input.data('base-price') * quantity).toFixed(2);
    var formattedTotal = newTotal.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    var displayTotal = symbolAtEnd ? formattedTotal + currencySymbol : currencySymbol + formattedTotal;
    $row.find('.product-total-price').text(displayTotal);

    var newTotalDiscount = ($input.data('discount-price') * quantity).toFixed(2);
    var formattedTotalDiscount = newTotalDiscount.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    var displayTotalDiscount = symbolAtEnd ? formattedTotalDiscount + currencySymbol : currencySymbol + formattedTotalDiscount;
    $row.find('.product-total-discount-price').text(displayTotalDiscount);

    let $dataToJs = $('.data-to-js');
    let existedProducts = JSON.parse($dataToJs.attr('data-product-details') || '[]');
    existedProducts[rowIndex].quantity = quantity;
    $dataToJs.attr('data-product-details', JSON.stringify(existedProducts));
}

manageQuantity();


//Edit Search
$(function () {
    const $searchInput = $('.edit-search-form input[name="search"]');
    const $searchWrap = $('.search-wrap-manage');

    // Show on focus
    $searchInput.on('focus', function () {
        $searchWrap.show();
    });

    // Hide on click outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.edit-search-form').length) {
            $searchWrap.hide();
        }
    });
});






