@extends('layouts.admin.app')

@section('title', translate('business_setup'))

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="text-capitalize mb-0 d-flex align-items-center gap-2">
                <img width="20" src="{{asset('public/assets/admin/img/icons/business-setup.png')}}" alt="{{ translate('business-setup') }}">
                {{translate('business_Setup')}}
            </h2>
        </div>

        <div class="inline-page-menu mb-4">
            @include('admin-views.business-settings.partial.business-setup-nav')
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="tio-notifications-alert mr-1"></i>
                    {{translate('Maintenance_Mode')}}
                </h5>
            </div>
            <?php
                $config=\App\CentralLogics\Helpers::get_business_settings('maintenance_mode');
                $selectedMaintenanceSystem = \App\CentralLogics\Helpers::get_business_settings('maintenance_system_setup') ?? [];
                $selectedMaintenanceDuration = \App\CentralLogics\Helpers::get_business_settings('maintenance_duration_setup');
                $startDate = new DateTime($selectedMaintenanceDuration['start_date']);
                $endDate = new DateTime($selectedMaintenanceDuration['end_date']);
            ?>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        @if($config)
                            <div class="mb-3">
                                <p class="mb-0">
                                    @if($selectedMaintenanceDuration['maintenance_duration'] == 'until_change')
                                        {{ translate('Your maintenance mode is activated until I change') }}
                                    @else
                                        {{ translate('Your maintenance mode is activated from') }}<strong class="pl-1">{{ $startDate->format('m/d/Y, h:i A') }}</strong> {{ translate('to') }} <strong>{{ $endDate->format('m/d/Y, h:i A') }}</strong>.
                                    @endif
                                    <a class="btn btn-outline-primary btn-sm py-1 px-2 edit square-btn maintenance-mode-show d-inline-block" href="#"><i class="tio-edit"></i></a>
                                </p>
                            </div>
                        @else
                            <p>*{{ translate('By turning on maintenance mode Control your all system & function') }}</p>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                            <h5 class="mb-0">{{translate('Maintenance Mode')}}</h5>
                            <label class="switcher">
                                <input type="checkbox" class="switcher_input @if(!$config) maintenance-mode-show @else maintenance-mode-off @endif"
                                       id="maintenance-mode-input"
                                    {{isset($config) && $config?'checked':''}}>
                                <span class="switcher_control"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        @if($config && count($selectedMaintenanceSystem) > 0)
                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                <h6 class="mb-0">
                                    {{ translate('Selected Systems') }}
                                </h6>
                                <ul class="selected-systems d-flex gap-2 flex-wrap bg-soft-dark px-5 py-1 mb-0 rounded">
                                    @foreach($selectedMaintenanceSystem as $system)
                                        <li class="mr-5">{{ ucwords(str_replace('_', ' ', $system)) }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h4 class="d-flex align-items-center gap-2 mb-0">
                    <i class="tio-settings"></i>
                    {{ translate('General settings form') }}
                </h4>
            </div>
            <div class="card-body">
                <form action="{{route('admin.business-settings.update-setup')}}" method="post"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        @php($name=Helpers::get_business_settings('restaurant_name'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('Shop Name')}}</label>
                                <input type="text" name="restaurant_name" value="{{$name}}" class="form-control"
                                       placeholder="{{ translate('ABC Company') }}" required>
                            </div>
                        </div>
                        @php($currency_code=Helpers::get_business_settings('currency'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('currency')}}</label>
                                <select name="currency" class="form-control js-select2-custom">
                                    @foreach(\App\Models\Currency::orderBy('currency_code')->get() as $currency)
                                        <option value="{{$currency['currency_code']}}" {{$currency_code==$currency['currency_code']?'selected':''}}>
                                            {{$currency['currency_code']}} ( {{$currency['currency_symbol']}} )
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @php($phone=Helpers::get_business_settings('phone'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('phone')}}</label>
                                <input type="text" value="{{$phone}}" name="phone" class="form-control"
                                       placeholder="" required>
                            </div>
                        </div>
                        @php($email=Helpers::get_business_settings('email_address'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('email')}}</label>
                                <input type="email" value="{{$email}}"
                                       name="email" class="form-control" placeholder="" required>
                            </div>
                        </div>
                        @php($address=Helpers::get_business_settings('address'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{translate('address')}}</label>
                                <input type="text" value="{{$address}}"
                                       name="address" class="form-control" placeholder=""
                                       required>
                            </div>
                        </div>

                        @php($pagination_limit=Helpers::get_business_settings('pagination_limit'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{translate('pagination')}} {{translate('settings')}}</label>
                                <input type="text" value="{{$pagination_limit}}"
                                       name="pagination_limit" class="form-control" placeholder=""
                                       required>
                            </div>
                        </div>
                        @php($mov=Helpers::get_business_settings('minimum_order_value'))
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{translate('min')}} {{translate('order')}} {{translate('value')}}
                                    ( {{Helpers::currency_symbol()}} )</label>
                                <input type="number" min="1" value="{{$mov}}"
                                       name="minimum_order_value" class="form-control" placeholder=""
                                       required>
                            </div>
                        </div>
                        @php($country_code=Helpers::get_business_settings('country')??'BD')
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="input-label" for="country">{{translate('country')}}</label>
                                <select id="country" name="country" class="form-control  js-select2-custom">
                                    @foreach(COUNTRY_CODE as $country)
                                        <option value="{{ $country['code'] }}" {{ $country['code'] == $country_code ? 'selected' : '' }}>{{ $country['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            @php($sp=Helpers::get_business_settings('self_pickup'))
                            <div class="form-group">
                                <label>{{translate('self_pickup')}}</label>
                                <small class="text-danger"> *</small>
                                <div class="input-group input-group-md-down-break">
                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="1"
                                                   name="self_pickup"
                                                   id="sp1" {{$sp==1?'checked':''}}>
                                            <label class="custom-control-label"
                                                   for="sp1">{{translate('on')}}</label>
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="0"
                                                   name="self_pickup"
                                                   id="sp2" {{$sp==0?'checked':''}}>
                                            <label class="custom-control-label"
                                                   for="sp2">{{translate('off')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            @php($current_time_zone=Helpers::get_business_settings('time_zone'))
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('time_zone')}}</label>
                                <select name="time_zone" id="time_zone" data-maximum-selection-length="3" class="form-control js-select2-custom">
                                    @foreach(TIME_ZONE as $time_zone)
                                        <option value="{{ $time_zone['key'] }}" {{ $time_zone['key'] == $current_time_zone ? 'selected' : '' }}>{{ $time_zone['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        @php($footer_text=Helpers::get_business_settings('footer_text'))
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('footer')}} {{translate('text')}}</label>
                                <input type="text" value="{{$footer_text}}"
                                       name="footer_text" class="form-control" placeholder="" required>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6">
                            @php($config=Helpers::get_business_settings('currency_symbol_position'))
                            <div class="form-group">
                                <label class="d-flex justify-content-between align-items-center"> {{ translate('Currency Symbol Position') }}</i> </label>

                                <div class="input-group input-group-md-down-break">
                                    <div class="form-control">
                                        <div class="custom-control custom-radio custom-radio-reverse">
                                            <input type="radio" class="custom-control-input currency-symbol-position"
                                                   name="projectViewNewProjectTypeRadio"
                                                   data-route="{{ route('admin.business-settings.currency-position',['left']) }}"
                                                   id="projectViewNewProjectTypeRadio1" {{(isset($config) && $config=='left')?'checked':''}}>
                                            <label class="custom-control-label media align-items-center" for="projectViewNewProjectTypeRadio1">
                                                <i class="tio-agenda-view-outlined text-muted mr-2"></i>
                                                <span class="media-body">{{Helpers::currency_symbol()}} {{ translate('Left') }}</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <div class="custom-control custom-radio custom-radio-reverse">
                                            <input type="radio" class="custom-control-input currency-symbol-position"
                                                   name="projectViewNewProjectTypeRadio"
                                                   data-route="{{ route('admin.business-settings.currency-position',['right']) }}"
                                                   id="projectViewNewProjectTypeRadio2" {{(isset($config) && $config=='right')?'checked':''}}>
                                            <label class="custom-control-label media align-items-center"
                                                   for="projectViewNewProjectTypeRadio2">
                                                <i class="tio-table text-muted mr-2"></i>
                                                <span class="media-body">
                                                    {{ translate('Right') }} {{Helpers::currency_symbol()}}
                                                    </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            @php($dm_status=Helpers::get_business_settings('dm_self_registration'))
                            <div class="form-group">
                                <label>{{translate('Deliverman_self_registration')}}
                                    <i class="tio-info-outlined"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       title="{{ translate('When this field is active, delivery men can register themself using the delivery man app.') }}">
                                    </i>
                                </label>
                                <div class="input-group input-group-md-down-break">
                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="1"
                                                   name="dm_self_registration"
                                                   id="dm_self_registration_on" {{$dm_status==1?'checked':''}}>
                                            <label class="custom-control-label"
                                                   for="dm_self_registration_on">{{translate('on')}}</label>
                                        </div>
                                    </div>

                                    <div class="form-control">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" value="0"
                                                   name="dm_self_registration"
                                                   id="dm_self_registration_off" {{$dm_status==0?'checked':''}}>
                                            <label class="custom-control-label"
                                                   for="dm_self_registration_off">{{translate('off')}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-6 mt-5">
                            @php($googleMapStatus=\App\CentralLogics\Helpers::get_business_settings('google_map_status'))
                            <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                <h5 class="mb-0">{{translate('Google Map status')}}
                                    <i class="tio-info-outlined" data-toggle="tooltip" data-placement="top"
                                       title="{{ translate('When this option is enabled  google map will show all over system.') }}">
                                    </i>
                                </h5>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" name="google_map_status" id="google_map_status" {{ $googleMapStatus == 1 ? 'checked' : '' }}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6 mt-5">
                            @php($guestCheckout=\App\CentralLogics\Helpers::get_business_settings('guest_checkout'))
                            <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                <h5 class="mb-0">{{translate('Guest Checkout')}}
                                    <i class="tio-info-outlined" data-toggle="tooltip" data-placement="top"
                                       title="{{ translate('When this option is active  users may place orders as guests without logging in.') }}">
                                    </i>
                                </h5>
                                <label class="switcher">
                                    <input type="checkbox" class="switcher_input" name="guest_checkout" id="guest_checkout" {{ $guestCheckout == 1 ? 'checked' : '' }}>
                                    <span class="switcher_control"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{translate('product')}} {{translate('and')}} {{translate('category')}} {{translate('translation')}}</label>
                                <select name="language[]" id="language" data-maximum-selection-length="3"
                                        class="form-control js-select2-custom" required multiple=true>
                                    @foreach(LANGUAGE_CODE as $language)
                                        <option value="{{ $language['code'] }}">{{ $language['name'] }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="mb-0">{{translate('Admin Logo')}}</label>
                                <small class="text-danger"> * ( {{translate('ratio')}} 3:1 )</small>
                                <p class="fs-14 text-muted mb-2 mt-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                <div class="custom-file">
                                    <input type="file" name="logo" id="customFileEg1" class="custom-file-input"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">
                                    <label class="custom-file-label"
                                        for="customFileEg1">{{translate('choose')}} {{translate('file')}}</label>
                                </div>

                                <div class="text-center mt-4">
                                    <img class="upload-img-view h-auto max-w-200" id="viewer"
                                        src="{{ $logo }}" alt="{{ translate('logo_image') }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="mb-0">{{translate('Web App Logo')}}</label>
                                <small class="text-danger"> * ( {{translate('ratio')}} 1:1 )</small>
                                <p class="fs-14 text-muted mb-2 mt-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                <div class="custom-file">
                                    <input type="file" name="app_logo" id="customFileEg3" class="custom-file-input"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">
                                    <label class="custom-file-label"
                                        for="customFileEg3">{{translate('choose')}} {{translate('file')}}</label>
                                </div>

                                <div class="text-center mt-4">
                                    <img class="upload-img-view h-auto max-w-200" id="viewer_3"
                                        src="{{ $app_logo }}" alt="{{ translate('app_logo_image') }}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="form-group">
                                <label class="mb-0">{{translate('fav_icon')}}</label>
                                <small class="text-danger"> * ( {{translate('ratio')}} 1:1 )</small>
                                <p class="fs-14 text-muted mb-2 mt-0">{{ translate('Image format')}} - {{ implode(', ', array_column(IMAGE_EXTENSIONS, 'key')) }} |{{ translate('maximum size') }} - {{ readableUploadMaxFileSize('image') }}</p>
                                <div class="custom-file">
                                    <input type="file" name="fav_icon" id="customFileEg2" class="custom-file-input"
                                           accept=".{{ implode(',.', array_column(IMAGE_EXTENSIONS, 'key')) }}, |image/*"
                                           data-maxFileSize="{{ readableUploadMaxFileSize('image') }}">
                                    <label class="custom-file-label"
                                        for="customFileEg2">{{translate('choose')}} {{translate('file')}}</label>
                                </div>

                                <div class="text-center mt-4">
                                    <img class="upload-img-view h-auto max-w-145" id="viewer_2"
                                        src="{{ $fav_icon}}" alt="{{ translate('fav_icon_image') }}"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <button type="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                class="btn btn-primary demo-form-submit">{{translate('submit')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="maintenance-mode-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">
                        <i class="tio-notifications-alert mr-1"></i>
                        {{translate('System Maintenance')}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('admin.business-settings.maintenance-mode-setup')}}" id="maintenanceModeForm">
                    <?php
                    $selectedMaintenanceSystem = \App\CentralLogics\Helpers::get_business_settings('maintenance_system_setup');
                    $selectedMaintenanceDuration = \App\CentralLogics\Helpers::get_business_settings('maintenance_duration_setup');
                    $selectedMaintenanceMessage = \App\CentralLogics\Helpers::get_business_settings('maintenance_message_setup');
                    $maintenanceMode = \App\CentralLogics\Helpers::get_business_settings('maintenance_mode') ?? 0;
                    ?>
                    <div class="modal-body">
                        @csrf
                        <div class="d-flex flex-column g-2">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <p>*{{ translate('By turning on maintenance mode Control your all system & function') }}</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                        <h5 class="mb-0">{{translate('Maintenance Mode')}}</h5>
                                        <label class="toggle-switch toggle-switch-sm">
                                            <input type="checkbox" class="toggle-switch-input" name="maintenance_mode" id="maintenance-mode-checkbox"
                                                {{ $maintenanceMode ?'checked':''}}>
                                            <span class="toggle-switch-label text mb-0">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-4">
                                    <h3>{{ translate('Select System') }}</h3>
                                    <p>{{ translate('Select the systems you want to temporarily deactivate for maintenance') }}</p>
                                </div>
                                <div class="col-xl-8">
                                    <div class="border p-3">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input system-checkbox" name="all_system" type="checkbox"
                                                       {{ in_array('branch_panel', $selectedMaintenanceSystem) &&
                                                            in_array('customer_app', $selectedMaintenanceSystem) &&
                                                            in_array('web_app', $selectedMaintenanceSystem) &&
                                                            in_array('deliveryman_app', $selectedMaintenanceSystem) ? 'checked' :'' }}
                                                       id="allSystem">
                                                <label class="form-check-label" for="allSystem">{{ translate('All System') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input system-checkbox" name="branch_panel" type="checkbox"
                                                       {{ in_array('branch_panel', $selectedMaintenanceSystem) ? 'checked' :'' }}
                                                       id="branchPanel">
                                                <label class="form-check-label" for="branchPanel">{{ translate('Branch Panel') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input system-checkbox" name="customer_app" type="checkbox"
                                                       {{ in_array('customer_app', $selectedMaintenanceSystem) ? 'checked' :'' }}
                                                       id="customerApp">
                                                <label class="form-check-label" for="customerApp">{{ translate('Customer App') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input system-checkbox" name="web_app" type="checkbox"
                                                       {{ in_array('web_app', $selectedMaintenanceSystem) ? 'checked' :'' }}
                                                       id="webApp">
                                                <label class="form-check-label" for="webApp">{{ translate('Web App') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input system-checkbox" name="deliveryman_app" type="checkbox"
                                                       {{ in_array('deliveryman_app', $selectedMaintenanceSystem) ? 'checked' :'' }}
                                                       id="deliverymanApp">
                                                <label class="form-check-label" for="deliverymanApp">{{ translate('Deliveryman App') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-4">
                                    <h3>{{ translate('Maintenance Date') }} & {{ translate('Time') }}</h3>
                                    <p>{{ translate('Choose the maintenance mode duration for your selected system.') }}</p>
                                </div>
                                <div class="col-xl-8">
                                    <div class="border p-3">
                                        <div class="d-flex flex-wrap gap-3 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="maintenance_duration"
                                                       {{ isset($selectedMaintenanceDuration['maintenance_duration']) && $selectedMaintenanceDuration['maintenance_duration'] == 'one_day' ? 'checked' : '' }}
                                                       value="one_day" id="one_day">
                                                <label class="form-check-label" for="one_day">{{ translate('For 24 Hours') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="maintenance_duration"
                                                       {{ isset($selectedMaintenanceDuration['maintenance_duration']) && $selectedMaintenanceDuration['maintenance_duration'] == 'one_week' ? 'checked' : '' }}
                                                       value="one_week" id="one_week">
                                                <label class="form-check-label" for="one_week">{{ translate('For 1 Week') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="maintenance_duration"
                                                       {{ isset($selectedMaintenanceDuration['maintenance_duration']) && $selectedMaintenanceDuration['maintenance_duration'] == 'until_change' ? 'checked' : '' }}
                                                       value="until_change" id="until_change">
                                                <label class="form-check-label" for="until_change">{{ translate('Until I change') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="maintenance_duration"
                                                       {{ isset($selectedMaintenanceDuration['maintenance_duration']) && $selectedMaintenanceDuration['maintenance_duration'] == 'customize' ? 'checked' : '' }}
                                                       value="customize" id="customize">
                                                <label class="form-check-label" for="customize">{{ translate('Customize') }}</label>
                                            </div>
                                        </div>
                                        <div class="row start-and-end-date">
                                            <div class="col-md-6">
                                                <label>{{ translate('Start Date') }}</label>
                                                <input type="datetime-local" class="form-control" name="start_date" id="startDate"
                                                       value="{{ old('start_date', $selectedMaintenanceDuration['start_date'] ?? '') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>{{ translate('End Date') }}</label>
                                                <input type="datetime-local" class="form-control" name="end_date" id="endDate"
                                                       value="{{ old('end_date', $selectedMaintenanceDuration['end_date'] ?? '') }}" required>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <small id="dateError" class="form-text text-danger" style="display: none;">{{ translate('Start date cannot be greater than end date.') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="advanceFeatureButtonDiv">
                            <div class="d-flex justify-content-center mt-3">
                                <a href="#" id="advanceFeatureToggle" class="d-block mb-3 maintenance-advance-feature-button">{{ translate('Advance Feature') }}</a>
                            </div>
                        </div>

                        <div class="row mt-4" id="advanceFeatureSection" style="display: none;">
                            <div class="col-xl-4">
                                <h3>{{ translate('Maintenance Massage') }}</h3>
                                <p>{{ translate('Select & type what massage you want to see your selected system when maintenance mode is active.') }}</p>
                            </div>
                            <div class="col-xl-8">
                                <div class="border p-3">
                                    <div class="form-group">
                                        <label>{{ translate('Show Contact Info') }}</label>
                                        <div class="d-flex flex-wrap">
                                            <div class="form-check mr-4">
                                                <input class="form-check-input" type="checkbox" name="business_number"
                                                       {{ isset($selectedMaintenanceMessage) && $selectedMaintenanceMessage['business_number'] == 1 ? 'checked' : '' }}
                                                       id="businessNumber">
                                                <label class="form-check-label ml-1" for="businessNumber">{{ translate('Business Number') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="business_email"
                                                       {{ isset($selectedMaintenanceMessage) && $selectedMaintenanceMessage['business_email'] == 1 ? 'checked' : '' }}
                                                       id="businessEmail">
                                                <label class="form-check-label ml-1" for="businessEmail">{{ translate('Business Email') }}</label>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label>{{ translate('Maintenance Message') }}
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('The maximum character limit is 100') }}">
                                            </i>
                                        </label>
                                        <input type="text" class="form-control" name="maintenance_message" placeholder="We're Working On Something Special!"
                                               maxlength="100" value="{{ $selectedMaintenanceMessage['maintenance_message'] ?? '' }}">
                                    </div>
                                    <div class="form-group">
                                        <label>{{ translate('Message Body') }}
                                            <i class="tio-info-outined"
                                               data-toggle="tooltip"
                                               data-placement="top"
                                               title="{{ translate('The maximum character limit is 255') }}">
                                            </i>
                                        </label>
                                        <textarea class="form-control" name="message_body" maxlength="255" rows="3" placeholder="{{ translate('Our system is currently undergoing maintenance to bring you an even tastier experience. Hang tight while we make the dishes.') }}">{{ isset($selectedMaintenanceMessage) && $selectedMaintenanceMessage['message_body'] ? $selectedMaintenanceMessage['message_body'] : ''}}</textarea>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="#" id="seeLessToggle" class="d-block mb-3 maintenance-advance-feature-button">{{ translate('See Less') }}</a>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cancelButton">{{ translate('Cancel') }}</button>
                            <button type="button" class="btn btn-primary demo-form-submit" @if(env('APP_MODE') != 'demo')
                                onclick="validateMaintenanceMode()"
                                @endif>{{ translate('Save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Checking -->
    <div class="modal fade" id="modalUncheckedDistanceExist" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="my-4">
                            <img src="{{ asset('public/assets/admin/svg/components/map-icon.svg') }}" alt="Checked Icon">
                        </div>
                        <div class="my-4">
                            <h4>{{ translate('Turn off google Map') }}?</h4>
                            <p>{{ translate('One or more Branch delivery charge setup is based on distance, so you must need to update branch wise delivery charge setup to be Fixed or based on Area/Zip code.') }}</p>
                        </div>
                        <div class="my-4">
                            <a class="btn btn-primary" target="_blank" href="{{ route('admin.business-settings.delivery-fee-setup') }}">{{ translate('Go to Delivery Charge Setup') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for checking -->
    <div class="modal fade" id="modalUncheckedDistanceNotExist" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="text-center mb-5">
                            <img src="{{ asset('public/assets/admin/svg/components/map-icon.svg') }}" alt="Unchecked Icon" class="mb-5">
                            <h4>{{ translate('Are You Sure') }}?</h4>
                            <p>{{ translate('By Turning On the Google Maps you need to setup following setting to get the map work properly.') }}</p>
                        </div>

                        <div class="row g-2 ">
                            <div class="col-12 mb-2">
                                <a class="d-flex align-items-center border rounded px-3 py-2 g-1"
                                   href="{{ route('admin.customer.list') }}"
                                   target="_blank">
                                    <img src="{{ asset('public/assets/admin/svg/components/people.svg') }}" width="21" alt="">
                                    <span>{{ translate('Map Location in Customer Addresses') }}</span>
                                </a>
                            </div>
                            <div class="col-12 mb-2">
                                <a class="d-flex align-items-center border rounded px-3 py-2 g-1"
                                   href="{{ route('admin.branch.list') }}"
                                   target="_blank">
                                    <img src="{{ asset('public/assets/admin/svg/components/branch.svg') }}" width="21" alt="">
                                    <span>{{ translate('Map in Branch Coverage Area') }}</span>
                                </a>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="d-flex align-items-center border rounded px-3 py-2 g-1">
                                    <img src="{{ asset('public/assets/admin/svg/components/delivery-car.svg') }}" width="21" alt="">
                                    <span class="text-primary">{{ translate('Deliveryman Live Location on Customer & Deliveryman App & web') }}</span>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <a class="d-flex align-items-center border rounded px-3 py-2 g-1"
                                   href="{{ route('admin.business-settings.delivery-fee-setup') }}"
                                   target="_blank">
                                    <img src="{{ asset('public/assets/admin/svg/components/delivery-charge.svg') }}" width="21" alt="">
                                    <span>{{ translate('Delivery Charge Setup') }}</span>
                                </a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center my-4 gap-3">
                            <button class="btn btn-secondary ml-2" id="cancelButtonNotExist">{{ translate('Cancel') }}</button>
                            <button class="btn btn-danger" id="turnOffButton">{{ translate('Ok, Turn Off') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Checking -->
    <div class="modal fade" id="modalCheckedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="my-4">
                            <img src="{{ asset('public/assets/admin/svg/components/map-icon.svg') }}" alt="Checked Icon">
                        </div>
                        <div class="my-4">
                            <h4>{{ translate('Turn on google Map') }}?</h4>
                            <p>{{ translate('By turning on this option, you can be able to see the map on customer app & website, admin panel, branch panel and deliveryman app. You can now also setup your delivery charges based on distance(km) from ') }}
                                <a class="" target="_blank" href="{{ route('admin.business-settings.delivery-fee-setup') }}">{{ translate('This Page') }}</a>
                            </p>
                            <p>{{ translate('note') }}: {{ translate('Currently Delivery Charge is set based on Zip Code/Area wise or Based on Fixed Delivery Charge') }}</p>
                        </div>
                        <div class="my-4">
                            <button class="btn btn-primary" id="turnOnButton">{{ translate('Yes, Turn On') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin/js/business-settings.js') }}"></script>

    <script>
        "use strict";

        @php($language=\App\Models\BusinessSetting::where('key','language')->first())
        @php($language = $language->value ?? null)
        let language = <?php echo($language); ?>;
        $('[id=language]').val(language);

        $('.maintenance-mode-off').on('click', function (){
            @if(env('APP_MODE')=='demo'){
                toastr.info('Disabled for demo version!')
            }@else{
                Swal.fire({
                    title: '{{translate("Are you sure?")}}',
                    text:  '{{translate("Be careful before you turn on/off maintenance mode")}}',
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#377dff',
                    cancelButtonText: '{{translate("No")}}',
                    confirmButtonText: '{{translate("Yes")}}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.get({
                            url: '{{route('admin.business-settings.maintenance-mode')}}',
                            contentType: false,
                            processData: false,
                            beforeSend: function () {
                                $('#loading').show();
                            },
                            success: function (data) {
                                toastr.success(data.message);
                            },
                            complete: function () {
                                $('#loading').hide();
                            },
                        });
                    } else {
                        location.reload();
                    }
                })
            }
            @endif
        })
    </script>
    <script>
        $('.maintenance-mode-show').click(function (){
            $('#maintenance-mode-modal').modal('show');
        });

        $(document).ready(function() {
            var initialMaintenanceMode = $('#maintenance-mode-input').is(':checked');

            $('#maintenance-mode-modal').on('show.bs.modal', function () {
                var initialMaintenanceModeModel = $('#maintenance-mode-input').is(':checked');
                $('#maintenance-mode-checkbox').prop('checked', initialMaintenanceModeModel);
            });

            $('#maintenance-mode-modal').on('hidden.bs.modal', function () {
                $('#maintenance-mode-input').prop('checked', initialMaintenanceMode);
            });

            $('#cancelButton').click(function() {
                $('#maintenance-mode-input').prop('checked', initialMaintenanceMode);
                $('#maintenance-mode-modal').modal('hide');
            });

            $('#maintenance-mode-checkbox').change(function() {
                $('#maintenance-mode-input').prop('checked', $(this).is(':checked'));
            });

            $('#advanceFeatureToggle').click(function(event) {
                event.preventDefault();
                $('#advanceFeatureSection').show();
                $('#advanceFeatureButtonDiv').hide();
            });

            $('#seeLessToggle').click(function(event) {
                event.preventDefault();
                $('#advanceFeatureSection').hide();
                $('#advanceFeatureButtonDiv').show();
            });

            $('#allSystem').change(function() {
                var isChecked = $(this).is(':checked');
                $('.system-checkbox').prop('checked', isChecked);
            });

            // If any other checkbox is unchecked, also uncheck "All System"
            $('.system-checkbox').not('#allSystem').change(function() {
                if (!$(this).is(':checked')) {
                    $('#allSystem').prop('checked', false);
                } else {
                    // Check if all system-related checkboxes are checked
                    if ($('.system-checkbox').not('#allSystem').length === $('.system-checkbox:checked').not('#allSystem').length) {
                        $('#allSystem').prop('checked', true);
                    }
                }
            });

            var startDate = $('#startDate');
            var endDate = $('#endDate');
            var dateError = $('#dateError');

            function updateDatesBasedOnDuration(selectedOption) {
                if (selectedOption === 'one_day' || selectedOption === 'one_week') {
                    var now = new Date();
                    var timezoneOffset = now.getTimezoneOffset() * 60000;
                    var formattedNow = new Date(now.getTime() - timezoneOffset).toISOString().slice(0, 16);

                    if (selectedOption === 'one_day') {
                        var end = new Date(now);
                        end.setDate(end.getDate() + 1);
                    } else if (selectedOption === 'one_week') {
                        var end = new Date(now);
                        end.setDate(end.getDate() + 7);
                    }

                    var formattedEnd = new Date(end.getTime() - timezoneOffset).toISOString().slice(0, 16);

                    startDate.val(formattedNow).prop('readonly', false).prop('required', true);
                    endDate.val(formattedEnd).prop('readonly', false).prop('required', true);
                    $('.start-and-end-date').removeClass('opacity');
                    dateError.hide();
                } else if (selectedOption === 'until_change') {
                    startDate.val('').prop('readonly', true).prop('required', false);
                    endDate.val('').prop('readonly', true).prop('required', false);
                    $('.start-and-end-date').addClass('opacity');
                    dateError.hide();
                } else if (selectedOption === 'customize') {
                    startDate.prop('readonly', false).prop('required', true);
                    endDate.prop('readonly', false).prop('required', true);
                    $('.start-and-end-date').removeClass('opacity');
                    dateError.hide();
                }
            }

            function validateDates() {
                var start = new Date(startDate.val());
                var end = new Date(endDate.val());
                if (start > end) {
                    dateError.show();
                    startDate.val('');
                    endDate.val('');
                } else {
                    dateError.hide();
                }
            }

            // Initial load
            var selectedOption = $('input[name="maintenance_duration"]:checked').val();
            updateDatesBasedOnDuration(selectedOption);

            // When maintenance duration changes
            $('input[name="maintenance_duration"]').change(function() {
                var selectedOption = $(this).val();
                updateDatesBasedOnDuration(selectedOption);
            });

            // When start date or end date changes
            $('#startDate, #endDate').change(function() {
                $('input[name="maintenance_duration"][value="customize"]').prop('checked', true);
                startDate.prop('readonly', false).prop('required', true);
                endDate.prop('readonly', false).prop('required', true);
                validateDates();
            });

            // // Form validation before submission
            $('#maintenanceModeForm').on('submit', function(e) {
                let selectedOption = $('input[name="maintenance_duration"]:checked').val();

                if (selectedOption === 'customize') {
                    let startDateValue = $('#startDate').val();
                    let endDateValue = $('#endDate').val();

                    if (!startDateValue || !endDateValue) {
                        e.preventDefault();
                        dateError.text('Please provide both start and end dates.').show();
                        return false;
                    }
                }
                dateError.hide();
            });
        });

        function validateMaintenanceMode() {
            const maintenanceModeChecked = $('#maintenance-mode-checkbox').is(':checked');

            if (maintenanceModeChecked) {
                const isAnySystemSelected = $('.system-checkbox').is(':checked');

                if (!isAnySystemSelected) {
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ translate("Please select a system") }}!',
                        text: '{{ translate("You must select at least one system when activating Maintenance Mode.") }}',
                        confirmButtonText: '{{ translate("OK") }}',
                        confirmButtonColor: '#673ab7',
                    });
                    return false;
                }
            }

            $('#maintenanceModeForm').submit();
        }

        $('#google_map_status').change(function() {
            if ($(this).is(':checked')) {
                $('#modalCheckedModal').modal('show');
            } else {
                $.ajax({
                    url: '{{ route('admin.business-settings.check-distance-based-delivery') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.hasDistanceBasedDelivery) {
                            $('#modalUncheckedDistanceExist').modal('show');
                            $('#google_map_status').prop('checked', true);
                        }else{
                            $('#modalUncheckedDistanceNotExist').modal('show');
                        }
                    }
                });
            }
        });

        let turnOnConfirmed = false; // Flag to track if "Yes, Turn On" was clicked

        // Handle the "Yes, Turn On" button click inside the modalCheckedModal
        $('#turnOnButton').click(function() {
            turnOnConfirmed = true; // Set flag when "Yes, Turn On" is clicked
            $('#modalCheckedModal').modal('hide'); // Hide the modal
        });

        // Revert checkbox state when modalCheckedModal is closed without confirmation
        $('#modalCheckedModal').on('hidden.bs.modal', function () {
            if (!turnOnConfirmed) {
                $('#google_map_status').prop('checked', false); // Revert to unchecked if not confirmed
            }
            turnOnConfirmed = false; // Reset the flag after modal closes
        });

        let turnOffConfirmed = false;

        $('#cancelButtonNotExist').click(function() {
            $('#google_map_status').prop('checked', true);
            $('#modalUncheckedDistanceNotExist').modal('hide');
            turnOffConfirmed = false;
        });

        $('#turnOffButton').click(function() {
            turnOffConfirmed = true;
            $('#modalUncheckedDistanceNotExist').modal('hide');
        });

        $('#modalUncheckedDistanceNotExist').on('hidden.bs.modal', function () {
            if (!turnOffConfirmed) {
                $('#google_map_status').prop('checked', true);
            }
            turnOffConfirmed = false;
        });
    </script>
@endpush
