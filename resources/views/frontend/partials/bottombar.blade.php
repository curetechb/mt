@php
    if(Auth::check()){
        $cart = \App\Models\Cart::where('user_id', Auth::user()->id)->get();
    }else{
        $temp_user_id = Session()->get('temp_user_id');
        $cart = \App\Models\Cart::where('temp_user_id', $temp_user_id)->get();
    }
    $total = 0;
    foreach($cart as $key => $cartItem){
        $total = $total + ($cartItem->price + $cartItem->tax) * $cartItem->quantity;
    }
@endphp
<div class="aiz-mobile-bottom-nav d-xl-none fixed-bottom bg-white shadow-lg border-top rounded-top" style="box-shadow: 0px -1px 10px rgb(0 0 0 / 15%)!important; ">
    <div class="row align-items-center gutters-5">

        <div class="col-3 bc1">
            <a href="" class="text-reset d-block text-center open-cart" style="color: orange">
                <span class="d-block fs-14 fw-600 opacity-60 cart-total">{{ single_price($total ?? 0) }}</span>
            </a>
        </div>
        <div class="col-6 bc2" style="background: orange; color: #fff">
            <a href="{{ $total > 0 ? route("checkout.shipping_info") : "javascript:void(0)" }}" class="text-reset text-center mplaceorder mx-auto p-3 mtoggler">
                <span class="d-block fs-14 fw-600 ">{{ translate($total > 0 ? 'Place Order' : 'Start Shopping') }}</span>
            </a>
        </div>

        <div class="col-3 bc3">
            <a href="" class="text-reset d-flex align-items-center justify-content-center open-cart">
                <i class="las la-shopping-cart fs-22 opacity-60"></i>
                @php
                    $count = (isset($cart) && count($cart)) ? count($cart) : 0;
                @endphp
                <span class="badge bg-primary ml-1 fw-600 cart-count">{{$count}}</span>
            </a>
        </div>
    </div>
</div>


