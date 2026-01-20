@extends('layouts.admin.app')

@section('title', translate('Add new coupon'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/coupon.png')}}" alt="{{ translate('coupon') }}">
                {{translate('add_new_coupon')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.coupon.store')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('title')}}</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="{{ translate('New coupon') }}" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('coupon')}} {{translate('type')}}</label>
                                <select name="coupon_type" class="form-control coupon-type">
                                    <option value="default">{{translate('default')}}</option>
                                    <option value="first_order">{{translate('first order')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6" id="limit-for-user">
                            <div class="form-group">
                                <label class="input-label">{{translate('limit')}} {{translate('for')}} {{translate('same')}} {{translate('user')}}</label>
                                <input type="number" name="limit" id="user-limit" value="{{ old('limit') }}" class="form-control" max="100000" placeholder="{{ translate('EX: 10') }}" required min="1">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between">
                                    <label class="input-label">{{translate('code')}}</label>
                                    <a href="javascript:void(0)" class="float-right c1 fz-12 generate-code">{{translate('generate_code')}}</a>
                                </div>
                                <input type="text" name="code" class="form-control" maxlength="15" id="code" value="{{ old('code') }}"
                                       placeholder="{{\Illuminate\Support\Str::random(8)}}" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('start')}} {{translate('date')}}</label>
                                <input type="text" name="start_date" id="start_date" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ translate('Select date') }}" data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('expire')}} {{translate('date')}}</label>
                                <input type="text" name="expire_date" id="expire_date" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ translate('Select date') }}" data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('min')}} {{translate('purchase')}}</label>
                                <input type="number" step="0.01" name="min_purchase" value="{{ old('min_purchase')}}" min="0" max="100000" class="form-control"
                                       placeholder="{{ translate('100') }}">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('discount')}}{{translate('type')}}</label>
                                <select name="discount_type" id="discount_type" class="form-control">
                                    <option value="percent">{{translate('percent')}}</option>
                                    <option value="amount">{{translate('amount')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('discount')}}</label>
                                <input type="number" step="0.01" min="1" max="100000" name="discount" value="{{old('discount') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6" id="max_discount_div">
                            <div class="form-group">
                                <label class="input-label">{{translate('max')}} {{translate('discount')}}</label>
                                <input type="number" step="0.01" min="0" value="{{ old('max_discount') }}" max="100000" name="max_discount" class="form-control">
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">{{translate('Reset')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="p-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center gy-2">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <h6 class="m-0">{{translate('Coupon List ')}}</h6>
                        <span class="badge badge-soft-dark rounded-50 fz-10">{{$coupons->total()}}</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET">
                            @foreach (request()->except('search','page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <div class="input-group min-h-35">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control py-1 h-35 fs-12"
                                       placeholder="{{translate('Search by title')}}" aria-label="Search"
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
                        <th>{{translate('title')}}</th>
                        <th>{{translate('code')}}</th>
                        <th>{{translate('min')}} {{translate('purchase')}}</th>
                        <th>{{translate('max')}} {{translate('discount')}}</th>
                        <th>{{translate('discount')}}</th>
                        <th>{{translate('discount')}} {{translate('type')}}</th>
                        <th>{{translate('start')}} {{translate('date')}}</th>
                        <th>{{translate('expire')}} {{translate('date')}}</th>
                        <th>{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($coupons as $key=>$coupon)
                        <tr>
                            <td>{{$coupons->firstItem()+$key}}</td>
                            <td><p class="max-w-200 min-w-150 line--limit-2" data-toggle="tooltip" data-placement="top" title="{{$coupon['title']}}">{{$coupon['title']}}</p></td>
                            <td>{{$coupon['code']}}</td>
                            <td>{{ Helpers::set_symbol($coupon['min_purchase']) }}</td>
                            <td>{{ $coupon['discount_type'] == 'percent' ? Helpers::set_symbol($coupon['max_discount']) : '-' }}</td>
                            <td>{{$coupon['discount']}}</td>
                            <td>{{translate($coupon['discount_type'])}}</td>
                            <td>{{date('d-m-Y', strtotime($coupon['start_date']))}}</td>
                            <td>{{date('d-m-Y', strtotime($coupon['expire_date']))}}</td>
                            <td>
                                @if($coupon['status']==1)
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status" checked id="{{$coupon['id']}}"
                                               data-route="{{route('admin.coupon.status',[$coupon['id'],0])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @else
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input change-status"  id="{{$coupon['id']}}"
                                               data-route="{{route('admin.coupon.status',[$coupon['id'],1])}}">
                                        <span class="switcher_control"></span>
                                    </label>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <a class="btn btn-outline-warning square-btn coupon_details" id="coupon_details"
                                       href="#" data-toggle="modal" data-id="{{ $coupon['id'] }}" data-toggle="modal" data-target="#exampleModalCenter">
                                        <i class="tio-invisible"></i>
                                    </a>

                                    <a class="btn btn-outline-info square-btn"
                                        href="{{route('admin.coupon.update',[$coupon['id']])}}"><i class="tio tio-edit"></i>
                                    </a>
                                    <a class="btn btn-outline-danger square-btn form-alert" href="javascript:"
                                       data-id="coupon-{{$coupon['id']}}"
                                       data-message="{{translate('Want to delete this attribute ?')}}">
                                        <i class="tio tio-delete"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.coupon.delete',[$coupon['id']])}}"
                                        method="post" id="coupon-{{$coupon['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="">
                {!! $coupons->links('layouts/partials/_pagination', ['perPage' => $perPage]) !!}
            </div>
            @if(count($coupons)==0)
                <div class="text-center p-4">
                    <img class="mb-3 width-7rem" src="{{asset('public/assets/admin//svg/illustrations/sorry.svg')}}" alt="{{ translate('Image') }}">
                    <p class="mb-0">{{ translate('No data to show') }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="quick-view" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered coupon-details" role="document">
            <div class="modal-content" id="quick-view-modal">
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/coupon.js') }}"></script>
    <script>
        "use strict";

        $('.coupon_details').on('click', function (){
            let id = $(this).data('id')
            $.ajax({
                type: 'GET',
                url: '{{route('admin.coupon.details')}}',
                data: {
                    id: id
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
                    $('#quick-view').modal('show');
                    $('#quick-view-modal').empty().html(data.view);
                }
            });
        });

    </script>
@endpush
