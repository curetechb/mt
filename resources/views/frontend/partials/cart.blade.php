@php
    $temp_user_id = Session()->get('temp_user_id');
    if(Auth::check()){
        $cart = \App\Models\Cart::where('user_id', Auth::user()->id)->get();
    }else{
        $cart = \App\Models\Cart::where('temp_user_id', $temp_user_id)->get();
    }
@endphp

<div class="p-3 fs-15 fw-600 p-3 border-bottom d-flex justify-content-between align-items-center">
    <div>
        {{ count($cart) }} {{ translate(' Items Bag') }}
    </div>
    <button type="button" class="btn btn-secondary btn-sm open-cart">
        Close
    </button>
</div>

@if (isset($cart) && count($cart) > 0)

    <ul class="h-100 overflow-auto c-scrollbar-light list-group list-group-flush cart-item-scroller">
        @php
            $total = 0;
        @endphp
        @foreach ($cart as $key => $cartItem)
            @php
                $product = \App\Models\Product::find($cartItem['product_id'], ['id','name', 'thumbnail_img','unit', 'unit_value','unit_price', 'low_stock_quantity']);
                $total = $total + ($product->unit_price + $cartItem['tax']) * $cartItem['quantity'];
                $stock = $product->stocks->where('variant', null)->first();
            @endphp
            @if ($product != null)
                <li class="list-group-item px-2">
                    <span class="d-flex align-items-center">
                        <div class="col-lg-2 col-3 px-0">
                            <div class="cart-items-row">
                                <div class="row no-gutters align-items-center aiz-plus-minus mr-2 ml-0 qty-counter product_{{ $product->id }}" data-id="{{ $product->id }}">
                                    <button class="cartbtn btn col-auto btn-icon btn-sm btn-circle btn-light" type="button" data-id="minus">
                                        {{-- <i class="las la-minus"></i> --}}
                                        <i class="las la-angle-down"></i>
                                    </button>
                                    <input type="number" name="quantity[{{ $cartItem['id'] }}]" data-id="{{ $cartItem['id'] }}" class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="{{ $cartItem['quantity'] }}" readonly>
                                    <button class="cartbtn btn col-auto btn-icon btn-sm btn-circle btn-light" type="button" data-id="plus">
                                        {{-- <i class="las la-plus"></i> --}}
                                        <i class="las la-angle-up"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <a href="#" class="text-reset d-flex align-items-center flex-grow-1">
                            <img src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                class="img-fit lazyload size-60px rounded" alt="{{ $product->name }}">
                            <span class="minw-0 pl-2 flex-grow-1" style="font-size: 12px">
                                <span class="fw-600 mb-1 text-truncate-2">
                                    {{ $product->name }}
                                </span>
                                {{-- <span class="">{{ $cartItem['quantity'] }}x</span> --}}
                                <div>
                                    <span>{{ single_price($product->unit_price + $cartItem['tax']) }}/</span>
                                    <span>{{ $product->unit_value > 0 ? $product->unit_value : "" }} {{ $product->unit }}</span>
                                </div>
                                @if ($stock->qty <= $product->low_stock_quantity)
                                    <span class="text-danger fw-600">Out of Stock</span>
                                @endif
                            </span>
                        </a>
                        <span class="text text-danger">{{ single_price( ($product->unit_price + $cartItem['tax']) * $cartItem['quantity'] ) }}</span>
                        <span class="" data-id="{{ $product->id }}">
                            <button class="btn btn-sm btn-icon stop-propagation cart-remove-btn" data-id="{{ $cartItem['id'] }}">
                                <i class="la la-close"></i>
                            </button>
                        </span>

                    </span>
                </li>
            @endif
        @endforeach
    </ul>
    <div class="cart-proceed d-none d-xl-block">
        <div class="px-3 py-2 fs-15 border-top d-flex justify-content-between">
            <span class="opacity-60">{{ translate('Subtotal') }}</span>
            <span class="fw-600">{{ single_price($total) }}</span>
        </div>
        <div class="px-3 py-2 text-center border-top">
            <ul class="list-inline mb-0">
                <li class="list-inline-item w-100">
                    <a href="{{ route('checkout.shipping_info') }}" class="btn btn-primary btn-sm w-100 fw-600">
                        {{ translate('Place Order') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>

@else
    <div class="text-center p-3">
        <i class="las la-frown la-3x opacity-60 mb-3"></i>
        <h3 class="h6 fw-700">{{ translate('Your Bag is empty') }}</h3>
    </div>
@endif
