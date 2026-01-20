@extends('layouts.admin.app')

@section('title', translate('reCaptcha Setup'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/third-party.png')}}" alt="{{ translate('3rd_Party_image') }}">
                {{translate('3rd_Party')}}
            </h2>
        </div>

        <div class="inline-page-menu my-4">
            @include('admin-views.business-settings.partial.third-party-nav')
        </div>


        <div class="col-md-">
            <div class="card">
                @php($config=Helpers::get_business_settings('recaptcha'))
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.recaptcha_update',['recaptcha']):'javascript:'}}" method="post">
                    @csrf
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="text-uppercase mb-0">{{translate('Google Recapcha Information')}}</h5>
                        <a class="btn-sm btn btn-outline-primary p-2 cursor-pointer" data-toggle="modal" data-target="#instructionsModal">
                            <i class="tio-info-outined"></i>
                            {{translate('Credentials_Setup')}}
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="badge-soft-secondary rounded mb-5 p-3">
                            <h5 class="m-0">{{ translate('V3 Version is available now. Must setup for ReCAPTCHA V3') }}</h5>
                            <p class="m-0">{{ translate('You must setup for V3 version and active the status. Otherwise the default reCAPTCHA will be displayed automatically') }}</p>
                        </div>
                        <div class="mt-4">
                            <div class="d-flex align-items-center gap-4 gap-xl-5 mb-2">
                                <div class="custom-radio">
                                    <input type="radio" name="status" value="1" id="status-active" {{isset($config) && $config['status'] == 1 ?'checked':''}}>
                                    <label for="status-active"> {{ translate('Active') }}</label>
                                </div>
                                <div class="custom-radio">
                                    <input type="radio" name="status" value="0" id="status-inactive" {{isset($config) && $config['status'] == 1 ?'':'checked'}}>
                                    <label for="status-inactive"> {{ translate('Inactive') }}</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-capitalize">{{translate('Site Key')}}</label>
                                        <input type="text" class="form-control" name="site_key" value="{{env('APP_MODE')!='demo'?$config['site_key']??"":''}}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="text-capitalize">{{translate('Secret Key')}}</label>
                                        <input type="text" class="form-control" name="secret_key" value="{{env('APP_MODE')!='demo'?$config['secret_key']??"":''}}">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        class="btn btn-primary demo-form-submit">{{translate('save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <div class="modal fade" id="instructionsModal" tabindex="-1" aria-labelledby="instructionsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-end">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center my-5">
                        <img src="{{ asset('public/assets/admin/svg/components/instruction.svg') }}">
                    </div>

                    <h5 class="modal-title my-3" id="instructionsModalLabel">{{translate('Instructions')}}</h5>

                    <ol class="d-flex flex-column __gap-5px __instructions">
                        <li>{{translate('To get site key and secret key go to the Credentials page')}}
                            ({{translate('Click')}} <a
                                href="https://www.google.com/recaptcha/admin/create"
                                target="_blank">{{translate('here')}}</a>)
                        </li>
                        <li>{{translate('Add a ')}}
                            <b>{{translate('label')}}</b> {{translate('(Ex: Test Label)')}}
                        </li>
                        <li>
                            {{translate('Select reCAPTCHA v3 as ')}}
                            <b>{{translate('reCAPTCHA Type')}}</b>
                        </li>
                        <li>
                            {{translate('Add')}}
                            <b>{{translate('domain')}}</b>
                            {{translate('(For ex: demo.6amtech.com)')}}
                        </li>
                        <li>
                            {{translate('Press')}}
                            <b>{{translate('Submit')}}</b>
                        </li>
                        <li>{{translate('Copy')}} <b>Site
                                Key</b> {{translate('and')}} <b>Secret
                                Key</b>, {{translate('paste in the input filed below and')}}
                            <b>Save</b>.
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

@endsection
