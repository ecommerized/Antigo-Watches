<div id="add-to-cart-form">
    <div class="modal-header p-2">
        <h4 class="modal-title product-title"></h4>
        <button class="close call-when-done" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body pt-1">
        <div class="media flex-wrap gap-3">
            <div class="box-120 rounded border">
                <img class="img-fit rounded"
                     src="{{$product['image_fullpath'][0]}}"
                     data-zoom="{{$product['image_fullpath'][0]}}"
                     alt="{{ translate('Product image') }}">
                <div class="cz-image-zoom-pane"></div>
            </div>

            <div class="details media-body">
                <h5 class="product-name mb-1"><a href="#"
                                                 class="h3 mb-0 product-title fs-16">{{ Str::limit($product->name, 100) }}</a>
                </h5>
                <div class="mb-1">
                    @if($product->discount > 0)
                        <span class="h3 font-weight-normal fs-14 text-price text-decoration-line-through">
                    {{ Helpers::set_symbol($product['price']) }}
                </span>
                    @endif
                    <span class="h2 fs-20 text-title">
                    {{ Helpers::set_symbol(($product['price']- Helpers::discount_calculate($product, $product['price']))) }}
                </span>
                </div>
                <div class="mb-0 text-price fs-14">
                    <span
                        class="stock-badge">{{ translate('Stock_Qty') }} : <strong><span class="total-stock text-dark">{{ $stock }}</span></strong></span>
                </div>
                <!-- Description -->
                <div class="row pt-lg-3 pt-2">
                    <div class="col-12">
                        <div class="mb-3">
                            <?php
                            $cart = false;
                            if (session()->has('cart')) {
                                foreach (session()->get('cart') as $key => $cartItem) {
                                    if (is_array($cartItem) && $cartItem['id'] == $product['id']) {
                                        $cart = $cartItem;
                                    }
                                }
                            }
                            ?>
                            <h2 class="fs-14 mb-1">{{translate('description')}}</h2>
                            <article>
                                <p class="d-block text-dark fs-12 m-0" id="description-{{ $product->id }}">
                                <span id="description-text-{{ $product->id }}">
                                    {!! \App\CentralLogics\Helpers::trimWords($product->description)['text'] !!}
                                </span>
                                    @if(Helpers::trimWords($product->description)['isTruncated'])
                                        <a href="javascript:void(0);"
                                           class="badge badge-soft-primary border-0 align-baseline fs-12 font-weight-light quick-view-see-more-button"
                                           id="see-more-btn-{{ $product->id }}"
                                           data-truncated="true">{{ translate('See More') }}</a>
                                    @endif
                                </p>
                            </article>
                        </div>
                        <div class="{{ count(json_decode($product->choice_options)) > 0 ? 'border rounded p-3' : '' }}">
                            <input type="hidden" name="id" value="{{ $product->id }}">
                            @foreach (json_decode($product->choice_options) as $key => $choice)
                                <h3 class="mb-2 pt-0 fs-14">{{ $choice->title }}</h3>
                                <div class="d-flex gap-3 flex-wrap mb-3">
                                    @foreach ($choice->options as $key => $option)
                                        <input class="btn-check" type="radio"
                                               id="{{ $choice->name }}-{{ $option }}"
                                               name="{{ $choice->name }}" value="{{ $option }}"
                                               @if($key == 0) checked @endif autocomplete="off">
                                        <label class="check-label rounded px-2 py-1 text-center lh-1.3 mb-0 choice-input"
                                               for="{{ $choice->name }}-{{ $option }}">{{ $option }}</label>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal-footer w-100 border-0 bg-white" id="quick-view-modal-footer">
        @include('branch-views.pos.partials.quick-view-modal-footer')
    </div>
</div>




<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('change', '#add-to-cart-form input[type="radio"]', function () {
            getVariantPrice(true);
        });

        $(document).on('change', '#add-to-cart-form input[name="quantity"]', function () {
            getVariantPrice();
        });

        $(document).off('click', '.add-to-shopping-cart').on('click', '.add-to-shopping-cart', function () {
            addToCart();
        });

        $(document).off('click', '.quick-view-see-more-button').on('click', '.quick-view-see-more-button', function () {
            var button = $(this);
            var productId = button.attr('id').split('-').pop();
            var descriptionText = $('#description-text-' + productId);
            var isTruncated = button.data('truncated');

            if (isTruncated) {
                var fullText = {!! json_encode(\App\CentralLogics\Helpers::trimWords($product->description, 0)["text"]) !!};
                descriptionText.html(fullText);
                button.text('{{ translate('See Less') }}');
                button.data('truncated', false);
            } else {
                var truncatedText = {!! json_encode(\App\CentralLogics\Helpers::trimWords($product->description, 50)["text"]) !!};
                descriptionText.html(truncatedText);
                button.text('{{ translate('See More') }}');
                button.data('truncated', true);
            }
        });
    });

</script>

