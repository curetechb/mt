<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">

    <title>@yield('meta_title', get_setting('website_name') . ' | ' . get_setting('site_motto'))</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="description" content="@yield('meta_description', get_setting('meta_description'))" />
    <meta name="keywords" content="@yield('meta_keywords', get_setting('meta_keywords'))">

    @yield('meta')

    @if (!isset($detailedProduct) && !isset($customer_product) && !isset($shop) && !isset($page) && !isset($blog))
        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="{{ get_setting('meta_title') }}">
        <meta itemprop="description" content="{{ get_setting('meta_description') }}">
        <meta itemprop="image" content="{{ uploaded_asset(get_setting('meta_image')) }}">

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product">
        <meta name="twitter:site" content="@publisher_handle">
        <meta name="twitter:title" content="{{ get_setting('meta_title') }}">
        <meta name="twitter:description" content="{{ get_setting('meta_description') }}">
        <meta name="twitter:creator" content="@author_handle">
        <meta name="twitter:image" content="{{ uploaded_asset(get_setting('meta_image')) }}">

        <!-- Open Graph data -->
        <meta property="og:title" content="{{ get_setting('meta_title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ route('home') }}" />
        <meta property="og:image" content="{{ uploaded_asset(get_setting('meta_image')) }}" />
        <meta property="og:description" content="{{ get_setting('meta_description') }}" />
        <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
        <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
    @endif

    <!-- Favicon -->
    {{-- <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}"> --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ static_asset('fab/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ static_asset('fab/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ static_asset('fab/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ static_asset('fab/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ static_asset('fab/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"
        rel="stylesheet">

    <!-- CSS Files -->
    {{-- <link rel="stylesheet" href="{{ static_asset('assets/css/vendors.css') }}"> --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="{{ static_asset('assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/aiz-core.css') }}">
    <link rel="stylesheet" href="{{ static_asset('assets/css/custom-style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;1,300&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            font-weight: 400;
            background: ;
        }



        #map {
            width: 100%;
            height: 250px;
        }

        #edit_map {
            width: 100%;
            height: 250px;
        }

        .pac-container {
            z-index: 100000;
        }

    </style>

    @if (get_setting('google_analytics') == 1)
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ env('TRACKING_ID') }}"></script>

        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ env('TRACKING_ID') }}');
            gtag('config', 'AW-944738048');
        </script>
    @endif

    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window,document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1438687889961635');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1"
        src="https://www.facebook.com/tr?id=1438687889961635&ev=PageView
        &noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->

    @php
        echo get_setting('header_script');
    @endphp
    <!--<script type="text/javascript">
        window.$crisp = [];
        window.CRISP_WEBSITE_ID = "16f75fda-d047-4856-9cb6-275b7d586172";
        (function() {
            d = document;
            s = d.createElement("script");
            s.src = "https://client.crisp.chat/l.js";
            s.async = 1;
            d.getElementsByTagName("head")[0].appendChild(s);
        })();
    </script>-->
</head>


