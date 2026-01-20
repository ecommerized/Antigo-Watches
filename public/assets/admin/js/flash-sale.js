$(document).on('ready', function () {
    $('.product-search-result').on('click', function() {
        $(this).siblings('input').focus();
    })
})

$(document).ready(function() {
    $('#flash-sale-product-store').click(function(event) {
        $('#product_store').submit();
    });
});

$(document).ready(function() {
    $("#product-search").on("input", function() {
        var searchQuery = $(this).val().toLowerCase();

        $(".product-search-result .result").each(function() {
            var productName = $(this).find("h6").text().toLowerCase();
            if (productName.includes(searchQuery)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
