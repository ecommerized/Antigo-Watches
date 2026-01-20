<?php
    $cartProduct = $cartProducts->where('id', $product->id)->values();
?>
<div class="pos-product-item card pos-single-product-card {{ 'product-id-' . $product->id }}" data-id="{{$product->id}}">
    <div class="pos-product-item_thumb d-center position-relative">
        <img src="{{$product['image_fullpath'][0]}}"
                 class="img-fit"
             alt="{{ translate('product') }}">
        <div class="hover-add-cart position-absolute">
            <button class="btn p-0 bg-transparent font-weight-bolder fs-16 text-nowrap text-white text-add-to-cart {{ (empty($cartProduct) || $cartProduct->isEmpty()) ? '' : 'd-none' }}" type="button">
                Add to cart
            </button>
        </div>

        <div class="total-cart-count {{ (!empty($cartProduct) && $cartProduct->isNotEmpty()) ? '' : 'd-none' }}">
            <div class="btn p-0 bg-white fs-14 font-weight-bolder text-dark w-35 h-35 rounded-circle mx-auto d-center count-product">
                {{ $cartProduct->sum('quantity') ?? 0 }}
            </div>
        </div>

    </div>

    <div class="pos-product-item_content clickable">
        <div class="pos-product-item_title">
            {{ Str::limit($product['name'], 15) }}
        </div>
        <div class="pos-product-item_price">
            {{ Helpers::set_symbol($product['price']- Helpers::discount_calculate($product, $product['price'])) }}
        </div>
    </div>
</div>





