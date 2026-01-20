$('.change-social-login-status').on('click', function (){
    let route = $(this).data('route');
    change_social_login_status(route)
});

function change_social_login_status(route) {
    $.get({
        url: route,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            setTimeout(function () {
                location.reload(true);
            }, 1000);
            toastr.success(data.message);
        },
        complete: function () {
            $('#loading').hide();
        },
    });
}
