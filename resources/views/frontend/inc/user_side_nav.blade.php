<!-- <div class="aiz-user-sidenav rounded overflow-auto c-scrollbar-light pb-5 pb-xl-0 usersidebar">
    <div class="p-4 text-center mb-4 border-bottom bg-primary text-white position-relative">
        <span class="avatar avatar-md mb-3">
            @if (Auth::user()->avatar_original != null)
                <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
            @else
                <img src="{{ static_asset('assets/img/avatar-place.png') }}" class="image rounded-circle" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/avatar-place.png') }}';">
            @endif
        </span>
        <h4 class="h5 fs-16 mb-1 fw-600">{{ Auth::user()->phone }}</h4>
    </div>

    <div class="sidemnenu mb-3">
        <ul class="aiz-side-nav-list px-2" data-toggle="aiz-side-menu">

            <li class="aiz-side-nav-item">
                <a href="{{ route('dashboard') }}" class="aiz-side-nav-link {{ areActiveRoutes(['dashboard'])}}">
                    <i class="las la-home aiz-side-nav-icon"></i>
                    <span class="aiz-side-nav-text">{{ translate('Dashboard') }}</span>
                </a>
            </li>

            @if(Auth::user()->user_type == 'delivery_boy')
                <li class="aiz-side-nav-item">
                    <a href="{{ route('assigned-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['completed-delivery'])}}">
                        <i class="las la-hourglass-half aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">
                            {{ translate('Assigned Delivery') }}
                        </span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('pickup-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['completed-delivery'])}}">
                        <i class="las la-luggage-cart aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">
                            {{ translate('Pickup Delivery') }}
                        </span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('on-the-way-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['completed-delivery'])}}">
                        <i class="las la-running aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">
                            {{ translate('On The Way Delivery') }}
                        </span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('completed-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['completed-delivery'])}}">
                        <i class="las la-check-circle aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">
                            {{ translate('Completed Delivery') }}
                        </span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('pending-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['pending-delivery'])}}">
                        <i class="las la-clock aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">
                            {{ translate('Pending Delivery') }}
                        </span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('cancelled-deliveries') }}" class="aiz-side-nav-link {{ areActiveRoutes(['cancelled-delivery'])}}">
                        <i class="las la-times-circle aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">
                            {{ translate('Cancelled Delivery') }}
                        </span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('cancel-request-list') }}" class="aiz-side-nav-link {{ areActiveRoutes(['cancel-request-list'])}}">
                        <i class="las la-times-circle aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">
                            {{ translate('Request Cancelled Delivery') }}
                        </span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('total-collection') }}" class="aiz-side-nav-link {{ areActiveRoutes(['today-collection'])}}">
                        <i class="las la-comment-dollar aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">
                            {{ translate('Total Collections') }}
                        </span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('total-earnings') }}" class="aiz-side-nav-link {{ areActiveRoutes(['total-earnings'])}}">
                        <i class="las la-comment-dollar aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">
                            {{ translate('Total Earnings') }}
                        </span>
                    </a>
                </li>
            @else

                @php
                    $delivery_viewed = App\Models\Order::where('user_id', Auth::user()->id)->where('delivery_viewed', 0)->get()->count();
                    $payment_status_viewed = App\Models\Order::where('user_id', Auth::user()->id)->where('payment_status_viewed', 0)->get()->count();
                @endphp
                <li class="aiz-side-nav-item">
                    <a href="{{ route('purchase_history.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['purchase_history.index'])}}">
                        <i class="las la-file-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Purchase History') }}</span>
                        @if($delivery_viewed > 0 || $payment_status_viewed > 0)<span class="badge badge-inline badge-success">{{ translate('New') }}</span>@endif
                    </a>
                </li>


                @if (addon_is_activated('refund_request'))
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('customer_refund_request') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_refund_request'])}}">
                            <i class="las la-backward aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Sent Refund Request') }}</span>
                        </a>
                    </li>
                @endif



                @if(get_setting('classified_product') == 1)
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('customer_products.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['customer_products.index', 'customer_products.create', 'customer_products.edit'])}}">
                            <i class="lab la-sketch aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Classified Products') }}</span>
                        </a>
                    </li>
                @endif

                @if(addon_is_activated('auction'))
                    <li class="aiz-side-nav-item">
                        <a href="javascript:void(0);" class="aiz-side-nav-link">
                            <i class="las la-gavel aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Auction') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            @if (Auth::user()->user_type == 'seller' && get_setting('seller_auction_product') == 1)
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('auction_products.seller.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['auction_products.seller.index','auction_product_create.seller','auction_product_edit.seller','product_bids.seller'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('All Auction Products') }}</span>
                                    </a>
                                </li>
                                <li class="aiz-side-nav-item">
                                    <a href="{{ route('auction_products_orders.seller') }}" class="aiz-side-nav-link {{ areActiveRoutes(['auction_products_orders.seller'])}}">
                                        <span class="aiz-side-nav-text">{{ translate('Auction Product Orders') }}</span>
                                    </a>
                                </li>
                            @endif
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('auction_product_bids.index') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Bidded Products') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('auction_product.purchase_history') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Purchase History') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif


                @if (get_setting('conversation_system') == 1)
                    @php
                        $conversation = \App\Models\Conversation::where('sender_id', Auth::user()->id)->where('sender_viewed', 0)->get();
                    @endphp
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('conversations.index') }}" class="aiz-side-nav-link {{ areActiveRoutes(['conversations.index', 'conversations.show'])}}">
                            <i class="las la-comment aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Conversations') }}</span>
                            @if (count($conversation) > 0)
                                <span class="badge badge-success">({{ count($conversation) }})</span>
                            @endif
                        </a>
                    </li>
                @endif

            @endif
            <li class="aiz-side-nav-item">
                <a href="{{ route('profile') }}" class="aiz-side-nav-link {{ areActiveRoutes(['profile'])}}">
                    <i class="las la-map-marked aiz-side-nav-icon"></i>
                    <span class="aiz-side-nav-text">{{translate('Manage Address')}}</span>
                </a>
            </li>

        </ul>
    </div>

</div> -->
