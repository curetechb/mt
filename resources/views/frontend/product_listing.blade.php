@extends('frontend.layouts.app')

@php
    $category_slug = request()->route('category_slug');
    $cat = \App\Models\Category::where('slug', $category_slug)->first();
@endphp

@if ($category_slug)
    @php
        $meta_title = $cat->meta_title;
        $meta_description = $cat->meta_description;
    @endphp
@else
    @php
        $meta_title         = get_setting('meta_title');
        $meta_description   = get_setting('meta_description');
    @endphp
@endif

@section('meta_title'){{ $meta_title }}@stop
@section('meta_description'){{ $meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $meta_title }}">
    <meta itemprop="description" content="{{ $meta_description }}">

    <!-- Twitter Card data -->
    <meta name="twitter:title" content="{{ $meta_title }}">
    <meta name="twitter:description" content="{{ $meta_description }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $meta_title }}" />
    <meta property="og:description" content="{{ $meta_description }}" />
@endsection

@section('content')

    <section class="mb-4 pt-3">
        <div class="container sm-px-0">
            <form class="" id="search-form" action="" method="GET">
                <div class="container-fluid">
                    <div>

                        @if (request()->routeIs("products.category"))
                            <ul class="breadcrumb bg-transparent p-0">
                                <li class="breadcrumb-item opacity-50">
                                    <a class="text-reset" href="{{ route('home') }}">{{ translate('Home')}}</a>
                                </li>
                                {{-- @if(!isset($category_id))
                                    <li class="breadcrumb-item fw-600  text-dark">
                                        <a class="text-reset" href="{{ route('search') }}">"{{ translate('All Categories')}}"</a>
                                    </li>
                                @else
                                    <li class="breadcrumb-item opacity-50">
                                        <a class="text-reset" href="{{ route('search') }}">{{ translate('All Categories')}}</a>
                                    </li>
                                @endif --}}
                                @if(isset($cat))
                                    <li class="text-dark fw-600 breadcrumb-item">
                                        <a class="text-reset" href="{{ route('products.category', $cat->slug) }}"> {{ $cat->getTranslation('name') }} </a>
                                    </li>
                                @endif
                            </ul>
                        @endif

                        <div class="text-left">
                            <div class="row gutters-5 flex-wrap align-items-center">
                                <div class="col-lg col-10">
                                    <h1 class="h6 fw-600 text-body">
                                        {{-- @if(isset($category_id))
                                            {{ \App\Models\Category::find($category_id)->getTranslation('name') }}
                                        @elseif(isset($query))
                                            {{ translate('Search result for ') }}"{{ $query }}"
                                        @else
                                            {{ translate('All Products') }}
                                        @endif --}}
                                        @if(isset($cat))
                                            {{-- {{ $cat->getTranslation('name') }} --}}
                                        @else
                                            {{ translate('Search result for ') }} {{ $query }}
                                        @endif
                                    </h1>
                                    <input type="hidden" name="keyword" value="{{ $query }}">
                                </div>
                                <!--<div class="col-2 col-lg-auto d-xl-none mb-lg-3 text-right">-->
                                <!--    <button type="button" class="btn btn-icon p-0" data-toggle="class-toggle" data-target=".aiz-filter-sidebar">-->
                                <!--        <i class="la la-filter la-2x"></i>-->
                                <!--    </button>-->
                                <!--</div>-->
                                <!--<div class="col-6 col-lg-auto mb-3 w-lg-200px">-->
                                <!--    @if (Route::currentRouteName() != 'products.brand')-->
                                <!--        <label class="mb-0 opacity-50">{{ translate('Brands')}}</label>-->
                                <!--        <select class="form-control form-control-sm aiz-selectpicker" data-live-search="true" name="brand" onchange="filter()">-->
                                <!--            <option value="">{{ translate('All Brands')}}</option>-->
                                <!--            @foreach (\App\Models\Brand::all() as $brand)-->
                                <!--                <option value="{{ $brand->slug }}" @isset($brand_id) @if ($brand_id == $brand->id) selected @endif @endisset>{{ $brand->getTranslation('name') }}</option>-->
                                <!--            @endforeach-->
                                <!--        </select>-->
                                <!--    @endif-->
                                <!--</div>-->
                                <!--<div class="col-6 col-lg-auto mb-3 w-lg-200px">-->
                                <!--    <label class="mb-0 opacity-50">{{ translate('Sort by')}}</label>-->
                                <!--    <select class="form-control form-control-sm aiz-selectpicker" name="sort_by" onchange="filter()">-->
                                <!--        <option value="newest" @isset($sort_by) @if ($sort_by == 'newest') selected @endif @endisset>{{ translate('Newest')}}</option>-->
                                <!--        <option value="oldest" @isset($sort_by) @if ($sort_by == 'oldest') selected @endif @endisset>{{ translate('Oldest')}}</option>-->
                                <!--        <option value="price-asc" @isset($sort_by) @if ($sort_by == 'price-asc') selected @endif @endisset>{{ translate('Price low to high')}}</option>-->
                                <!--        <option value="price-desc" @isset($sort_by) @if ($sort_by == 'price-desc') selected @endif @endisset>{{ translate('Price high to low')}}</option>-->
                                <!--    </select>-->
                                <!--</div>-->
                            </div>
                        </div>
                        <input type="hidden" name="min_price" value="">
                        <input type="hidden" name="max_price" value="">
                        <div class="row gutters-5 row-cols-xxl-5 row-cols-xl-4 row-cols-lg-4 row-cols-md-2 row-cols-2" id="data-wrapper">
                            @foreach ($products as $key => $product)
                                <div class="col">
                                    @include('frontend.partials.product_box_1',['product' => $product])
                                </div>
                            @endforeach
                        </div>
                        @if (request()->routeIs("products.category"))
                        <div class="auto-load text-center">
                            <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                x="0px" y="0px" height="60" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
                                <path fill="#000"
                                    d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
                                    <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s"
                                        from="0 50 50" to="360 50 50" repeatCount="indefinite" />
                                </path>
                            </svg>
                        </div>
                        @endif
                        {{-- <div class="aiz-pagination aiz-pagination-center mt-4">
                            {{ $products->appends(request()->input())->links() }}
                        </div> --}}
                    </div>
                </div>
            </form>
        </div>
    </section>


    @include("frontend.partials.bottombar")
@endsection

@section('script')
    <script type="text/javascript">
        function filter(){
            $('#search-form').submit();
        }
        function rangefilter(arg){
            $('input[name=min_price]').val(arg[0]);
            $('input[name=max_price]').val(arg[1]);
            filter();
        }
    </script>

@if (request()->routeIs("products.category"))
<script>
    var ENDPOINT = "{{ route('products.category', request()->route('category_slug')) }}";
    var page = 2;
    infinteLoadMore(page);
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            page++;
            infinteLoadMore(page);
        }
    });
    function infinteLoadMore(page) {
        $.ajax({
                url: ENDPOINT + "?page=" + page,
                datatype: "html",
                type: "get",
                beforeSend: function () {
                    $('.auto-load').show();
                }
            })
            .done(function (response) {
                if (response.length == 0) {
                    $('.auto-load').html("");
                    return;
                }
                $('.auto-load').hide();
                $("#data-wrapper").append(response);
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {
                console.log('Server error occured');
            });
    }
</script>
@endif
@endsection
