<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="d-flex align-items-center gap-3 py-2 px-3 justify-content-between">
                    @php($logo = Helpers::get_business_settings('logo'))
                    <a class="navbar-brand w-75" href="{{route('admin.dashboard')}}" aria-label="Front">
                        <img class="navbar-brand-logo"
                             alt="{{ translate('logo') }}"
                             src="{{Helpers::onErrorImage(
                            $logo,
                            asset('storage/app/public/ecommerce').'/' . $logo,
                            asset('public/assets/admin/img/160x160/img2.jpg') ,
                            'ecommerce/')}}"

                        >
                        <img class="navbar-brand-logo-mini"
                             alt="{{ translate('logo') }}"
                             src="{{Helpers::onErrorImage(
                            $logo,
                            asset('storage/app/public/ecommerce').'/' . $logo,
                            asset('public/assets/admin/img/160x160/img2.jpg') ,
                            'ecommerce/')}}"
                        >
                    </a>

                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mt-1">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                           title="Expand"></i>
                    </button>
                </div>

                <div class="navbar-vertical-content">
                    <div class="sidebar--search-form py-3">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input"
                                   placeholder="Search Menu...">
                        </div>
                    </div>

                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.dashboard')}}" title="{{translate('Dashboards')}}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('dashboard')}}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('pos')}} {{translate('management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/pos*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('POS')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/pos*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/pos')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.pos.index')}}"
                                       title="{{translate('POS')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{translate('POS')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/pos/orders')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.pos.orders')}}"
                                       title="{{translate('orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('orders')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::Pos()->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('order')}} {{translate('management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/orders*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('order')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/order*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/orders/list/all')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.orders.list',['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('all')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/pending')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'pending'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/confirmed')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('confirmed')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'confirmed'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/processing')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['processing'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('processing')}}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'processing'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/out_for_delivery')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['out_for_delivery'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('out_for_delivery')}}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'out_for_delivery'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/delivered')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['delivered'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('delivered')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->where(['order_status'=>'delivered'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/returned')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['returned'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('returned')}}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'returned'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/failed')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['failed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('failed')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'failed'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/orders/list/canceled')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.orders.list',['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('canceled')}}
                                                <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'canceled'])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('product')}} {{translate('management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/category*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                            >
                                <i class="tio-category nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('category')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/category*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/category/add')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.category.add')}}"
                                       title="{{translate('add new category')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('category')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/category/add-sub-category')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.category.add-sub-category')}}"
                                       title="{{translate('add new sub category')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('sub_category')}}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/attribute*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.attribute.add-new')}}"
                            >
                                <i class="tio-apps nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('attribute')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/product*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                            >
                                <i class="tio-premium-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('product')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/product*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/product/add-new')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.product.add-new')}}"
                                       title="{{translate('add new product')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{translate('add')}} {{translate('new')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/product/list')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.product.list')}}"
                                       title="{{translate('product list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('list')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/product/bulk-import')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.product.bulk-import')}}"
                                       title="{{translate('bulk import')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('bulk_import')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/product/bulk-export')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.product.bulk-export')}}"
                                       title="{{translate('bulk export')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('bulk_export')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('Promotion Management')}}">{{translate('Promotion Management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/banner*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.banner.add-new')}}">
                                <i class="tio-image nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('banner')}}</span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/flash-sale*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.flash-sale.index')}}">
                                <i class="tio-alarm-alert nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Flash Sale')}}</span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/coupon*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.coupon.add-new')}}"
                            >
                                <i class="tio-gift nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('coupon')}}</span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/notification*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.notification.add-new')}}"
                            >
                                <i class="tio-notifications nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('send')}} {{translate('notification')}}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('Support & Help Section')}}">{{translate('Support & Help Section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/message*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.message.list')}}"
                            >
                                <i class="tio-messages nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages')}}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('report_and_analytics')}}">{{translate('report_and_analytics')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/earning')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.report.earning')}}">
                                <i class="tio-chart-pie-1 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('earning_report')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/order')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.report.order')}}">
                                <i class="tio-chart-bar-2 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('order_report')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/driver-report')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.report.driver-report')}}">
                                <i class="tio-chart-pie-2 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('deliveryman_report')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/product-report')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.report.product-report')}}">
                                <i class="tio-chart-bar-1 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('product_report')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/sale-report')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.report.sale-report')}}">
                                <i class="tio-chart-bar-4 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('sale_report')}}
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/wallet-transaction-history')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.report.wallet-transaction-history')}}">
                                <i class="tio-wallet nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('wallet_transaction_history')}}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('User Management')}}">{{translate('user_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer/list') || Request::is('admin/customer/view*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.customer.list')}}"
                            >
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('customer')}} {{translate('list')}}
                                </span>
                            </a>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer/subscribed-email*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.customer.subscribed_emails')}}">
                                <i class="tio-email-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('Subscribed Emails')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/reviews*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.reviews.list')}}"
                            >
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('product')}} {{translate('reviews')}}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/delivery-man*') ?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-user-big-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('deliveryman')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/delivery-man*') ?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/delivery-man/add')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.delivery-man.add')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('register')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/delivery-man/list')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.delivery-man.list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('list')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item  {{Request::is('admin/delivery-man/pending/list') || Request::is('admin/delivery-man/denied/list')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.delivery-man.pending')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('New Joining Request')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item  {{Request::is('admin/delivery-man/reviews/list')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.delivery-man.reviews.list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('reviews')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                   title="{{translate('Business Section')}}">{{translate('system_settings')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/ecom-setup') || Request::is('admin/business-settings/otp-setup') || Request::is('admin/business-settings/cookies-setup') || Request::is('admin/business-settings/delivery-fee-setup') || Request::is('admin/business-settings/customer-setup')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.business-settings.ecom-setup')}}"
                            >
                                <i class="tio-settings nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('business_Setup')}}</span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/branch*') ?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shop nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('branch_setup')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/branch*') ?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/branch/add-new')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.branch.add-new')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('add_new')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/branch/list')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.branch.list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('list')}}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/sms-module') || Request::is('admin/business-settings/mail-config') || Request::is('admin/business-settings/payment-method') || Request::is('admin/business-settings/recaptcha')  ||
                                Request::is('admin/business-settings/map-api-settings') || Request::is('admin/business-settings/fcm-index') || Request::is('admin/business-settings/firebase-message-config') ||
                                Request::is('admin/business-settings/social-media-login') ||  Request::is('admin/business-settings/social-media-chat') ||  Request::is('admin/business-settings/firebase-auth') ?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.business-settings.sms-module')}}"
                            >
                                <span class="tio-settings-outlined nav-icon"></span>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('3rd_Party')}}</span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/social-media') || Request::is('admin/business-settings/return-page') || Request::is('admin/business-settings/about-us') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/terms-and-conditions') ||
                            Request::is('admin/business-settings/cancellation-page') || Request::is('admin/business-settings/refund-page') ?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-pages nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Pages_&_Media')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/business-settings/social-media') || Request::is('admin/business-settings/return-page') || Request::is('admin/business-settings/about-us') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/terms-and-conditions') ||
                                    Request::is('admin/business-settings/cancellation-page') || Request::is('admin/business-settings/refund-page')?'block':'none'}}">

                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/return-page') || Request::is('admin/business-settings/about-us') || Request::is('admin/business-settings/privacy-policy') || Request::is('admin/business-settings/terms-and-conditions') ||
                                     Request::is('admin/business-settings/cancellation-page') || Request::is('admin/business-settings/refund-page')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.business-settings.about-us')}}"
                                       title="{{translate('pages')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('pages')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/business-settings/social-media')?'active':''}}">
                                    <a class="nav-link "
                                       href="{{route('admin.business-settings.social-media')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Social Media')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/app-setting') || Request::is('admin/business-settings/db-index') || Request::is('admin/business-settings/location-setup')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.business-settings.app_setting')}}"
                            >
                                <i class="tio-labels nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('system_setup')}}</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('system')}} {{translate('addon')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/system-addon')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('admin.system-addon.index')}}" title="{{translate('System Addons')}}">
                                <i class="tio-add-circle-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                        {{translate('System Addons')}}
                                    </span>
                            </a>
                        </li>

                        @if(count(config('addon_admin_routes'))>0)
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/payment/configuration/*') || Request::is('admin/sms/configuration/*')?'active':''}} mb-5">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                   href="javascript:">
                                    <i class="tio-puzzle nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Addon Menus')}}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/payment/configuration/*') || Request::is('admin/sms/configuration/*')?'block':'none'}}">
                                    @foreach(config('addon_admin_routes') as $routes)
                                        @foreach($routes as $route)
                                            <li class="navbar-vertical-aside-has-menu {{Request::is($route['path'])  ? 'active' :''}}">
                                                <a class="js-navbar-vertical-aside-menu-link nav-link "
                                                   href="{{ $route['url'] }}" title="{{ translate($route['name']) }}">
                                                    <span class="tio-circle nav-indicator-icon"></span>
                                                    <span class="text-truncate">{{ translate($route['name']) }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </li>
                        @endif

                        <li class="nav-item p-top-100px">
                            <div class="nav-divider"></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>

@push('script_2')
    <script>
        "use script";
        // Active Item scrollTop show
        $(window).on('load', function () {
            if ($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        // Search
        var $items = $('.navbar-vertical-content .navbar-nav > li');
        $('#search-bar-input').on('keyup', function () {
            var keyword = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            console.log(keyword)

            $items.hide(); // hide all initially

            if (keyword === '') {
                $items.show(); // show all if input is empty
                return;
            }

            var showItems = $();

            $items.each(function (i, el) {
                var $el = $(el);
                var text = $el.text().replace(/\s+/g, ' ').toLowerCase();

                if ($el.hasClass('nav-item') && text.includes(keyword)) {
                    // Matched .nav-item — show it and everything until next .nav-item
                    showItems = showItems.add($el);
                    $el.nextAll().each(function () {
                        var $next = $(this);
                        if ($next.hasClass('nav-item')) {
                            return false; // stop at next nav-item
                        }
                        showItems = showItems.add($next);
                    });
                } else if (!$el.hasClass('nav-item') && text.includes(keyword)) {
                    // Matched non-nav-item — show just this
                    showItems = showItems.add($el);
                }
            });

            showItems.show();
        });
    </script>
@endpush



