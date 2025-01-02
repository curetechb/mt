@extends('frontend.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @if (get_setting('home_slider_images') != null)
                <div class="aiz-carousel dots-inside-bottom mobile-img-auto-height" data-arrows="true" data-dots="true" data-autoplay="true">
                    @php $slider_images = json_decode(get_setting('home_slider_images'), true);  @endphp
                    @foreach ($slider_images as $key => $value)
                        <div class="carousel-box">
                            <img class="d-block mw-100 img-fit rounded shadow-sm overflow-hidden" src="{{ uploaded_asset($slider_images[$key]) }}" alt="{{ env('APP_NAME') }} promo" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

<!--<div class="slider-image">-->
<!--    <img src="{{ static_asset('assets/img/slider.png') }}" alt="" class="img-fluid" width="100%"/>-->
<!--</div>-->


<div id="taja_category">
    <section class="mb-4">
        <div class="container-fluid">
            <div class="px-md-4 py-md-3 bg-white rounded">
            <h2 class="text-center my-4" style="color: #555">Our Product Categories</h2>
            @if (count($categories) > 0)
            <div class="row gutters-5">
                @foreach ($categories as $key => $category)
                <div class="col-md-4 mb-2">
                    <a href="{{ route('products.category', $category->slug) }}" class="taja-cat-box">
                       <div class="flex-grow-1 text-center">{{ $category->name }}</div>
                       <img src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($category->icon) }}" alt="{{ $category->name }}" class="lazyload" width="20" height="20" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                    </a>
                </div>
                @endforeach
            </div>
            @endif
            </div>
        </div>
    </section>
</div>



<!-- Footer -->
@include('frontend.inc.footer')

@endsection



@section("script")
<script src="{{ static_asset('assets/js/slick.js') }}"></script>
<script>
    $(".aiz-carousel").not(".slick-initialized").each(function () {
                var $this = $(this);


                var slidesPerViewXs = $this.data("xs-items");
                var slidesPerViewSm = $this.data("sm-items");
                var slidesPerViewMd = $this.data("md-items");
                var slidesPerViewLg = $this.data("lg-items");
                var slidesPerViewXl = $this.data("xl-items");
                var slidesPerView = $this.data("items");

                var slidesCenterMode = $this.data("center");
                var slidesArrows = $this.data("arrows");
                var slidesDots = $this.data("dots");
                var slidesRows = $this.data("rows");
                var slidesAutoplay = $this.data("autoplay");
                var slidesFade = $this.data("fade");
                var asNavFor = $this.data("nav-for");
                var infinite = $this.data("infinite");
                var focusOnSelect = $this.data("focus-select");
                var adaptiveHeight = $this.data("auto-height");


                var vertical = $this.data("vertical");
                var verticalXs = $this.data("vertical-xs");
                var verticalSm = $this.data("vertical-sm");
                var verticalMd = $this.data("vertical-md");
                var verticalLg = $this.data("vertical-lg");
                var verticalXl = $this.data("vertical-xl");

                slidesPerView = !slidesPerView ? 1 : slidesPerView;
                slidesPerViewXl = !slidesPerViewXl ? slidesPerView : slidesPerViewXl;
                slidesPerViewLg = !slidesPerViewLg ? slidesPerViewXl : slidesPerViewLg;
                slidesPerViewMd = !slidesPerViewMd ? slidesPerViewLg : slidesPerViewMd;
                slidesPerViewSm = !slidesPerViewSm ? slidesPerViewMd : slidesPerViewSm;
                slidesPerViewXs = !slidesPerViewXs ? slidesPerViewSm : slidesPerViewXs;


                vertical = !vertical ? false : vertical;
                verticalXl = (typeof verticalXl == 'undefined') ? vertical : verticalXl;
                verticalLg = (typeof verticalLg == 'undefined') ? verticalXl : verticalLg;
                verticalMd = (typeof verticalMd == 'undefined') ? verticalLg : verticalMd;
                verticalSm = (typeof verticalSm == 'undefined') ? verticalMd : verticalSm;
                verticalXs = (typeof verticalXs == 'undefined') ? verticalSm : verticalXs;


                slidesCenterMode = !slidesCenterMode ? false : slidesCenterMode;
                slidesArrows = !slidesArrows ? false : slidesArrows;
                slidesDots = !slidesDots ? false : slidesDots;
                slidesRows = !slidesRows ? 1 : slidesRows;
                slidesAutoplay = !slidesAutoplay ? false : slidesAutoplay;
                slidesFade = !slidesFade ? false : slidesFade;
                asNavFor = !asNavFor ? null : asNavFor;
                infinite = !infinite ? false : infinite;
                focusOnSelect = !focusOnSelect ? false : focusOnSelect;
                adaptiveHeight = !adaptiveHeight ? false : adaptiveHeight;


                var slidesRtl = ($("html").attr("dir") === "rtl" && !vertical) ? true : false;
                var slidesRtlXL = ($("html").attr("dir") === "rtl" && !verticalXl) ? true : false;
                var slidesRtlLg = ($("html").attr("dir") === "rtl" && !verticalLg) ? true : false;
                var slidesRtlMd = ($("html").attr("dir") === "rtl" && !verticalMd) ? true : false;
                var slidesRtlSm = ($("html").attr("dir") === "rtl" && !verticalSm) ? true : false;
                var slidesRtlXs = ($("html").attr("dir") === "rtl" && !verticalXs) ? true : false;

                $this.slick({
                    slidesToShow: slidesPerView,
                    autoplay: slidesAutoplay,
                    dots: slidesDots,
                    arrows: slidesArrows,
                    infinite: infinite,
                    vertical: vertical,
                    rtl: slidesRtl,
                    rows: slidesRows,
                    centerPadding: "0px",
                    centerMode: slidesCenterMode,
                    fade: slidesFade,
                    asNavFor: asNavFor,
                    focusOnSelect: focusOnSelect,
                    adaptiveHeight: adaptiveHeight,
                    slidesToScroll: 1,
                    prevArrow:
                        '<button type="button" class="slick-prev"><i class="las la-angle-left"></i></button>',
                    nextArrow:
                        '<button type="button" class="slick-next"><i class="las la-angle-right"></i></button>',
                    responsive: [
                        {
                            breakpoint: 1500,
                            settings: {
                                slidesToShow: slidesPerViewXl,
                                vertical: verticalXl,
                                rtl: slidesRtlXL,
                            },
                        },
                        {
                            breakpoint: 1200,
                            settings: {
                                slidesToShow: slidesPerViewLg,
                                vertical: verticalLg,
                                rtl: slidesRtlLg,
                            },
                        },
                        {
                            breakpoint: 992,
                            settings: {
                                slidesToShow: slidesPerViewMd,
                                vertical: verticalMd,
                                rtl: slidesRtlMd,
                            },
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: slidesPerViewSm,
                                vertical: verticalSm,
                                rtl: slidesRtlSm,
                            },
                        },
                        {
                            breakpoint: 576,
                            settings: {
                                slidesToShow: slidesPerViewXs,
                                vertical: verticalXs,
                                rtl: slidesRtlXs,
                            },
                        },
                    ],
                });
            });
</script>

@endsection
