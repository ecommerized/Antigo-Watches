$('#redirection_type').on('change', function (){
    let type = $(this).val();
    console.log(type);
    show_item(type);
})

function show_item(type) {
    if (type === 'product') {
        $('.type-product').show();
        $('.type-category').hide();
    } else {
        $('.type-product').hide();
        $('.type-category').show();
    }
}

$('#banner_type').on('change', function (){
    let type = $(this).val();
    banner_select(type);
})

function banner_select(type) {
    if (type === 'primary') {
        $("#primary_banner").show();
        $("#secondary_banner").hide();
    } else {
        $("#primary_banner").hide();
        $("#secondary_banner").show();
    }
}
