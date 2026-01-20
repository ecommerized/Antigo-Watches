@if(isset($products))
    @forelse($products as $key => $product)
        <div class="{{ !empty(json_decode($product->variations, true)) ? 'quick-view' : '' }} {{ $product->total_stock <= 0 ? 'unavailable' : ($existedProducts->contains('id', $product->id) ? 'active' : '')  }} search-item d-flex align-items-sm-center gap-2 p-2 border rounded cursor-pointer {{ $product->total_stock > 0 && empty(json_decode($product->variations, true)) ? 'add-searched-product' : ''}}"
             data-id="{{ $product->id }}"
             data-name="{{ $product->name }}"
             data-quantity="1"
             data-variant=""
             data-price="{{ \App\CentralLogics\Helpers::set_symbol($product->price) }}"
             data-discount="{{ ($product->discount_type == 'percent' ? ($product->price * $product->discount) / 100 : $product->discount) }}"
             data-discount-price="{{ $product->price - ($product->discount_type == 'percent' ? ($product->price * $product->discount) / 100 : $product->discount) }}"
             data-image="{{ $product['image_fullpath'][0] }}"
             data-stock="{{ $product->total_stock }}"
             data-base-price="{{ $product->price }}"
             data-total-discount-price="{{ \App\CentralLogics\Helpers::set_symbol($product->price - ($product->discount_type == 'percent' ? ($product->price * $product->discount) / 100 : $product->discount)) }}"
        >
            <div class="list-items-media cursor-pointer">
                <div class="thumb {{ $product->total_stock <= 0 ? 'd-center' : '' }} position-relative rounded overflow-hidden w-65px h-65px">
                    <img width="55" height="55"
                         src="{{ $product['image_fullpath'][0] }}" alt="image"
                         class="rounded">
                    @if($product->total_stock <= 0)
                        <div class="text-white fs-10 font-medium position-absolute unavail">Stock Out
                        </div>
                    @endif
                </div>
            </div>
            <div
                class="d-flex w-100 flex-sm-nowrap flex-wrap align-items-center justify-content-between search-items-body">
                <div class="cont d-flex flex-column gap-0">
                    <p class="fs-14 text-dark mb-0 max-w-440 line--limit-1">{{ $product->name }} </p>
                    <div class="fs-12">{{ translate('Stock Qty') }} : <span class="text-dark">{{ $product->total_stock }}</span></div>
                </div>
                <div class="text-sm-right cont d-flex flex-column gap-0">
                    <div class="text-dark fs-12 text-title text-nowrap">{{ translate('Unit Price') }}</div>
                    <div class="d-flex align-items-center gap-1">
                        <h6 class="m-0 font-semibold text-dark fs-14">{{ \App\CentralLogics\Helpers::set_symbol($product->price) }}</h6>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center gap-2 py-5 px-3 bg-light border rounded">
            <p class="text-title2 m-0">{{ translate('No Items found') }}</p>
        </div>
    @endforelse
@endif
