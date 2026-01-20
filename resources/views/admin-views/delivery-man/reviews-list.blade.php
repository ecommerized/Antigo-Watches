@extends('layouts.admin.app')

@section('title', translate('Review List'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/rating.png')}}" alt="{{ translate('rating') }}">
                {{translate('review_List')}}
            </h2>
        </div>

        <div class="card">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Delivery Men Review List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$reviews->total()}}</span>
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
                <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{translate('SL')}}</th>
                            <th>{{translate('deliveryman')}}</th>
                            <th>{{translate('customer')}}</th>
                            <th>{{translate('review')}}</th>
                            <th>{{translate('rating')}}</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($reviews as $key=>$review)
                            <tr>
                                <td>{{$reviews->firstitem()+$key}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                            @if($review->delivery_man)
                                                <a class="text-dark" href="{{route('admin.delivery-man.preview',[$review['delivery_man_id']])}}">
                                                    {{$review->delivery_man->f_name.' '.$review->delivery_man->l_name}}
                                                </a>
                                            @else
                                                <span class="text-muted">
                                                    {{translate('DeliveryMan Unavailable')}}
                                                </span>
                                            @endif
                                    </span>
                                </td>
                                <td>
                                    @if(isset($review->customer))
                                        <a class="text-dark" href="{{route('admin.customer.view',[$review->user_id])}}">
                                            {{$review->customer->f_name." ".$review->customer->l_name}}
                                        </a>
                                    @else
                                        <span class="text-muted">
                                            {{translate('Customer unavailable')}}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="mx-w300 mn-w200 text-wrap pragraph-description" data-limit="120">
                                        <p class="mb-0">
                                            {{$review->comment}}
                                        </p>
                                        <a href="#0" class="text-primary d-inline-block cursor-pointer font-semibold text-underline see-more">see_more</a>
                                    </div>
                                </td>
                                <td>
                                    <label class="badge badge-soft-info">
                                        {{$review->rating}} <i class="tio-star ml-1"></i>
                                    </label>
                                </td>
                            </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $reviews->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($reviews)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script_2')
<script>
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
