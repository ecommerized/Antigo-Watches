@extends('layouts.admin.app')

@section('title', translate('Delivery Man Preview'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-6">
                    <h1 class="page-header-title">{{$deliveryMan['f_name'].' '.$deliveryMan['f_name']}}</h1>
                </div>
                <div class="col-6">
                    <a href="{{url()->previous()}}" class="btn btn-primary float-right">
                        <i class="tio-back-ui"></i> {{translate('back')}}
                    </a>
                </div>
            </div>
            <ul class="nav nav-tabs page-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:">
                        {{translate('deliveryman')}} {{translate('reviews')}}
                    </a>
                </li>
            </ul>
        </div>

        <div class="card mb-3 mb-lg-5">
            <div class="card-body">
                <div class="row align-items-md-center gx-md-5">
                    <div class="col-md-auto mb-3 mb-md-0">
                        <div class="d-flex align-items-center">
                            <img class="avatar avatar-xxl avatar-4by3 mr-4"
                                 src="{{$deliveryMan['image-fullpath']}}"
                                 alt="{{ translate('delivery-man') }}">
                            <div class="d-block">
                                <h4 class="display-2 text-dark mb-0">{{count($deliveryMan->rating)>0?number_format($deliveryMan->rating[0]->average, 2, '.', ' '):0}}</h4>
                                <p> {{ translate('of') }} {{$deliveryMan->reviews->count()}} {{translate('reviews')}}
                                    <span class="badge badge-soft-dark badge-pill ml-1"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md">
                        <ul class="list-unstyled list-unstyled-py-2 mb-0">

                        @php($total=$deliveryMan->reviews->count())
                            <li class="d-flex align-items-center font-size-sm">
                                @php($five=Helpers::dm_rating_count($deliveryMan['id'],5))
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
                                @php($four=Helpers::dm_rating_count($deliveryMan['id'],4))
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
                                @php($three=Helpers::dm_rating_count($deliveryMan['id'],3))
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
                                @php($two=Helpers::dm_rating_count($deliveryMan['id'],2))
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
                                @php($one=Helpers::dm_rating_count($deliveryMan['id'],1))
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
                        <th>{{translate('attachment')}}</th>
                        <th>{{translate('date')}}</th>
                    </tr>
                    </thead>

                    <tbody>

                    @foreach($reviews as $review)
                        <tr>
                            <td>
                                @if(isset($review->customer))
                                    <a class="d-flex align-items-center"
                                       href="{{route('admin.customer.view',[$review['user_id']])}}">
                                        <div class="avatar avatar-circle">
                                            <img class="avatar-img" width="75" height="75"
                                                 src="{{$review->customer->image_fullpath}}"
                                                 alt="Image Description">
                                        </div>
                                        <div class="ml-3">
                                        <span class="d-block h5 text-hover-primary mb-0">{{$review->customer['f_name']." ".$review->customer['l_name']}} <i
                                                class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                                                title="Verified Customer"></i></span>
                                            <span class="d-block font-size-sm text-body">{{$review->customer->email}}</span>
                                        </div>
                                    </a>
                                @else
                                    <span class="badge-pill badge-soft-dark text-muted text-sm small">
                                        {{translate('Customer unavailable')}}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="text-wrap width-18rem">
                                    <div class="d-flex mb-2">
                                        <label class="badge badge-soft-info">
                                            {{$review->rating}} <i class="tio-star"></i>
                                        </label>
                                    </div>

                                    <p>
                                        {{$review['comment']}}
                                    </p>
                                </div>
                            </td>
                            <td>
                                @foreach($review['attachment_fullpath'] as $attachment)
                                    <img width="100"
                                         src="{{$attachment}}" alt="{{ translate('review') }}">
                                @endforeach
                            </td>
                            <td>
                                {{date('d M Y H:i:s',strtotime($review['created_at']))}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-12">
                        {!! $reviews->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
