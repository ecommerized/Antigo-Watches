$('.show-identification-image').on('click', function (){
    let image_location = $(this).data('image');
    console.log(image_location);
    $('#identification_image_view_modal').modal('show');
    if(image_location != null || image_location !== '') {
        $('#identification_image_element').attr("src", image_location);
    }
})

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#viewer').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").change(function () {
    readURL(this);
});



