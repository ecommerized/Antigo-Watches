@extends('layouts.admin.app')

@section('title', translate('Flash sale'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="16" src="{{asset('public/assets/admin/img/icons/flash-sale.png')}}" alt="{{ translate('flash-sale') }}">
                {{translate('Flash sale Setup')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">

                <div class="mb-4 product-search-result-wrap">
                    <label class="input-label">{{translate('Product')}}</label>
                    <input type="text" name="product_search" id="product-search" class="form-control" placeholder="{{ translate('search product') }}">

                    <div class="product-search-result bg-white shadow-soft rounded mt-1 px-3">
                        @foreach($products as $product)
                            <div class="border-bottom py-3 result">
                                <a class="media gap-3" href="{{ route('admin.flash-sale.add-product-to-session', [$flash_sale_id, $product['id']]) }}">
                                    <img class="selected-product-img rounded border p-1" width="55"
                                         src="{{$product['image_fullpath'][0]}}" alt="{{ translate('image') }}">
                                    <div class="media-body">
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <div class="d-flex flex-wrap column-gap-3 fs-12">
                                            <div>{{ translate('price') }} : {{ Helpers::set_symbol($product->price) }}</div>
                                            <div>{{ translate('total_stock') }} : {{ $product->total_stock }}</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="selected-products">
                    @php
                        $selected_products = session()->get('selected_products', []);
                    @endphp
                    @foreach($selected_products as $selected_product)
                        @if($selected_product['flash_sale_id'] == $flash_sale_id)
                            <div class="bg-light rounded selected-product-item p-3 position-relative">
                                <a class="remove-item-btn" href="{{ route('admin.flash-sale.delete-product-from-session', [$flash_sale_id, $selected_product['product_id']]) }}">
                                    <i class="tio-clear"></i>
                                </a>
                                <div class="media gap-3">
                                    <img class="selected-product-img rounded border p-1" width="55"
                                         src="{{$selected_product['image']}}"
                                         alt="{{ translate('image') }}">
                                    <div class="media-body">
                                        <h6 class="mb-1">{{ $selected_product['name'] }}</h6>
                                        <div class="d-flex flex-wrap column-gap-3 fs-12">
                                            <div>{{ translate('price') }} : {{ Helpers::set_symbol($selected_product['price']) }}</div>
                                            <div>{{ translate('Current Stock') }} : {{ $selected_product['total_stock'] }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                </div>

                <div class="d-flex justify-content-end gap-3 mt-3">
                    <a type="reset" class="btn btn-secondary px-5" href="{{ route('admin.flash-sale.delete-all-products-from-session', [$flash_sale_id]) }}">{{translate('reset')}}</a>
                    <a type="submit" class="btn btn-primary px-5" id="flash-sale-product-store">{{translate('submit')}}</a>
                    <form action="{{route('admin.flash-sale.add_flash_sale_product', [$flash_sale_id])}}"
                        method="post" id="product_store" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Flash Sale Product List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$flashSaleProducts->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{translate('Search by name')}}" aria-label="Search"
                                       value="{{$search}}" autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary px-2 py-1 min-h-35">
                                        <i class="tio-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('Name')}}</th>
                            <th>{{translate('Price')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($flashSaleProducts as $key => $product)
                        <tr>
                            <td> {{$flashSaleProducts->firstitem()+$key}}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ Helpers::set_symbol($product->price) }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="flash-product-delete-{{$product->id}}"
                                       data-message="{{translate('Want to delete this product ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.flash-sale.product.delete',[$flash_sale_id, $product->id])}}"
                                      method="post" id="flash-product-delete-{{$product->id}}">
                                    @csrf
                                    @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $flashSaleProducts->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
             @if(count($flashSaleProducts)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/flash-sale.js') }}"></script>
@endpush
