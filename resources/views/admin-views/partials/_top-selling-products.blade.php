<div class="card-header">
    <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
        <img width="20" src="{{asset('public/assets/admin/img/icons/top-selling-product.png')}}" alt="{{ translate('image') }}">
        {{translate('top_selling_products')}}
    </h4>
</div>

<div class="card-body">
    <div class="d-flex flex-column gap-3">
        @foreach($top_sell as $key=>$item)
            @if(isset($item->product))
                <a class="d-flex flex-wrap align-items-center justify-content-between gap-3" href="{{route('admin.product.view',[$item['product_id']])}}">
                    <div class="media align-items-center gap-3">
                        <div class="avatar-lg">
                            <img class="rounded border img-fit"
                            src="{{$item->product->image_fullpath[0]}}"
                            alt="{{$item->product->name}}-image">
                        </div>

                        <div class="media-body">
                            <span class="text-dark">{{substr($item->product['name'],0,20)}} {{strlen($item->product['name'])>20?'...':''}}</span>
                        </div>
                    </div>
                    <label class="px-2 py-1 bg-primary text-white rounded lh-1.3">{{translate("Sold")}} : {{$item['count']}}</label>
                </a>
            @endif
        @endforeach
    </div>
</div>
