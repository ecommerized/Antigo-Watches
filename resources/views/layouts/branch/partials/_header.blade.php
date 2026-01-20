<div id="headerMain" class="d-none">
    <header id="header"
            class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
        <div class="navbar-nav-wrap">
            <div class="navbar-brand-wrapper">
                @php($logo = Helpers::get_business_settings('logo'))
                <a class="navbar-brand" href="{{route('branch.dashboard')}}" aria-label="">
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
                               href="{{route('branch.orders.list',['status'=>'all'])}}">
                                <i class="tio-shopping-cart-outlined"></i>
                                <span class="btn-status btn-status-danger">
                                    {{ DB::table('orders')->where(['branch_id' => auth('branch')->id(), 'checked' => 0])->count() }}
                                </span>
                            </a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <div class="hs-unfold">
                            <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper media align-items-center gap-3 bg-transparent dropdown-toggle dropdown-toggle-left-arrow" href="javascript:;"
                               data-hs-unfold-options='{
                                     "target": "#accountNavbarDropdown",
                                     "type": "css-animation"
                                   }'>

                                <div class="d-none d-md-block media-body text-right">
                                    <h5 class="profile-name text-capitalize mb-0">{{auth('branch')->user()->name}}</h5>
                                    <span class="fs-12 text-capitalize">{{ translate('Branch Admin') }}</span>
                                </div>
                                <div class="avatar avatar-sm avatar-circle">
                                    <img class="avatar-img"
                                         src="{{auth('branch')->user()->image_fullpath}}"
                                         alt="{{ translate('image') }}">
                                    <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                                </div>
                            </a>

                            <div id="accountNavbarDropdown"
                                 class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account">
                                <div class="dropdown-item-text">
                                    <div class="media gap-3 align-items-center">
                                        <div class="avatar avatar-sm avatar-circle mr-2">
                                            <img class="avatar-img"
                                                 src="{{auth('branch')->user()->image_fullpath}}"
                                                 alt="{{ translate('Image') }}">
                                        </div>
                                        <div class="media-body">
                                            <span class="card-title h5">{{auth('branch')->user()->name}}</span>
                                            <span class="card-text">{{auth('branch')->user()->email}}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="{{route('branch.settings')}}">
                                    <span class="text-truncate pr-2" title="Settings">{{translate('settings')}}</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="javascript:" onclick="Swal.fire({
                                    title:'{{translate("Do you want to logout?")}}',
                                    showDenyButton: true,
                                    showCancelButton: true,
                                    confirmButtonColor: '#673ab7',
                                    cancelButtonColor: '#363636',
                                    confirmButtonText: `{{translate("Yes")}}`,
                                    cancelButtonText: `{{translate("No")}}`,
                                    denyButtonText: `Don't Logout`,
                                    }).then((result) => {
                                    if (result.value) {
                                    location.href='{{route('branch.auth.logout')}}';
                                    } else{
                                    Swal.fire('{{translate("Canceled")}}', '', 'info')
                                    }
                                    })">
                                    <span class="text-truncate pr-2" title="Sign out">{{translate('sign_out')}}</span>
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
