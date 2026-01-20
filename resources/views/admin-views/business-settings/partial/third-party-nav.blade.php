<ul class="list-unstyled">
    <li class="{{Request::is('admin/business-settings/sms-module')?'active':''}}"><a href="{{route('admin.business-settings.sms-module')}}">{{translate('SMS_Module')}}</a></li>
    <li class="{{Request::is('admin/business-settings/mail-config')?'active':''}}"><a href="{{route('admin.business-settings.mail-config')}}">{{translate('Mail_Config')}}</a></li>
    <li class="{{Request::is('admin/business-settings/payment-method')?'active':''}}"><a href="{{route('admin.business-settings.payment-method')}}">{{translate('Payment_Methods')}}</a></li>
    <li class="{{Request::is('admin/business-settings/recaptcha*')?'active':''}}"><a href="{{route('admin.business-settings.recaptcha_index')}}">{{translate('Recaptcha')}}</a></li>
    <li class="{{Request::is('admin/business-settings/map-api-settings')?'active':''}}"><a href="{{route('admin.business-settings.map_api_settings')}}">{{translate('Google_Map_APIs')}}</a></li>
    <li class="{{Request::is('admin/business-settings/fcm-index')?'active':''}}"><a href="{{route('admin.business-settings.fcm-index')}}">{{translate('Push_Notification')}}</a></li>
    <li class="{{Request::is('admin/business-settings/firebase-message-config')?'active':''}}"><a href="{{route('admin.business-settings.firebase_message_config_index')}}">{{translate('Firebase_Message_Config')}}</a></li>
    <li class="{{Request::is('admin/business-settings/social-media-login')?'active':''}}"><a href="{{route('admin.business-settings.social-media-login')}}">{{translate('social_media_login')}}</a></li>
    <li class="{{Request::is('admin/business-settings/social-media-chat')?'active':''}}"><a href="{{route('admin.business-settings.social-media-chat')}}">{{translate('social_media_chat')}}</a></li>
    <li class="{{Request::is('admin/business-settings/firebase-auth')?'active':''}}"><a href="{{route('admin.business-settings.firebase-auth')}}">{{translate('firebase_auth')}}</a></li>
</ul>
