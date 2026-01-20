@extends('layouts.admin.app')

@section('title', translate('Update Coupon'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/coupon.png')}}" alt="{{ translate('coupon') }}">
                {{translate('update_coupon')}}
            </h2>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.coupon.update',[$coupon['id']])}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('title')}}</label>
                                <input type="text" name="title" value="{{$coupon['title']}}" class="form-control"
                                       placeholder="{{ translate('New coupon') }}" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('coupon')}} {{translate('type')}}</label>
                                <select name="coupon_type" class="form-control coupon-type">
                                    <option value="default" {{$coupon['coupon_type']=='default'?'selected':''}}>
                                        {{translate('default')}}
                                    </option>
                                    <option value="first_order" {{$coupon['coupon_type']=='first_order'?'selected':''}}>
                                        {{translate('first')}} {{translate('order')}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6" id="limit-for-user">
                            <div class="form-group">
                                <label class="input-label">{{translate('limit')}} {{translate('for')}} {{translate('same')}} {{translate('user')}}</label>
                                <input type="number" name="limit" value="{{$coupon['limit']}}"  max="100000" class="form-control"
                                       placeholder="{{ translate('EX: 10') }}">
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('code')}}</label>
                                <input type="text" name="code" class="form-control" value="{{$coupon['code']}}" maxlength="15"
                                       placeholder="{{\Illuminate\Support\Str::random(8)}}" required>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="">{{translate('start')}} {{translate('date')}}</label>
                                <input type="text" name="start_date" id="start_date" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ translate('Select date') }}" value="{{date('Y/m/d',strtotime($coupon['start_date']))}}"
                                       data-hs-flatpickr-options='{ "dateFormat": "Y/m/d", "minDate": "today" }'>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="">{{translate('expire')}} {{translate('date')}}</label>
                                <input type="text" name="expire_date" id="expire_date" class="js-flatpickr form-control flatpickr-custom" placeholder="{{ translate('Select date') }}" value="{{date('Y/m/d',strtotime($coupon['expire_date']))}}"
                                       data-hs-flatpickr-options='{
                                     "dateFormat": "Y/m/d",
                                     "minDate": "today"
                                   }'>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('min')}} {{translate('purchase')}}</label>
                                <input type="number" name="min_purchase" step="0.01" value="{{$coupon['min_purchase']}}"
                                       min="0" max="100000" class="form-control"
                                       placeholder="{{ translate('100') }}">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('discount')}} {{translate('type')}}</label>
                                <select name="discount_type" id="discount_type" class="form-control">
                                    <option value="percent" {{$coupon['discount_type']=='percent'?'selected':''}}>{{translate('percent')}}</option>
                                    <option value="amount" {{$coupon['discount_type']=='amount'?'selected':''}}>{{translate('amount')}}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label">{{translate('discount')}}</label>
                                <input type="number" min="1" max="100000" step="0.01" value="{{$coupon['discount']}}"
                                       name="discount" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6" id="max_discount_div">
                            <div class="form-group">
                                <label class="input-label">{{translate('max')}} {{translate('discount')}}</label>
                                <input type="number" min="0" max="100000" step="0.01"
                                       value="{{$coupon['max_discount']}}" name="max_discount" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="d-flex justify-content-end gap-3">
                        <button type="reset" class="btn btn-secondary">{{translate('Reset')}}</button>
                        <button type="submit" class="btn btn-primary">{{translate('update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/coupon.js') }}"></script>
@endpush
