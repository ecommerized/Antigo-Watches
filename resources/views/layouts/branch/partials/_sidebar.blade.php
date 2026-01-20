<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset">
                <div class="d-flex align-items-center gap-3 py-2 px-3 justify-content-between">

                    @php($logo = Helpers::get_business_settings('logo'))
                    <a class="navbar-brand w-75" href="{{route('branch.dashboard')}}" aria-label="Front">
                        <img class="navbar-brand-logo"
                             src="{{Helpers::onErrorImage(
                            $logo,
                            asset('storage/app/public/ecommerce').'/' . $logo,
                            asset('public/assets/admin/img/160x160/img2.jpg') ,
                            'ecommerce/')}}"
                             alt="{{ translate('Logo') }}">
                    </a>
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mt-1">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align"
                            title="Expand"></i>
                    </button>
                </div>

                <div class="navbar-vertical-content text-capitalize">
                    <div class="sidebar--search-form py-3">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input" placeholder="{{ translate('Search Menu...') }}">
                        </div>
                    </div>

                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch')?'show':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                               href="{{route('branch.dashboard')}}" title="Dashboards">
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
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/pos*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('POS')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub {{Request::is('branch/pos*')?'d-block':'d--none'}}">
                                <li class="nav-item {{Request::is('branch/pos')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.pos.index')}}"
                                       title="{{translate('POS')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{translate('POS')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/pos/orders')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.pos.orders')}}" title="{{translate('orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('orders')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::where('branch_id', auth('branch')->id())->Pos()->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle" title="Pages">{{translate('order')}} {{translate('management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/orders*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                               title="orders">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('order')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub {{Request::is('branch/orders*')?'d-block':'d--none'}}">
                                <li class="nav-item {{Request::is('branch/orders/list/all')?'active':''}}">
                                    <a class="nav-link" href="{{route('branch.orders.list',['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('all')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->where(['branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/pending')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'pending','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/confirmed')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('confirmed')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'confirmed','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/processing')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['processing'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('processing')}}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'processing','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/out_for_delivery')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['out_for_delivery'])}}"
                                       title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('out_for_delivery')}}
                                                <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'out_for_delivery','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/delivered')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['delivered'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('delivered')}}
                                                <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->where(['order_status'=>'delivered','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/returned')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['returned'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('returned')}}
                                                <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'returned','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/failed')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['failed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('failed')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'failed','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('branch/orders/list/canceled')?'active':''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list',['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('canceled')}}
                                                <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status'=>'canceled','branch_id'=>auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
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

