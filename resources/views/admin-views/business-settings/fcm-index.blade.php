@extends('layouts.admin.app')

@section('title', translate('FCM Settings'))

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

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">{{translate('Firebase Push Notification Setup')}}</h5>
            </div>
            <div class="card-body">
                <form action="{{route('admin.business-settings.update-fcm')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @php($key=\App\Models\BusinessSetting::where('key','push_notification_key')->first()->value)
                    <div class="form-group d--none">
                        <label class="input-label">{{translate('server')}} {{translate('key')}}</label>
                        <textarea name="push_notification_key" class="form-control">{{env('APP_MODE')=='demo'?'':$key}}</textarea>
                    </div>
                    @php($serviceFileContent = Helpers::get_business_settings('push_notification_service_file_content'))
                    <div class="form-group">
                        <label class="input-label">{{translate('service_file_content')}}
                            <i class="tio-info cursor-pointer" data-toggle="tooltip" data-placement="top"
                               title="{{ translate('select and copy all the service file content and add here') }}">
                            </i>
                        </label>
                        <textarea name="push_notification_service_file_content" class="form-control" rows="15"
                                  required>{{env('APP_MODE')!='demo'?json_encode($serviceFileContent):''}}</textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                class="btn btn-primary demo-form-submit">{{translate('save')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{translate('push')}} {{translate('messages')}}</h5>
            </div>
            <div class="card-body">
                <form action="{{route('admin.business-settings.update-fcm-messages')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        @php($orderPendingMessage=\App\Models\BusinessSetting::where('key','order_pending_message')->first()->value)
                        @php($data=json_decode($orderPendingMessage,true))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="pending_status">
                                        <input type="checkbox" name="pending_status" class="switcher_input"
                                                value="1" id="pending_status" {{$data['status']==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="pending_status" class="text-dark mb-0 cursor-pointer">{{translate('order')}} {{translate('pending')}} {{translate('message')}}</label>
                                </div>
                                <textarea name="pending_message" class="form-control">{{$data['message']}}</textarea>
                            </div>
                        </div>

                        @php($orderConfirmationMessage=\App\Models\BusinessSetting::where('key','order_confirmation_msg')->first()->value)
                        @php($data=json_decode($orderConfirmationMessage,true))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="confirm_status">
                                        <input type="checkbox" name="confirm_status" class="switcher_input"
                                                value="1" id="confirm_status" {{$data['status']==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="confirm_status" class="text-dark mb-0 cursor-pointer">{{translate('order')}} {{translate('confirmation')}} {{translate('message')}}</label>
                                </div>
                                <textarea name="confirm_message" class="form-control">{{$data['message']}}</textarea>
                            </div>
                        </div>

                        @php($orderProcessingMessage=\App\Models\BusinessSetting::where('key','order_processing_message')->first()->value)
                        @php($data=json_decode($orderProcessingMessage,true))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="processing_status">
                                        <input type="checkbox" name="processing_status"
                                                class="switcher_input"
                                                value="1" id="processing_status" {{$data['status']==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="processing_status" class="text-dark mb-0 cursor-pointer">{{translate('order')}} {{translate('processing')}} {{translate('message')}}</label>
                                </div>
                                <textarea name="processing_message" class="form-control">{{$data['message']}}</textarea>
                            </div>
                        </div>

                        @php($orderOutForDeliveryMessage=\App\Models\BusinessSetting::where('key','out_for_delivery_message')->first()->value)
                        @php($data=json_decode($orderOutForDeliveryMessage,true))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="out_for_delivery">
                                        <input type="checkbox" name="out_for_delivery_status"
                                                class="switcher_input"
                                                value="1" id="out_for_delivery" {{$data['status']==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="out_for_delivery" class="text-dark mb-0 cursor-pointer">{{translate('Order_Out_for_delivery_Message')}}</label>
                                </div>
                                <textarea name="out_for_delivery_message" class="form-control">{{$data['message']}}</textarea>
                            </div>
                        </div>

                        @php($orderDeliveredMessage=\App\Models\BusinessSetting::where('key','order_delivered_message')->first()->value)
                        @php($data=json_decode($orderDeliveredMessage,true))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="delivered_status">
                                        <input type="checkbox" name="delivered_status" class="switcher_input"
                                                value="1" id="delivered_status" {{$data['status']==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="delivered_status" class="text-dark mb-0 cursor-pointer">{{translate('Order_Delivered_Message')}}</label>
                                </div>
                                <textarea name="delivered_message" class="form-control">{{$data['message']}}</textarea>
                            </div>
                        </div>

                        @php($data= Helpers::get_business_settings('customer_notify_message'))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="customer_notify">
                                        <input type="checkbox" name="customer_notify_status"
                                                class="switcher_input"
                                                value="1"
                                                id="customer_notify" {{isset($data) && $data['status']==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="customer_notify" class="text-dark mb-0 cursor-pointer">{{translate('Deliveryman_Assign_Notification_for_Customer')}}</label>
                                </div>

                                <textarea name="customer_notify_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($deliverymanAssignMessage=\App\Models\BusinessSetting::where('key','delivery_boy_assign_message')->first()->value)
                        @php($data=json_decode($deliverymanAssignMessage,true))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="delivery_boy_assign">
                                        <input type="checkbox" name="delivery_boy_assign_status"
                                                class="switcher_input"
                                                value="1"
                                                id="delivery_boy_assign" {{$data['status']==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="delivery_boy_assign" class="text-dark mb-0 cursor-pointer">{{translate('Deliveryman_Assaign_Message')}}</label>
                                </div>

                                <textarea name="delivery_boy_assign_message" class="form-control">{{$data['message']}}</textarea>
                            </div>
                        </div>

                        @php($deliveryStartMessage=\App\Models\BusinessSetting::where('key','delivery_boy_start_message')->first()->value)
                        @php($data=json_decode($deliveryStartMessage,true))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="delivery_boy_start_status">
                                        <input type="checkbox" name="delivery_boy_start_status"
                                                class="switcher_input"
                                                value="1"
                                                id="delivery_boy_start_status" {{$data['status']==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="delivery_boy_start_status" class="text-dark mb-0 cursor-pointer">{{translate('Deliveryman_Start_Message')}}</label>
                                </div>
                                <textarea name="delivery_boy_start_message" class="form-control">{{$data['message']}}</textarea>
                            </div>
                        </div>

                        @php($deliveryDeliveredMessage=\App\Models\BusinessSetting::where('key','delivery_boy_delivered_message')->first()->value)
                        @php($data=json_decode($deliveryDeliveredMessage,true))
                        <div class="col-md-6">
                            <div class="form-group">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <label class="switcher" for="delivery_boy_delivered">
                                    <input type="checkbox" name="delivery_boy_delivered_status"
                                            class="switcher_input"
                                            value="1"
                                            id="delivery_boy_delivered" {{$data['status']==1?'checked':''}}>
                                    <span class="switcher_control"></span>
                                </label>
                                <label for="delivery_boy_delivered" class="text-dark mb-0 cursor-pointer">{{translate('Deliveryman_Delivered_Message')}}</label>
                            </div>
                                <textarea name="delivery_boy_delivered_message" class="form-control">{{$data['message']}}</textarea>
                            </div>
                        </div>

                        @php($data=Helpers::get_business_settings('returned_message'))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher"
                                            for="returned_status">
                                        <input type="checkbox" name="returned_status"
                                                class="switcher_input"
                                                value="1"
                                                id="returned_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="returned_status" class="text-dark mb-0 cursor-pointer">{{translate('Order_returned_message')}}</label>
                                </div>
                                <textarea name="returned_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($data=Helpers::get_business_settings('failed_message'))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="failed_status">
                                        <input type="checkbox" name="failed_status"
                                                class="switcher_input"
                                                value="1"
                                                id="failed_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="failed_status" class="text-dark mb-0 cursor-pointer">{{translate('Order_failed_message')}}</label>
                                </div>
                                <textarea name="failed_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($data=Helpers::get_business_settings('canceled_message'))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="canceled_status">
                                        <input type="checkbox" name="canceled_status"
                                                class="switcher_input"
                                                value="1"
                                                id="canceled_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="canceled_status" class="text-dark mb-0 cursor-pointer">{{translate('Order_canceled_message')}}</label>
                                </div>

                                <textarea name="canceled_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                        @php($data=Helpers::get_business_settings('wallet_rewarded_message'))
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <label class="switcher" for="wallet_rewarded_status">
                                        <input type="checkbox" name="wallet_rewarded_status"
                                                class="switcher_input"
                                                value="1"
                                                id="wallet_rewarded_status" {{(isset($data['status']) && $data['status']==1)?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                    <label for="wallet_rewarded_status" class="text-dark mb-0 cursor-pointer">{{translate('wallet_rewarded_message')}}</label>
                                </div>

                                <textarea name="wallet_rewarded_message" class="form-control">{{$data['message']??''}}</textarea>
                            </div>
                        </div>

                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                class="btn btn-primary demo-form-submit">{{translate('save')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
