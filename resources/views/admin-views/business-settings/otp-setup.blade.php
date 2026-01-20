@extends('layouts.admin.app')

@section('title', translate('OTP and Login Setup'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/business-setup.png')}}" alt="{{ translate('business_setup_image') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.business-settings.update-otp')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            @php($maximum_otp_hit=\App\Models\BusinessSetting::where('key','maximum_otp_hit')->first()?->value)
                            <div class="form-group">
                                <label class="input-label" for="maximum_otp_hit">{{translate('maximum_OTP_submit_attempt')}}
                                    <i class="tio-info-outlined" data-toggle="tooltip" data-placement="top"
                                       title="{{ translate('The maximum OTP hit is a measure of how many times a specific one-time password has been generated and used within a time.') }}">
                                    </i>
                                </label>
                                <input type="number" value="{{$maximum_otp_hit}}" name="maximum_otp_hit"
                                       class="form-control" placeholder="" min="1" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            @php($otp_resend_time=\App\Models\BusinessSetting::where('key','otp_resend_time')->first()?->value)
                            <div class="form-group">
                                <label class="input-label" for="otp_resend_time">{{translate('OTP_resend_time')}}
                                    <span class="text-danger">( {{ translate('in second') }} )</span>
                                    <i class="tio-info-outlined" data-toggle="tooltip" data-placement="top"
                                       title="{{ translate('If the user fails to get the OTP within a certain time, user can request a resend.') }}">
                                    </i>
                                </label>
                                <input type="number" value="{{$otp_resend_time}}" name="otp_resend_time"
                                       class="form-control" placeholder="" min="1" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            @php($temporary_block_time=\App\Models\BusinessSetting::where('key','temporary_block_time')->first()?->value)
                            <div class="form-group">
                                <label class="input-label" for="temporary_block_time">{{translate('temporary_OTP_block_time')}}
                                    <span class="text-danger">( {{ translate('in second') }} )</span>
                                    <i class="tio-info-outlined" data-toggle="tooltip" data-placement="top"
                                       title="{{ translate('Temporary OTP block time refers to a security measure implemented by systems to restrict access to OTP service for a specified period of time for wrong OTP submission.') }}">
                                    </i>
                                </label>
                                <input type="number" value="{{$temporary_block_time}}" name="temporary_block_time"
                                       class="form-control" placeholder="" min="1" required>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-4">
                            @php($maximum_login_hit=\App\Models\BusinessSetting::where('key','maximum_login_hit')->first()?->value)
                            <div class="form-group">
                                <label class="input-label" for="maximum_otp_hit">{{translate('maximum Login Attempt')}}
                                    <i class="tio-info-outlined"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       title="{{ translate('The maximum login hit is a measure of how many times a user can submit password within a time.') }}">
                                    </i>
                                </label>
                                <input type="number" min="1" value="{{$maximum_login_hit}}"
                                       name="maximum_login_hit" class="form-control" placeholder="" required>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-4">
                            @php($temporary_login_block_time=\App\Models\BusinessSetting::where('key','temporary_login_block_time')->first()?->value)
                            <div class="form-group">
                                <label class="input-label" for="temporary_block_time">{{translate('temporary_login_block_time')}}
                                    <span class="text-danger">( {{ translate('in second') }} )</span>
                                    <i class="tio-info-outlined"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       title="{{ translate('Temporary login block time refers to a security measure implemented by systems to restrict access for a specified period of time for wrong Password submission.') }}">
                                    </i>
                                </label>
                                <input type="number" min="1" value="{{$temporary_login_block_time}}"
                                       name="temporary_login_block_time" class="form-control" placeholder="" required>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                class="btn btn-primary demo-form-submit">{{translate('update')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
