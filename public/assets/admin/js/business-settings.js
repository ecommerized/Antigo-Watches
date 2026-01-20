$('#shipping_by_distance_status').on('click',function(){
    $("#delivery_charge").prop('disabled', true);
    $("#min_shipping_charge").prop('disabled', false);
    $("#shipping_per_km").prop('disabled', false);
});

$('#default_delivery_status').on('click',function(){
    $("#delivery_charge").prop('disabled', false);
    $("#min_shipping_charge").prop('disabled', true);
    $("#shipping_per_km").prop('disabled', true);
});

$(document).ready(function () {
    $("#phone_verification_on").click(function () {
        if ($('#email_verification_on').prop("checked") == true) {
            $('#email_verification_off').prop("checked", true);
            $('#email_verification_on').prop("checked", false);
            const message = "Both Phone & Email verification can't be active at a time";
            toastr.info(message);
        }
    });
    $("#email_verification_on").click(function () {
        if ($('#phone_verification_on').prop("checked") == true) {
            $('#phone_verification_off').prop("checked", true);
            $('#phone_verification_on').prop("checked", false);
            const message = "Both Phone & Email verification can't be active at a time";
            toastr.info(message);
        }
    });
});

$('.currency-symbol-position').on('click', function (){
    let route = $(this).data('route');

    $.get({
        url: route,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            toastr.success(data.message);
        },
        complete: function () {
            $('#loading').hide();
        },
    });
});

function readURL(input, viewer) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#' + viewer).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").change(function () {
    readURL(this, 'viewer');
});
$("#customFileEg2").change(function() {
    readURL(this, 'viewer_2');
});
$("#customFileEg3").change(function() {
    readURL(this, 'viewer_3');
});

$("#language").on("change", function () {
    $("#alert_box").css("display", "block");
});
