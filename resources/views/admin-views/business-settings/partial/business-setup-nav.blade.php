<ul class="list-unstyled">
    <li class="{{Request::is('admin/business-settings/ecom-setup')?'active':''}}"><a href="{{route('admin.business-settings.ecom-setup')}}">{{translate('business_setup')}}</a></li>
    <li class="{{Request::is('admin/business-settings/delivery-fee-setup')?'active':''}}"><a href="{{route('admin.business-settings.delivery-fee-setup')}}">{{translate('delivery_fee_setup')}}</a></li>
    <li class="{{Request::is('admin/business-settings/login-setup')?'active':''}}"><a href="{{route('admin.business-settings.login-setup')}}">{{translate('Customer Login')}}</a></li>
    <li class="{{Request::is('admin/business-settings/otp-setup')?'active':''}}"><a href="{{route('admin.business-settings.otp-setup')}}">{{translate('OTP_setup')}}</a></li>
    <li class="{{Request::is('admin/business-settings/cookies-setup')?'active':''}}"><a href="{{route('admin.business-settings.cookies-setup')}}">{{translate('cookies_setup')}}</a></li>
    <li class="{{Request::is('admin/business-settings/customer-setup')?'active':''}}"><a href="{{route('admin.business-settings.customer-setup')}}">{{translate('customer')}}</a></li>
</ul>
