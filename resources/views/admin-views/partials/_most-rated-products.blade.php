<div class="card-header">
    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
        <img width="20" src="{{asset('public/assets/admin/img/icons/top-rated.png')}}" alt="{{ translate('image') }}">
        {{translate('most_rated_products')}}
    </h4>
</div>

<div class="card-body d-flex flex-column gap-3">
    @foreach($most_rated_products as $key=>$item)
        @php($product=\App\Models\Product::find($item['product_id']))
        @if(isset($product))
            <a href="{{route('admin.product.view',[$item['product_id']])}}" class="text-dark d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="media gap-3 align-items-center w-50">
                    <div class="avatar-lg border rounded">
                        <img class="img-fit rounded"
                            src="{{ $product['image_fullpath'][0] }}"
                            alt="{{$product->name}}-image">
                    </div>
                    <span class="media-body">
                        {{isset($product)?substr($product->name,0,30) . (strlen($product->name)>20?'...':''):'not exists'}}
                    </span>
                </div>
                <div class="fs-18 d-flex align-items-center gap-2">
                    {{round($item['ratings_average'],2)}}
                    <i class="tio-star gold"></i>
                </div>
                <div class="fs-18">
                    {{$item['total']}} <i class="tio-user"></i>
                </div>
            </a>
        @endif
    @endforeach
</div>