<body>

    @php
        $temp_user_id = Session()->get('temp_user_id');
        if(Auth::check()){
            $cart = \App\Models\Cart::where('user_id', Auth::user()->id)->get();
        }else{
            $cart = \App\Models\Cart::where('temp_user_id', $temp_user_id)->get();
        }

        $total = 0;
        foreach($cart as $key => $cartItem){
            $total = $total + ($cartItem->product->unit_price + $cartItem['tax']) * $cartItem['quantity'];
        }
    @endphp


    <div class="child text-center pt-2" role="button">
        <div>
            <i class="las la-shopping-bag la-2x"></i>
        </div>
        <p><span class="cart-count">{{ count($cart) }}</span> ITEMS</p>
        <h6 class="bg-white cart-total">{{ single_price($total) }}</h6>
    </div>


    <!-- aiz-main-wrapper -->
    <!-- <div class="w-100 text-center pt-3 py-1" style="background: #f84f31; color: #fff;">
        <p>Eid Mubarak. Greetings from Muslim Town. We wish you and your family all the happiness. Muslim Town service is close from <u>03 May, 2022</u> to <u>05 May, 2022</u> for Eid vacation.</p>
    </div>-->
    <div class="aiz-main-wrapper d-flex flex-column">

        <!-- Header -->
        @include('frontend.inc.nav')

        <div class="home-banner-area">
            <div>
                <div class="d-none d-md-block h-100 pr-0 catside" id="catside">
                    @include('frontend.partials.category_menu')
                </div>

                <div class="main-container">
                    @yield('content')
                </div>


                <div class="h-100 pr-0 cart-sidebar" id="cart-sidebar">
                    <div class="cart-close-btn d-none d-lg-flex" role="button">
                        <i class="las la-angle-right font-weight-bold"></i>
                    </div>
                    <div id="cart_items" class="h-100 position-relative">
                        @include('frontend.partials.cart')
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- @if (get_setting('show_cookies_agreement') == 'on')
        <div class="aiz-cookie-alert shadow-xl">
            <div class="p-3 bg-dark rounded">
                <div class="text-white mb-3">
                    @php
                        echo get_setting('cookies_agreement_text');
                    @endphp
                </div>
                <button class="btn btn-primary aiz-cookie-accept">
                    {{ translate('Ok. I Understood') }}
                </button>
            </div>
        </div>
    @endif --}}

    {{-- @if (get_setting('show_website_popup') == 'on')
        <div class="modal website-popup removable-session d-none" data-key="website-popup" data-value="removed">
            <div class="absolute-full bg-black opacity-60"></div>
            <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-md">
                <div class="modal-content position-relative border-0 rounded-0">
                    <div class="aiz-editor-data">
                        {!! get_setting('website_popup_content') !!}
                    </div>
                    @if (get_setting('show_subscribe_form') == 'on')
                        <div class="pb-5 pt-4 px-5">
                            <form class="" method="POST" action="{{ route('subscribers.store') }}">
                                @csrf
                                <div class="form-group mb-0">
                                    <input type="email" class="form-control" placeholder="{{ translate('Your Email Address') }}" name="email" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block mt-3">
                                    {{ translate('Subscribe Now') }}
                                </button>
                            </form>
                        </div>
                    @endif
                    <button class="absolute-top-right bg-white shadow-lg btn btn-circle btn-icon mr-n3 mt-n3 set-session" data-key="website-popup" data-value="removed" data-toggle="remove-parent" data-parent=".website-popup">
                        <i class="la la-close fs-20"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif --}}

    <p id="current_route" data-id="{{ request()->route()->getName() }}"></p>

    @include('frontend.partials.modal')

    <div class="modal fade" id="addToCart">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-zoom product-modal" id="modal-size"
            role="document">
            <div class="modal-content position-relative">
                <div class="c-preloader text-center p-3">
                    <i class="las la-spinner la-spin la-3x"></i>
                </div>
                <button type="button" class="close absolute-top-right btn-icon close z-1" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true" class="la-2x">&times;</span>
                </button>
                <div id="addToCart-modal-body">

                </div>
            </div>
        </div>
    </div>

    @yield('modal')

    <!-- SCRIPTS -->
    {{-- <script src="{{ static_asset('assets/js/vendors.js') }}"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ static_asset('assets/js/lazysizes.js') }}"></script>



    @if (get_setting('facebook_chat') == 1)
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                    xfbml: true,
                    version: 'v3.3'
                });
            };

            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s);
                js.id = id;
                js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));
        </script>
        <div id="fb-root"></div>
        <!-- Your customer chat code -->
        <div class="fb-customerchat" attribution=setup_tool page_id="{{ env('FACEBOOK_PAGE_ID') }}">
        </div>
    @endif

    <script>
        function notify(type = "dark", message = "") {
            $.notify({
                // options
                message: message,
            }, {
                // settings
                showProgressbar: true,
                delay: 2500,
                mouse_over: "pause",
                placement: {
                    from: "bottom",
                    align: "left",
                },
                animate: {
                    enter: "animated fadeInUp",
                    exit: "animated fadeOutDown",
                },
                type: type,
                template: '<div data-notify="container" class="aiz-notify alert alert-{0}" role="alert">' +
                    '<button type="button" aria-hidden="true" data-notify="dismiss" class="close"><i class="las la-times"></i></button>' +
                    '<span data-notify="message">{2}</span>' +
                    '<div class="progress" data-notify="progressbar">' +
                    '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                    "</div>" +
                    "</div>",
            });
        }


        @foreach (session('flash_notification', collect())->toArray() as $message)
            notify('{{ $message['level'] }}', '{{ $message['message'] }}');
        @endforeach
    </script>

    <script>


        $('#search').on('keyup', function() {
            search();
        });

        $('#search').on('focus', function() {
            search();
        });

        function showAddToCartModal(id) {

            if (!$('#modal-size').hasClass('modal-lg')) {
                $('#modal-size').addClass('modal-lg');
            }
            $('#addToCart-modal-body').html(null);
            $('#addToCart').modal();
            $('.c-preloader').show();

            $.ajax({
                type: "POST",
                url: '{{ route('cart.showCartModal') }}',
                data: {
                    _token: "{{ @csrf_token() }}",
                    id: id
                },
                success: function(data) {
                    $('.c-preloader').hide();
                    $('#addToCart-modal-body').html(data);
                    if ($('.img-zoom')[0]) {
                        $('.img-zoom').zoom({
                            magnify: 1.5
                        });
                        if ((('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator
                                .msMaxTouchPoints > 0))) {
                            $('.img-zoom').trigger('zoom.destroy');
                        }
                    }
                }
            });

        }

        function search() {
            var searchKey = $('#search').val();
            if (searchKey.length > 0) {
                $('body').addClass("typed-search-box-shown");

                $('.typed-search-box').removeClass('d-none');
                $('.search-preloader').removeClass('d-none');
                $.ajax({
                    type: "POST",
                    url: '{{ route('search.ajax') }}',
                    data: {
                        _token: "{{ @csrf_token() }}",
                        search: searchKey
                    },
                    success: function(data) {
                        if (data == '0') {
                            // $('.typed-search-box').addClass('d-none');
                            $('#search-content').html(null);
                            $('.typed-search-box .search-nothing').removeClass('d-none').html(
                                'Sorry, nothing found for <strong>"' + searchKey + '"</strong>');
                            $('.search-preloader').addClass('d-none');

                        } else {
                            $('.typed-search-box .search-nothing').addClass('d-none').html(null);
                            $('#search-content').html(data);
                            $('.search-preloader').addClass('d-none');
                        }
                    }
                });
            } else {
                $('.typed-search-box').addClass('d-none');
                $('body').removeClass("typed-search-box-shown");
            }
        }






    </script>

    @yield('script')

    @php
        echo get_setting('footer_script');
    @endphp

    {{-- <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="defd7d3b-1a36-41c3-933e-f3f837363e9f";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script> --}}
    <script>

        // Category Sidebar Toggle
        $(document).ready(function() {
            $(document).on("click", ".togglebar,.mtoggler",function() {

                $("#cart-sidebar").animate({
                    right: '-100%'
                });

                $(".bc3").css({display: 'block'});
                $(".bc2").removeClass("col-9");
                $(".bc2").addClass("col-6");

                var w = $("#catside");

                if (w.is(":visible")) {
                    $("#catside").attr('style', 'display: none !important');
                    $(".main-container").css({
                        "margin-left": "0px"
                    });
                } else {
                    $("#catside").attr('style', 'display: block !important');
                    if ($(window).width() >= 768) {
                        $(".main-container").css({
                            "margin-left": "250px"
                        });
                    }
                }
            });



            @if ((request("offer") && request('product')))
                $("#cart-sidebar").css({
                    right: '0px'
                });
            @endif

            $(document).on("click", ".cart-close-btn, .open-cart",function(e) {

                e.preventDefault();

                var w = $("#cart-sidebar");
                var right = w.css("right");

                if (right == "0px") {
                    $(".bc3").css({display: 'block'});
                    $(".bc2").removeClass("col-9");
                    $(".bc2").addClass("col-6");

                    $("#cart-sidebar").animate({
                        right: '-110%'
                    });
                } else {
                    $(".bc3").css({display: 'none'});
                    $(".bc2").removeClass("col-6");
                    $(".bc2").addClass("col-9");

                    $("#cart-sidebar").animate({
                        right: '0px'
                    });
                }
            });

            $(".child").on("click", function() {
                $("#cart-sidebar").animate({
                    right: '0px'
                });
            });


            // for checkout page
            if($("#current_route").data("id") == "checkout.shipping_info"){
                $(".cart-proceed").removeClass("d-xl-block");
                $(".cart-proceed").addClass("d-none");
            }

        });
    </script>



