@extends('layouts.admin.app')

@section('title', translate('Product Preview'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                    <img width="20" src="{{asset('public/assets/admin/img/icons/product.png')}}" alt="{{ translate('product') }}">
                    {{$product['name']}}
                </h2>
                <a href="{{url()->previous()}}" class="btn btn-primary">
                    <i class="tio-back-ui"></i> {{translate('back')}}
                </a>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-5">
                        <div class="media gap-4 align-items-center">
                            <div class="avatar avatar-xxl avatar-4by3 border rounded">
                                <img class="img-fit rounded"
                                src="{{$product['image_fullpath'][0]}}"
                                alt="{{ translate('product') }}">
                            </div>
                            <div class="media-body">
                                <h2 class="display-2 text-primary mb-0">
                                    {{count($product->rating)>0?number_format($product->rating[0]->average, 2, '.', ' '):0}}
                                </h2>
                                <p> {{translate('of')}} {{$product->reviews->count()}} {{translate('reviews')}}
                                    <span class="badge badge-soft-dark badge-pill ml-1"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <ul class="list-unstyled list-unstyled-py-3 mb-0">
                        @php($total=$product->reviews->count())
                            <li class="d-flex align-items-center font-size-sm">
                                @php($five=Helpers::rating_count($product['id'],5))
                                <span
                                    class="mr-3">{{translate('5 star')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($five/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($five/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$five}}</span>
                            </li>

                            <li class="d-flex align-items-center font-size-sm">
                                @php($four=Helpers::rating_count($product['id'],4))
                                <span class="mr-3">{{translate('4 star')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($four/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($four/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$four}}</span>
                            </li>

                            <li class="d-flex align-items-center font-size-sm">
                                @php($three=Helpers::rating_count($product['id'],3))
                                <span class="mr-3">{{translate('3 star')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($three/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($three/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$three}}</span>
                            </li>

                            <li class="d-flex align-items-center font-size-sm">
                                @php($two=Helpers::rating_count($product['id'],2))
                                <span class="mr-3">{{translate('2 star')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($two/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($two/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$two}}</span>
                            </li>

                            <li class="d-flex align-items-center font-size-sm">
                                @php($one=Helpers::rating_count($product['id'],1))
                                <span class="mr-3">{{translate('1 star')}}</span>
                                <div class="progress flex-grow-1">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{$total==0?0:($one/$total)*100}}%;"
                                         aria-valuenow="{{$total==0?0:($one/$total)*100}}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ml-3">{{$one}}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="col-12">
                        <hr>
                    </div>

                    <div class="col-md-6 col-lg-4 text-dark">
                        <h4 class="mb-3 text-capitalize">{{$product['name']}}</h4>
                        <div>
                            {{translate('total_stock')}}: {{$product['total_stock']}}
                        </div>
                        <div>
                            {{translate('price')}} :
                            <span>{{ Helpers::set_symbol($product['price']) }} / {{translate(''.$product['unit'])}}</span>
                        </div>
                        <div>{{translate('tax')}} :
                            <span>{{ $product['tax_type'] == 'amount' ? Helpers::set_symbol($product['tax']) : $product['tax']. '%' }}</span>
                        </div>
                        <div>{{translate('discount')}} :
                            <span>{{ $product['discount_type'] == 'amount' ? Helpers::set_symbol($product['discount']) : $product['discount']. '%'}}</span>
                        </div>
                        @if(count(json_decode($product['variations'],true)) > 1)
                            <h4 class="mt-4 mb-3 text-capitalize"> {{translate('variations')}} </h4>
                        @endif
                        <div class="d-flex flex-column gap-1 fs-12">
                            @foreach(json_decode($product['variations'],true) as $variation)
                                <div class="text-capitalize">
                                {{$variation['type']}} : {{ Helpers::set_symbol($variation['price']) }} ( Stock : {{$variation['stock']??0}} )
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-8">
                        <div class="border-md-left pl-md-4 h-100">
                            <h4>{{translate('short')}} {{translate('description')}} : </h4>
                            <p>{!! $product['description'] !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive datatable-custom">
                <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{translate('reviewer')}}</th>
                            <th>{{translate('review')}}</th>
                            <th>{{translate('date')}}</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($reviews as $review)
                        <tr>
                            <td>
                                @if(isset($review->customer))
                                    <a class="media gap-3 align-items-center"
                                       href="{{route('admin.customer.view',[$review['user_id']])}}">
                                        <div class="avatar avatar-circle">
                                            <img class="img-fit rounded-circle"
                                                 src="{{$review->customer->image_fullpath}}"
                                                 alt="{{ translate('image') }}">
                                        </div>
                                        <div class="media-body">
                                            <span class="d-block h5 text-hover-primary mb-0">{{$review->customer['f_name']." ".$review->customer['l_name']}}
                                                <i class="tio-verified text-primary" data-toggle="tooltip" data-placement="top" title="Verified Customer"></i>
                                            </span>
                                            <span class="d-block font-size-sm text-body">{{$review->customer->email}}</span>
                                        </div>
                                    </a>
                                @else
                                    <span class="text-muted">
                                        {{translate('Customer unavailable')}}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-wrap mx-w300 mn-w200">
                                    <div class="d-flex">
                                        <label class="badge badge-soft-info d-flex gap-1 align-items-center">
                                            {{$review->rating}} <i class="tio-star"></i>
                                        </label>
                                    </div>

                                    <div class="pragraph-description" data-limit="120">
                                        <p class="m-0">
                                            {{$review['comment']}}
                                        </p>
                                        <a href="#0" class="text-primary d-inline-block cursor-pointer font-semibold text-underline see-more">see_more</a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{date('d M Y H:i:s',strtotime($review['created_at']))}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="table-responsive mt-4 px-3">
                <div class="d-flex justify-content-end">
                    {!! $reviews->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script_2')
<script>
    //Text hide / Showing
    $(document).ready(function () {
        $('.pragraph-description').each(function () {
            var $container = $(this);
            var limit = parseInt($container.data('limit')) || 350;
            var $desc = $container.find('p');
            var fullText = $desc.text().trim();

            if (fullText.length > limit) {
                var shortText = fullText.substring(0, limit) + '...';
                $desc.data('full-text', fullText).text(shortText);
                $container.find('.see-more').show().text('See More');
            } else {
                $container.find('.see-more').remove();
            }
        });

        $(document).on('click', '.see-more', function (e) {
            e.preventDefault();

            var $link = $(this);
            var $container = $link.closest('.pragraph-description');
            var $desc = $container.find('p');
            var fullText = $desc.data('full-text');
            var limit = parseInt($container.data('limit')) || 350;

            if ($link.text().trim().toLowerCase() === 'see more') {
                $desc.text(fullText);
                $link.text('See Less');
            } else {
                $desc.text(fullText.substring(0, limit) + '...');
                $link.text('See More');
            }
        });
    });
</script>
@endpush
