@extends('layouts.admin.app')

@section('title', translate('Firebase Settings'))

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

        <div class="card">
            @php($data=Helpers::get_business_settings('firebase_message_config'))
            <div class="card-body">
                <form action="{{env('APP_MODE')!='demo'?route('admin.business-settings.firebase_message_config'):'javascript:'}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @if(isset($data))
                    <div class="row">
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('API Key')}}</label>
                                <input type="text" placeholder="" class="form-control" name="apiKey"
                                       value="{{env('APP_MODE')!='demo'?$data['apiKey']:''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('Auth Domain')}}</label>
                                <input type="text" class="form-control" name="authDomain" value="{{env('APP_MODE')!='demo'?$data['authDomain']:''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('Project ID')}}</label>
                                <input type="text" class="form-control" name="projectId" value="{{env('APP_MODE')!='demo'?$data['projectId']:''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('Storage Bucket')}}</label>
                                <input type="text" class="form-control" name="storageBucket" value="{{env('APP_MODE')!='demo'?$data['storageBucket']:''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('Messaging Sender ID')}}</label>
                                <input type="text" placeholder="" class="form-control" name="messagingSenderId"
                                       value="{{env('APP_MODE')!='demo'?$data['messagingSenderId']:''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-xl-4 col-sm-6">
                            <div class="form-group">
                                <label>{{translate('App ID')}}</label>
                                <input type="text" placeholder="" class="form-control" name="appId"
                                       value="{{env('APP_MODE')!='demo'?$data['appId']:''}}" required autocomplete="off">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                        class="btn btn-primary demo-form-submit">{{translate('save')}}
                                </button>
                            </div>
                            @else
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