{{--  --}}
    <script>

        $(document).ready(function() {

            $(document).on("click", ".cartbtn", function() {

                // code for buy now button in modal
                if($(this).hasClass("buy-now-btn") && $(".modalcarttext").html() != "0 in Bag"){
                    $('#addToCart').modal('toggle');
                    $("#cart-sidebar").animate({
                        right: '0px'
                    });

                    $(".bc3").css({display: 'none'});
                    $(".bc2").removeClass("col-6");
                    $(".bc2").addClass("col-9");
                    return false;
                }



                let productId = $(this).parent().data("id");
                let productDiv = $(`.product_${productId}`);
                let thisobj = $(this);
                let data_id = $(this).data("id");

                // if modal open and bag amount 0 then dont update qty
                if($(this).siblings(".modalcarttext").html() == "0 in Bag" && data_id == "minus"){
                    return false;
                }

                if(parseInt($(this).siblings(".input-number").val()) == 1 && data_id == "minus"){
                    $(this).attr("disabled", true);
                    return false;
                }

                let code = $("#coupon_code").val() ?? "";

                $.ajax({
                    type: "POST",
                    url: "{{ route('cart.addToCart') }}?coupon_code="+code,
                    data: {
                        product_id: productId,
                        _token: "{{ csrf_token() }}",
                        data_id: data_id
                    },
                    success: function(data) {

                        // for buy now button open cart sidebar
                        if(thisobj.hasClass("buy-now-btn")){
                            $('#addToCart').modal('toggle');
                            $("#cart-sidebar").animate({
                                right: '0px'
                            });
                        }

                        // show any validation error in modal
                        if (data.modal_view) {
                            $('.c-preloader').hide();
                            $('#addToCart').modal();
                            $('#addToCart-modal-body').html(data.modal_view);
                        }

                        // modal cart quantity show
                        productDiv.find(".modalcarttext").html(`${data.cart_item_count} in Bag`);

                        // if item added then visible + and - button else remove
                        if(parseInt(data.cart_item_count) > 0){
                            productDiv.find(".minusfrombag").addClass("d-block");
                            productDiv.find(".addtobag").addClass("d-block");
                            // increase qty after api call
                            productDiv.find(".bagtext").html(`${data.cart_item_count} in Bag`);
                            productDiv.addClass("pbtn");


                        }else{
                            productDiv.find(".minusfrombag").removeClass("d-block");
                            productDiv.find(".addtobag").removeClass("d-block");

                            productDiv.find(".minusfrombag").addClass("d-none");
                            productDiv.find(".addtobag").addClass("d-none");

                            productDiv.find(".bagtext").html(`Add to Bag`);
                            productDiv.removeClass("pbtn");

                        }

                        // for mobile bottom bar menu
                        if(parseInt(data.cart_count) > 0){
                            $(".mplaceorder").removeClass("mtoggler");
                            $(".mplaceorder").attr("href", "{{ route("checkout.shipping_info") }}");
                            $(".mplaceorder").find("span").html("Place Order");

                            // $(".checkout-form").addClass("d-block");
                            // $(".checkout-cart-empty").addClass("d-none");
                        }else{
                            $(".mplaceorder").addClass("mtoggler");
                            $(".mplaceorder").attr("href", "#");
                            $(".mplaceorder").find("span").html("Start Shopping");

                            // $(".checkout-form").addClass("d-none");
                            // $(".checkout-cart-empty").addClass("d-block");
                        }

                        // update cart details
                        $('.cart-count').html(data.cart_count);
                        $('.cart-total').html(data.cart_total);
                        $(".checkout-cart-total").html(data.grand_total);
                        $('#cart_items').html(data.nav_cart_view);

                        if(data.coupon_discount != '৳0.00'){
                            $("#coupon_error").html("");
                            $("#coupon_success").html("Discount "+data.coupon_discount+" applied");
                            $(".checkout-cart-total").html(data.grand_total);
                            $("#coupon-remove-btn").show();
                            $("#apply-coupon-btn").hide();

                            $("#coupon_code").attr("readonly", "readonly");
                        }else{
                            // coupon code remove
                            $("#coupon-remove-btn").hide();
                            $("#apply-coupon-btn").show();
                            $("#coupon_code").removeAttr("readonly");
                            $("#coupon_code").val("");
                            $("#coupon_error").html("");
                            $("#coupon_success").html("");
                        }



                        // for checkout page
                        if($("#current_route").data("id") == "checkout.shipping_info"){
                            $(".cart-proceed").removeClass("d-xl-block");
                            $(".cart-proceed").addClass("d-none");
                        }
                    }
                });

            });

            $(document).on("click", ".cart-remove-btn",function(){

                let productId = $(this).parent().data("id");
                let productDiv = $(`.product_${productId}`);

                let cartId = $(this).data("id");

                let code = $("#coupon_code").val() ?? "";

                $.ajax({
                    type: "POST",
                    url: '{{ route('cart.removeFromCart') }}?coupon_code='+code,
                    data: {
                        _token: "{{ @csrf_token() }}",
                        id: cartId
                    },
                    success: function(data) {

                        // if all data removed update mobile bottom bar
                        if(parseInt(data.cart_count) <= 0){
                            $(".mplaceorder").addClass("mtoggler");
                            $(".mplaceorder").attr("href", "#");
                            $(".mplaceorder").find("span").html("Start Shopping");

                            $(".checkout-form").removeClass("d-block");
                            $(".checkout-form").addClass("d-none");

                            $(".checkout-cart-empty").addClass("d-block");
                            $(".checkout-cart-empty").removeClass("d-none");

                        }

                        productDiv.find(".minusfrombag").removeClass("d-block");
                        productDiv.find(".addtobag").removeClass("d-block");

                        productDiv.find(".minusfrombag").addClass("d-none");
                        productDiv.find(".addtobag").addClass("d-none");

                        productDiv.find(".bagtext").html(`Add to Bag`);
                        productDiv.removeClass("pbtn");

                        $('.cart-count').html(data.cart_count);
                        $('.cart-total').html(data.cart_total);
                        $(".checkout-cart-total").html(data.grand_total);
                        $('#cart_items').html(data.nav_cart_view);

                        // for checkout page
                        if($("#current_route").data("id") == "checkout.shipping_info"){
                            $(".cart-proceed").removeClass("d-xl-block");
                            $(".cart-proceed").addClass("d-none");

                            $(".shipping_cost").html(data.shipping_cost);
                        }

                        if(data.coupon_discount != '৳0.00'){

                            $("#coupon_error").html("");
                            $("#coupon_success").html("Discount "+data.coupon_discount+" applied");
                            $(".checkout-cart-total").html(data.grand_total);
                            $("#coupon-remove-btn").show();
                            $("#apply-coupon-btn").hide();

                            $("#coupon_code").attr("readonly", "readonly");

                        }else{
                            // coupon code remove
                            $("#coupon-remove-btn").hide();
                            $("#apply-coupon-btn").show();
                            $("#coupon_code").removeAttr("readonly");
                            $("#coupon_code").val("");
                            $("#coupon_error").html("");
                            $("#coupon_success").html("");
                        }

                    }
                });

            });


            $(document).on("click", ".givemailaddress", function() {

                $(this).siblings('div:first').addClass("d-inline-block");

            });

            $(document).on("submit", ".givemailform", function(e) {

                e.preventDefault();
                const email = $(this).find("#invoicemail").val();
                const formData = new FormData();
                formData.append("email", email);

                $('.givemailform').find(".invbtn").attr("disabled", true);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    method: "POST",
                    url: "{{ route('invoice.mail') }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data, textStatus, jqXHR) {
                        if (data.success) {
                            $(".givemailform").parent().removeClass("d-inline-block");
                            $(".givemailform").parent().addClass("d-none");
                            $(".givemailaddress").html("Invoice is Sent to your Email Address");
                            $(".givemailaddress").removeClass("givemailaddress");
                        }
                    }
                })

            });

            $(document).on('click', '[data-toggle="class-toggle"]', function() {
                var $this = $(this);
                var target = $this.data("target");
                var sameTriggers = $this.data("same");
                var backdrop = $(this).data("backdrop");

                if ($(target).hasClass("active")) {
                    $(target).removeClass("active");
                    $(sameTriggers).removeClass("active");
                    $this.removeClass("active");
                    $('body').removeClass("overflow-hidden");
                } else {
                    $(target).addClass("active");
                    $this.addClass("active");
                    if (backdrop == 'static') {
                        $('body').addClass("overflow-hidden");
                    }
                }
            });

        });
    </script>

</body>

</html>
