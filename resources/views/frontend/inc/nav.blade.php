@if(get_setting('topbar_banner') != null)
<div class="position-relative top-banner removable-session z-1035 d-none" data-key="top-banner" data-value="removed">
    <a href="{{ get_setting('topbar_banner_link') }}" class="d-block text-reset">
        <img src="{{ uploaded_asset(get_setting('topbar_banner')) }}" class="w-100 mw-100 h-50px h-lg-auto img-fit">
    </a>
    <button class="btn text-white absolute-top-right set-session" data-key="top-banner" data-value="removed" data-toggle="remove-parent" data-parent=".top-banner">
        <i class="la la-close la-2x"></i>
    </button>
</div>
@endif

<header class="@if(get_setting('header_stikcy') == 'on')sticky-top @endif z-1020 bg-tb border-bottom shadow-sm">
    <div class="position-relative logo-bar-area z-1">
        <div class="container-fluid">
            <div class="d-flex align-items-center">

                <div class="pl-0 d-flex align-items-center" style="width: 250px">
                    <i class="las la-bars togglebar" role="button"></i>
                    <a class="d-block py-10px mr-3 ml-0" href="{{ route('home') }}">
                        @php
                        $header_logo = get_setting('header_logo');
                        @endphp
                        @if($header_logo != null)
                        <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-30px h-md-40px" height="40">
                        @else
                        <img src="{{ static_asset('assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}" class="mw-100 h-30px h-md-40px" height="40">
                        @endif
                    </a>

                </div>
                <div class="d-lg-none ml-auto text-white mr-0">
                    <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle" data-target=".front-header-search">
                        <i class="las la-search la-flip-horizontal la-2x"></i>
                    </a>
                </div>

                <div class="flex-grow-1 front-header-search d-flex align-items-center bg-white">
                    <div class="position-relative flex-grow-1">
                        <form action="{{ request()->routeIs('home.marketing') ? route('home.search') : route('search') }}" method="GET" class="stop-propagation search-form">
                            <div class="d-flex position-relative align-items-center">
                                <div class="d-lg-none" data-toggle="class-toggle" data-target=".front-header-search">
                                    <button class="btn px-2" type="button"><i class="la la-2x la-long-arrow-left"></i></button>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="border-0 border-lg form-control" id="search" name="keyword" @isset($query) value="{{ $query }}" @endisset placeholder="{{translate('I am shopping for...')}}" autocomplete="off">
                                    <div class="input-group-append d-none d-lg-block">
                                        <button class="btn btn-light" type="submit">
                                            <i class="las la-search la-flip-horizontal fs-22"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100" style="min-height: 200px">
                            <div class="search-preloader absolute-top-center">
                                <div class="dot-loader"></div>
                            </div>
                            <div class="search-nothing d-none p-3 text-center fs-16">

                            </div>
                            <div id="search-content" class="text-left">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-none d-lg-none ml-3 mr-0">
                    <div class="nav-search-box">
                        <a href="#" class="nav-box-link">
                            <i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i>
                        </a>
                    </div>
                </div>

                {{-- <div class="d-none d-lg-block align-self-stretch ml-3 mr-0" data-hover="dropdown">
                    <div class="nav-cart-box text-white dropdown h-100" id="cart_items">
                        @include('frontend.partials.cart')
                    </div>
                </div> --}}

                @auth
                    <div class="align-self-stretch ml-3 mr-0" data-hover="dropdown">
                        <div class="nav-cart-box text-white dropdown h-100">
                            <a href="javascript:void(0)" class="d-flex align-items-center text-reset h-100" data-toggle="dropdown" data-display="static">
                                <i class="las la-user la-2x opacity-80"></i>
                                <span class="flex-grow-1 ml-1 cart-counter-box">
                                    <span class="badge badge-light badge-inline badge-pill fw-600">{{ Auth::user()->orders()->where('delivery_status', 'pending')->count() }}</span>
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-sm p-0 stop-propagation">

                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="{{ route('purchase_history.index') }}">Purchase History</a></li>
                                    <!-- <li><a class="dropdown-item" href="{{ route('customer_refund_request') }}">Sent Refund Request</a></li> -->
                                    {{-- @if (get_setting('reward_activation') == 1)
                                        <li><a class="dropdown-item" href="{{ route('reward_histories') }}">Reward Histories</a></li>
                                    @endif --}}
                                    <li><a class="dropdown-item" href="{{ route('profile') }}">Manage Address</a></li>

                            </div>

                        </div>
                    </div>
                @endauth

            </div>
        </div>

    </div>

</header>

<div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div id="order-details-modal-body">

            </div>
        </div>
    </div>
</div>
