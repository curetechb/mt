@extends('frontend.layouts.app')

@section('content')

<section class="pt-5 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col active">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 las la-shopping-cart"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart')}}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-map"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('2. Shipping & Payment')}}</h3>
                        </div>
                    </div>
                    {{-- <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-truck"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('3. Delivery info')}}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('4. Payment')}}</h3>
                        </div>
                    </div> --}}
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('3. Confirmation')}}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-4" id="cart-summary">
    <div class="container">
        @if( $carts && count($carts) > 0 )
            <div class="row">
                <div class="col-md-12 col-lg-10 mx-auto">
                    <div class="shadow-sm bg-white p-3 p-lg-4 rounded text-left cartmoblist">
                        <div class="mb-4">
                            <div class="row gutters-5 d-lg-flex border-bottom mb-3 pb-3">
                                <div class="col-5 fw-600">{{ translate('Product')}}</div>
                                <div class="col-2 fw-600 d-none d-lg-block">{{ translate('Price')}}</div>
                                {{-- <div class="col fw-600">{{ translate('Tax')}}</div> --}}
                                <div class="col-lg-2 col-3 fw-600">{{ translate('Quantity')}}</div>
                                <div class="col-2 fw-600">{{ translate('Total')}}</div>
                                <div class="col-1 fw-600">{{ translate('Remove')}}</div>
                            </div>
                            <ul class="list-group list-group-flush">
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($carts as $key => $cartItem)
                                    @php
                                        $product = \App\Models\Product::find($cartItem['product_id']);
                                        $product_stock = $product->stocks->where('variant', $cartItem['variation'])->first();
                                        $total = $total + ($product->unit_price + $cartItem['tax']) * $cartItem['quantity'];
                                        $product_name_with_choice = $product->getTranslation('name');
                                        if ($cartItem['variation'] != null) {
                                            $product_name_with_choice = $product->getTranslation('name').' - '.$cartItem['variation'];
                                        }
                                    @endphp
                                    <li class="list-group-item px-0 px-lg-3 ">
                                        <div class="row gutters-5 cart-items-row">
                                            <div class="col-5 d-flex">
                                                <span class="mr-2 ml-0">
                                                    <img
                                                        src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                        class="img-fit size-60px rounded"
                                                        alt="{{ $product->getTranslation('name')  }}"
                                                    >
                                                </span>
                                                <div>
                                                    <div class="fs-14 opacity-60 mob-product-title">
                                                        {{ $product_name_with_choice }}
                                                        {{ $product->unit }}
                                                    </div>
                                                    <div class="fw-600 fs-16 d-block d-lg-none mob-single-price">{{ single_price($product->unit_price) }}</div>
                                                    {{-- <div>{{ $cartItem['quantity'] }} * {{ $product->unit_value }} {{ $product->unit }} = {{ $product->unit_value * $cartItem['quantity'] }} {{ $product->unit }}</div> --}}
                                                </div>
                                            </div>

                                            <div class="col-2 order-1 my-3 my-lg-0 d-none d-lg-block">
                                                <span class="opacity-60 fs-12 d-block d-lg-none">{{ translate('Price')}}</span>
                                                <span class="fw-600 fs-16">{{ single_price($product->unit_price) }}</span>
                                            </div>
                                            {{-- <div class="col-lg col-4 order-2 order-lg-0 my-3 my-lg-0">
                                                <span class="opacity-60 fs-12 d-block d-lg-none">{{ translate('Tax')}}</span>
                                                <span class="fw-600 fs-16">{{ single_price($cartItem['tax']) }}</span>
                                            </div> --}}

                                            <div class="col-lg-2 col-3 order-3">
                                                <div class="row no-gutters align-items-center aiz-plus-minus mr-2 ml-0 qty-counter">
                                                    <button class="cartminusbtn btn col-auto btn-icon btn-sm btn-circle btn-light" type="button" >
                                                        <i class="las la-minus"></i>
                                                    </button>
                                                    <input type="number" name="quantity[{{ $cartItem['id'] }}]" data-id="{{ $cartItem['id'] }}" class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="{{ $cartItem['quantity'] }}" readonly>
                                                    <button class="cartplusbtn btn col-auto btn-icon btn-sm btn-circle btn-light" type="button" >
                                                        <i class="las la-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-2 order-4 my-3 my-lg-0">
                                                {{-- <span class="opacity-60 fs-12 d-block d-lg-none">{{ translate('Total')}}</span> --}}
                                                <span class="fw-600 fs-16 text-primary mob-total-price">{{ single_price(($product->unit_price + $cartItem['tax']) * $cartItem['quantity']) }}</span>
                                            </div>
                                            <div class="col-1 order-5 text-right">
                                                <a href="javascript:void(0)" onclick="removeFromCartView(event, {{ $cartItem['id'] }})" class="btn btn-icon btn-sm btn-soft-primary btn-circle">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="px-3 py-2 mb-4 border-top d-flex justify-content-between">
                            <span class="opacity-60 fs-15">{{translate('Subtotal')}}</span>
                            <span class="fw-600 fs-17">{{ single_price($total) }}</span>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-6 text-center text-md-left order-1 order-md-0">
                                <a href="{{ route('home') }}" class="btn btn-link">
                                    <i class="las la-arrow-left"></i>
                                    {{ translate('Return to shop')}}
                                </a>
                            </div>
                            <div class="col-md-6 text-center text-md-right">
                                <a href="{{ route('checkout.shipping_info') }}" class="btn btn-primary fw-600">
                                    {{ translate('Continue')}}
                                </a>
                                {{-- @if(Auth::check())
                                    <a href="{{ route('checkout.shipping_info') }}" class="btn btn-primary fw-600">
                                        {{ translate('Continue to Shipping')}}
                                    </a>
                                @else
                                    <button class="btn btn-primary fw-600" onclick="showCheckoutModal()">{{ translate('Continue to Shipping')}}</button>
                                @endif --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="shadow-sm bg-white p-4 rounded">
                        <div class="text-center p-3">
                            <i class="las la-frown la-3x opacity-60 mb-3"></i>
                            <h3 class="h4 fw-700">{{translate('Your Cart is empty')}}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

@endsection

@section('script')
    <script type="text/javascript">
        function removeFromCartView(e, key){
            e.preventDefault();
            removeFromCart(key);
        }

        $(document).ready(function() {

            $(document).on("click", ".cartminusbtn, .cartplusbtn", function(){

                const thisobj = $(this).siblings('.input-number');
                let id = thisobj.data("id");
                let val = parseInt(thisobj.val());

                if($(this).hasClass("cartminusbtn")){
                    if(val <= 1) return false;
                    val = val - 1;
                }else{
                    val = val + 1;
                }

                $.ajax({
                    type:"POST",
                    url: '{{ route('cart.updateQuantity') }}',
                    data: {id:id, quantity: val, _token: "{{ csrf_token() }}", from: 'cart'},
                    success: function(data){

                        if(data.modal_view){
                            $('.c-preloader').hide();
                            $('#addToCart').modal();
                            $('#addToCart-modal-body').html(data.modal_view);
                        }
                        updateNavCart(data.nav_cart_view,data.cart_count);
                        $('#cart-summary').html(data.cart_view);
                    }
                });

            });


        });

    </script>
@endsection
