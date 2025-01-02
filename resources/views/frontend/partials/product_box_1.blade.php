<div class="aiz-card-box rounded mt-1 mb-5 bg-white product-box">

    <div class="product-main-layer">
        @if(discount_in_percentage($product) > 0)
            <span class="badge-custom">{{ translate('OFF') }}<span class="box ml-1 mr-0">&nbsp;{{discount_in_percentage($product)}}%</span></span>
        @endif
        <div class="position-relative">
            <a href="javascript:void(0)" class="d-block" onclick="showAddToCartModal({{ $product->id }})">
                <img
                    class="img-fit lazyload mx-auto h-140px h-md-210px"
                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                    data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                    alt="{{  $product->getTranslation('name')  }}"
                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                >
            </a>

        </div>
        <div class="p-md-3 p-2 text-center">

            <h3 class="fw-normal fs-14 text-truncate-2 lh-1-4 mb-3">
                <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }})" class="d-block text-reset">{{  $product->getTranslation('name')  }}</a>
            </h3>
            <div>
                 <span style="font-size: 13px; color: #666">{{ $product->unit_value > 0 ? $product->unit_value : "" }} {{ $product->unit }}</span> 
                {{-- <span style="font-size: 13px; color: #666">{{ $product->unit }}</span> --}}
            </div>
            <div class="fs-15 product-box-price">
                @if(home_base_price($product) != home_discounted_base_price($product))
                    <del class="fw-600 opacity-50 mr-1">{{ home_base_price($product) }}</del>
                @endif
                <span class="fw-700">{{ home_discounted_base_price($product) }}</span>
            </div>

        </div>

        <div class="product-overlayer">
            <a href="javascript:void(0)" onclick="showAddToCartModal({{ $product->id }})" href="#">Item Details</a>
        </div>
    </div>

    @php
        $temp_user = session("temp_user_id");
        $incart = \App\Models\Cart::where('temp_user_id', $temp_user)->where("product_id", $product->id)->first();
        $stock = $product->stocks->where('variant', null)->first();
    @endphp

    @if ($stock->qty <= $product->low_stock_quantity)
        <div class="pbtn addtocartplusminus" style="opacity: 0.6">
            <button type="button" class="border-0 w-100" disabled>Out of Stock</button>
        </div>
    @else
        <div class="@if($incart) pbtn @endif d-flex align-items-center justify-content-between addtocartplusminus product_{{ $product->id }}" data-id="{{ $product->id }}">
            <button class="@if(!$incart) d-none @endif cartbtn minusfrombag" data-id="minus" type="button">-</button>
            <button type="button" class="cartbtn bagtext w-100" data-id="plus">{{ $incart ? $incart->quantity." in Bag" : "Add to Bag" }}</button>
            <button class="@if(!$incart) d-none @endif cartbtn addtobag" data-id="plus" type="button">+</button>
        </div>
    @endif

</div>

