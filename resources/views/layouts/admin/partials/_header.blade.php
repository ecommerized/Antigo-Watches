@if (env('APP_MODE')=='demo')
    <div class="__announcement-bar" style="background-image: url({{ asset('public/assets/website-top-header.png') }})">
        <div class="container">
            <div class="wrapper">
                <div class="txt">
                    This is a demo website - Buy genuine Hexacom using our official link !
                </div>
                <a href="https://codecanyon.net/item/emarket-ecommerce-app-with-laravel-admin-panel-delivery-man-app/31157454?s_rank=20" class="click" target="_blank">Click Now <img src="{{ asset('public/assets/arrowww.png') }}" alt=""></a>
                <a href="https://codecanyon.net/item/emarket-ecommerce-app-with-laravel-admin-panel-delivery-man-app/31157454?s_rank=20" class="btn btn-sm" style="background-color: #FF7500; color:#ffffff" target="_blank">Buy Now</a>
            </div>
        </div>
    </div>
@endif
<div id="headerMain" class="d-none">
    <header id="header"
            class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper">
                @php($logo = Helpers::get_business_settings('logo'))
                <a class="navbar-brand" href="{{route('admin.dashboard')}}" aria-label="">
                    <img class="navbar-brand-logo"
                         src="{{Helpers::onErrorImage(
                            $logo,
                            asset('storage/app/public/ecommerce').'/' . $logo,
                            asset('public/assets/admin/img/160x160/img2.jpg') ,
                            'ecommerce/')}}" alt="{{ translate('Logo') }}">
                    <img class="navbar-brand-logo-mini"
                         src="{{Helpers::onErrorImage(
                            $logo,
                            asset('storage/app/public/ecommerce').'/' . $logo,
                            asset('public/assets/admin/img/160x160/img2.jpg') ,
                            'ecommerce/')}}"
                         alt="{{ translate('Logo') }}">
                </a>
            </div>

            <div class="navbar-nav-wrap-content-left d-xl-none">
                <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mr-3">
                    <i class="tio-first-page navbar-vertical-aside-toggle-short-align" data-toggle="tooltip"
                       data-placement="right" title="Collapse"></i>
                    <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                       data-template='<div class="tooltip d-none d-sm-block" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                       data-toggle="tooltip" data-placement="right" title="Expand"></i>
                </button>
            </div>

            <div class="navbar-nav-wrap-content-right">
                <ul class="navbar-nav align-items-center flex-row">
                    <li class="nav-item d-none d-sm-inline-block">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                               href="{{route('admin.message.list')}}">
                                <i class="tio-messages-outlined"></i>
                                @php($message=\App\Models\Conversation::where('checked',0)->count())
                                @if($message!=0)
                                    <span class="btn-status btn-status-danger">{{ $message }}</span>
                                @endif
                            </a>
                        </div>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle"
                               href="{{route('admin.orders.list',['status'=>'all'])}}">
                                <i class="tio-shopping-cart-outlined"></i>
                                <span class="btn-status btn-status-danger">{{\App\Models\Order::where(['checked' => 0])->count()}}</span>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item ml-md-3">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper media align-items-center gap-3 bg-transparent dropdown-toggle dropdown-toggle-left-arrow" href="javascript:;"
                               data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>
                                <div class="d-none d-md-block media-body text-right">
                                    <h5 class="profile-name text-capitalize mb-0">{{auth('admin')->user()->f_name}}</h5>
                                    <span class="fs-12 text-capitalize">{{ translate('Super Admin') }}</span>
                                </div>
                                <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img"
                                         src="{{auth('admin')->user()->image_fullpath}}"
                                         alt="{{ translate('Image') }}">
                                    <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                </div>
                            </a>

                            <div id="accountNavbarDropdown"
                                 class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account">
                                <div class="dropdown-item-text">
                                    <div class="media gap-3 align-items-center">
                                        <div class="avatar avatar-sm avatar-circle mr-2" style="min-width: 40px">
                                            <img class="avatar-img"
                                                 src="{{auth('admin')->user()->image_fullpath}}"
                                                 alt="{{ translate('Image') }}">
                                        </div>
                                        <div class="media-body">
                                            <span class="card-title h5">{{auth('admin')->user()->f_name}}</span>
                                            <span class="card-text">{{ Str::limit(auth('admin')->user()->email, 10)}}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{route('admin.settings')}}">
                                    <span class="text-truncate pr-2" title="Settings">{{\App\CentralLogics\translate('settings')}}</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="javascript:" onclick="Swal.fire({
                                    title:'{{translate("Do you want to logout?")}}',
                                    showDenyButton: true,
                                    showCancelButton: true,
                                    confirmButtonColor: '#673ab7',
                                    cancelButtonColor: '#363636',
                                    confirmButtonText: '{{translate("Yes")}}',
                                    cancelButtonText: '{{translate("No")}}',
                                    denyButtonText: `{{translate("Don't Logout")}}`,
                                    }).then((result) => {
                                    if (result.value) {
                                    location.href='{{route('admin.auth.logout')}}';
                                    } else{
                                    Swal.fire('{{ translate("Canceled")}}', '', 'info')
                                    }
                                    })">
                                    <span class="text-truncate pr-2" title="Sign out">{{\App\CentralLogics\translate('sign_out')}}</span>
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>
</div>
<div id="headerFluid" class="d-none"></div>
<div id="headerDouble" class="d-none"></div>
