<div class="aiz-category-menu bg-white h-100 @if(Route::currentRouteName() == 'home') shadow-sm" @else shadow-lg" id="category-sidebar" @endif>
    {{-- <div class="p-3 bg-soft-primary d-none d-lg-block rounded-top all-category position-relative text-left">
        <span class="fw-600 fs-16 mr-3">{{ translate('Categories') }}</span>
        <a href="{{ route('categories.all') }}" class="text-reset">
            <span class="d-none d-lg-inline-block">{{ translate('See All') }} ></span>
        </a>
    </div> --}}
    <ul class="list-unstyled categories no-scrollbar py-2 mb-0 text-left">

        {{-- @php
            $flash_deal = \App\Models\FlashDeal::where('slug', 'ramadan-special')
                ->first();
        @endphp
        <li class="category-nav-element">
            <a href="{{ route("flash-deal-details", $flash_deal->slug) }}" class="text-truncate text-reset py-2 px-3 d-block">

                <img
                    class="cat-image lazyload mr-2"
                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                    data-src="{{ static_asset('assets/img/fruit.png') }}"
                    width="20" height="20"
                    alt="{{ $flash_deal->title }}"
                    class="img-fit w-100 lazyload"
                >
                <span class="top-cat-name">{{ $flash_deal->title }}</span>
            </a>
        </li> --}}

        @foreach (\App\Models\Category::where('level', 0)->orderBy('order_level', 'desc')->get() as $key => $category)
            <li class="category-nav-element" data-id="{{ $category->id }}">
                <a href="{{ route('products.category', $category->slug) }}" class="text-truncate text-reset py-2 px-3 d-block">
                    <img
                        class="cat-image lazyload mr-2"
                        src="{{ static_asset('assets/img/placeholder.jpg') }}"
                        data-src="{{ uploaded_asset($category->icon) }}"
                        width="20" height="20"
                        alt="{{ $category->name }}"
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                    >
                    <span class="top-cat-name">{{ $category->name }}</span>
                </a>
                @if ($category->categories()->count() > 0)
                    <div class="sub-cat-menu c-scrollbar-light rounded shadow-lg p-3">
                        <ul class="list-unstyled categories no-scrollbar py-2 mb-0 text-left">
                            @foreach ($category->categories as $subcat)
                                <li class="category-nav-element">
                                    <a href="{{ route('products.category', $subcat->slug) }}" class="text-truncate text-reset py-2 px-3 d-block">
                                        <span class="cat-name">{{ $subcat->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- @if(count(\App\Utility\CategoryUtility::get_immediate_children_ids($category->id))>0)
                    <div class="sub-cat-menu c-scrollbar-light rounded shadow-lg p-4">
                        <div class="c-preloader text-center absolute-center">
                            <i class="las la-spinner la-spin la-3x opacity-70"></i>
                        </div>
                    </div>
                @endif --}}
            </li>
        @endforeach
    </ul>
</div>
