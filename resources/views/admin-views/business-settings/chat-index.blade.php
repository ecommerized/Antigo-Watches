@extends('layouts.admin.app')

@section('title', translate('Social Media chat'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-4">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/third-party.png')}}" alt="{{ translate('img') }}">
                {{translate('3rd_Party')}}
            </h2>
        </div>

        <div class="inline-page-menu my-4">
            @include('admin-views.business-settings.partial.third-party-nav')
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.update-social-media-chat')}}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                @php($whatsapp=\App\Models\BusinessSetting::where('key','whatsapp')->first()?->value)
                                @php($whatsapp_data=json_decode($whatsapp,true))
                                <div class="col-md-6 col-12">
                                    <div class="card mb-3">
                                        <div class="card-body form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="whatsapp_status">
                                            <span class="toggle-switch-content ml-0">
                                                <span class="d-block font-weight-bold">{{translate('whatsapp')}}</span>
                                            </span>
                                                <input type="checkbox" name="whatsapp_status" class="toggle-switch-input"
                                                       value="1" id="whatsapp_status" {{$whatsapp_data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                            </label>
                                            <label class="text-capitalize" class="form-label">{{translate('Number')}}<span class="text-danger"> ({{ translate('without country code') }})</span></label>
                                            <input type="text" name="whatsapp_number"  class="form-control" placeholder="{{ translate('number') }}" value="{{$whatsapp_data['number']}}">
                                        </div>
                                    </div>
                                </div>

                                @php($telegram=\App\Models\BusinessSetting::where('key','telegram')->first()?->value)
                                @php($telegram_data=json_decode($telegram,true))
                                <div class="col-md-6 col-12">
                                    <div class="card mb-3">
                                        <div class="card-body form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="telegram_status">
                                                <span class="toggle-switch-content ml-0">
                                                    <span class="d-block font-weight-bold">{{translate('telegram')}}</span>
                                                </span>
                                                <input type="checkbox" name="telegram_status" class="toggle-switch-input"
                                                       value="1" id="telegram_status" {{$telegram_data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                            </label>
                                            <label class="text-capitalize" class="form-label">{{translate('User Name')}}<span class="text-danger"> ({{ translate('without @') }})</span></label>
                                            <input type="text" name="telegram_user_name"  class="form-control" placeholder="{{ translate('user name') }}" value="{{$telegram_data['user_name']}}">
                                        </div>
                                    </div>

                                </div>

                                @php($messenger=\App\Models\BusinessSetting::where('key','messenger')->first()?->value)
                                @php($messenger_data=json_decode($messenger,true))
                                <div class="col-md-6 col-12">
                                    <div class="card mb-3">
                                        <div class="card-body form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="messenger_status">
                                            <span class="toggle-switch-content ml-0">
                                            <span class="d-block font-weight-bold">{{translate('messenger')}}</span>
                                          </span>
                                                <input type="checkbox" name="messenger_status" class="toggle-switch-input"
                                                       value="1" id="messenger_status" {{$messenger_data['status']==1?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                              </span>
                                            </label>
                                            <label class="text-capitalize" class="form-label">{{translate('User Name')}}</label>
                                            <input type="text" name="messenger_user_name"  class="form-control" placeholder="{{ translate('user name') }}" value="{{$messenger_data['user_name']}}">
                                        </div>
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
        </div>
    </div>
@endsection

