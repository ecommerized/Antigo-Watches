$(document).on('ready', function () {
    $('.js-flatpickr').each(function () {
        $.HSCore.components.HSFlatpickr.init($(this));
    });
});

$(document).ready(function () {
    let CouponType = $('.coupon-type').val();
    if (CouponType === 'first_order') {
        $('#limit-for-user').hide();
    } else {
        $('#limit-for-user').show();
    }

    let discountType = $('#discount_type').val();
    if (discountType === 'amount') {
        $('#max_discount_div').hide();
    } else {
        $('#max_discount_div').show();
    }
})

$("#discount_type").change(function(){
    if(this.value === 'amount') {
        $("#max_discount_div").hide();
    }
    else if(this.value === 'percent') {
        $("#max_discount_div").show();
    }
});

$('.coupon-type').change(function(){
    let type = (this.value);
    if(type==='first_order'){
        $('#limit-for-user').hide();
        $('#user-limit').removeAttr('required');
    }else{
        $('#user-limit').prop('required',true);
        $('#limit-for-user').show();
    }
});

$('.generate-code').on('click', function (){
    let code = Math.random().toString(36).substring(2,12);
    $('#code').val(code)
})

$('#start_date,#expire_date').change(function () {
    let from = $('#start_date').val();
    let to = $('#expire_date').val();
    if (from !== '' && to !== '') {
        if (from > to) {
            $('#start_date').val('');
            $('#expire_date').val('');
            toastr.error('Invalid date range!', Error, {
                CloseButton: true,
                ProgressBar: true
            });
        }
    }
});



