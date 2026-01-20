<ul class="list-unstyled">
    <li class="{{Request::is('admin/business-settings/about-us')?'active':''}}"><a href="{{route('admin.business-settings.about-us')}}">{{translate('about_us')}}</a></li>
    <li class="{{Request::is('admin/business-settings/terms-and-conditions')?'active':''}}"><a href="{{route('admin.business-settings.terms-and-conditions')}}">{{translate('terms & condition')}}</a></li>
    <li class="{{Request::is('admin/business-settings/privacy-policy')?'active':''}}"><a href="{{route('admin.business-settings.privacy-policy')}}">{{translate('privacy_policy')}}</a></li>
    <li class="{{Request::is('admin/business-settings/cancellation-page*')?'active':''}}"><a href="{{route('admin.business-settings.cancellation_page_index')}}">{{translate('cancellation_policy')}}</a></li>
    <li class="{{Request::is('admin/business-settings/refund-page*')?'active':''}}"><a href="{{route('admin.business-settings.refund_page_index')}}">{{translate('refund_policy')}}</a></li>
    <li class="{{Request::is('admin/business-settings/return-page*')?'active':''}}"><a href="{{route('admin.business-settings.return_page_index')}}">{{translate('return_policy')}}</a></li>
</ul>
