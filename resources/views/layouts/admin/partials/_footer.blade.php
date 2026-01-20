<footer class="footer">
    <div class="row justify-content-between align-items-center gy-2">
        <div class="col-lg-4">
            <p class="text-capitalize text-center text-lg-left mb-0">
                {{ Helpers::get_business_settings('footer_text') }}
            </p>
        </div>
        <div class="col-lg-8">
            <div class="d-flex justify-content-center justify-content-lg-end">
                <ul class="list-inline-menu justify-content-center">
                    <li>
                        <a href="{{route('admin.business-settings.ecom-setup')}}">
                            <span>{{translate('Business')}} {{translate('setup')}}</span>
                            <i class="tio-settings"></i>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('admin.settings')}}">
                            <span>{{translate('profile')}}</span>
                            <i class="tio-user"></i>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('admin.dashboard')}}">
                            <span>{{translate('Home')}}</span>
                            <i class="tio-home-outlined"></i>
                        </a>
                    </li>
                    <li>
                        <label class="badge badge-soft-success">
                            {{ translate('Software Version') }} : {{ env('SOFTWARE_VERSION') }}
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
