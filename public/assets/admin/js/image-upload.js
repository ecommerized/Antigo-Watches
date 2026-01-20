function readURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();

        reader.onload = function (e) {
            $('#viewer').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#customFileEg1").change(function () {
    readURL(this);
});

$('.delete_file_input').click(function () {
    let $parentDiv = $(this).closest('div');
    $parentDiv.find('input[type="file"]').val('');
    $parentDiv.find('.img_area_with_preview img').attr("src", " ");
    $(this).hide();
});

$('.custom-upload-input-file').on('change', function(){
    if (parseFloat($(this).prop('files').length) !== 0) {
        let $parentDiv = $(this).closest('div');
        $parentDiv.find('.delete_file_input').fadeIn();
    }
    let $parentDiv = $(this).closest('div');
    uploadColorImage($parentDiv, $(this));
})


function uploadColorImage($parentDiv, thisData) {
    if (thisData && thisData[0].files.length > 0) {
        $parentDiv.find('.img_area_with_preview img').attr("src", window.URL.createObjectURL(thisData[0].files[0]));
        $parentDiv.find('.img_area_with_preview img').removeClass('d-none');
        $parentDiv.find('.existing-image-div img').addClass('d-none');
        $parentDiv.find('.delete_file_input').fadeIn();
    }
}


// $(document).ready(function () {
//     // Hide .btn-group-uplod by default
//     $('.upload--img-wrap .btn-group-uplod').hide();
//
//     // When file is uploaded
//     $('.custom-upload-input-file').on('change', function () {
//         let $wrap = $(this).closest('.upload--img-wrap');
//
//         if (this.files && this.files.length > 0) {
//             let imgSrc = window.URL.createObjectURL(this.files[0]);
//             $wrap.find('.img_area_with_preview img')
//                 .attr("src", imgSrc)
//                 .removeClass('d-none');
//
//             $wrap.find('.existing-image-div img').addClass('d-none');
//             $wrap.find('.btn-group-uplod').fadeIn();
//
//             // Disable input so no reupload without edit click
//             $(this).prop('disabled', true);
//         }
//     });
//
//     // Edit/Reupload click → enable input & open picker
//     $('.upload--img-wrap').on('click', '.edit-reupload', function () {
//         let $fileInput = $(this).closest('.upload--img-wrap')
//                                 .find('.custom-upload-input-file');
//
//         $fileInput.prop('disabled', false).val(''); // re-enable & clear
//         $fileInput.trigger('click');
//     });
//
//     // Delete click
//     $('.upload--img-wrap').on('click', '.delete-img', function () {
//         let $wrap = $(this).closest('.upload--img-wrap');
//         let $fileInput = $wrap.find('.custom-upload-input-file');
//
//         $fileInput.val('').prop('disabled', false); // clear & re-enable
//
//         $wrap.find('.img_area_with_preview img')
//              .attr("src", "")
//              .addClass('d-none');
//
//         $wrap.find('.existing-image-div img').removeClass('d-none');
//         $wrap.find('.btn-group-uplod').fadeOut();
//     });
// });
