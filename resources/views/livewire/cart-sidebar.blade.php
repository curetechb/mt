<div id="sidebar-container">
    <div class="cart-box toggle-cart-sidebar" role="button">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="currentColor">
                <path
                    d="M19,7H16V6A4,4,0,0,0,8,6V7H5A1,1,0,0,0,4,8V19a3,3,0,0,0,3,3H17a3,3,0,0,0,3-3V8A1,1,0,0,0,19,7ZM10,6a2,2,0,0,1,4,0V7H10Zm8,13a1,1,0,0,1-1,1H7a1,1,0,0,1-1-1V9H8v1a1,1,0,0,0,2,0V9h4v1a1,1,0,0,0,2,0V9h2Z">
                </path>
            </svg>
        </div>
        <p><span>{{$cartCount}}</span> ITEMS</p>
        <h6>{{$cartTotal}}</h6>
    </div>
    <aside class="right-sidebar" id="cartsidebar" wire:ignore.self>
        <div class="cart-close-btn toggle-cart-sidebar d-none" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M15.54,11.29,9.88,5.64a1,1,0,0,0-1.42,0,1,1,0,0,0,0,1.41l4.95,5L8.46,17a1,1,0,0,0,0,1.41,1,1,0,0,0,.71.3,1,1,0,0,0,.71-.3l5.66-5.65A1,1,0,0,0,15.54,11.29Z"></path></svg>
        </div>
        <div class="right-sidebar-content">
            <div class="d-flex align-items-center justify-content-between px-2 py-3">
                <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M19,7H16V6A4,4,0,0,0,8,6V7H5A1,1,0,0,0,4,8V19a3,3,0,0,0,3,3H17a3,3,0,0,0,3-3V8A1,1,0,0,0,19,7ZM10,6a2,2,0,0,1,4,0V7H10Zm8,13a1,1,0,0,1-1,1H7a1,1,0,0,1-1-1V9H8v1a1,1,0,0,0,2,0V9h4v1a1,1,0,0,0,2,0V9h2Z">
                        </path>
                    </svg>
                    {{$cartCount}} Items in the Bag
                </div>
                <button type="button" class="rsidebar-close-btn toggle-cart-sidebar">Shop More</button>
            </div>
            <hr class="my-0" />
            <ul class="p-2 cart-list">


                @forelse ($cartItems as $cartItem)
                    <li class="right-sidebar-cart">
                        <div class="cart-counter">
                            <button wire:click="update({{$cartItem->product_id}}, '-')" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17,9.17a1,1,0,0,0-1.41,0L12,12.71,8.46,9.17a1,1,0,0,0-1.41,0,1,1,0,0,0,0,1.42l4.24,4.24a1,1,0,0,0,1.42,0L17,10.59A1,1,0,0,0,17,9.17Z"></path>
                                </svg>
                            </button>
                            <div>{{ $cartItem->quantity }}</div>
                            <button wire:click="update({{$cartItem->product_id}}, '+')" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17,13.41,12.71,9.17a1,1,0,0,0-1.42,0L7.05,13.41a1,1,0,0,0,0,1.42,1,1,0,0,0,1.41,0L12,11.29l3.54,3.54a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29A1,1,0,0,0,17,13.41Z"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="cart-image"><img src="{{api_asset($cartItem->product->thumbnail_img)}}" class="img-fluid px-1" alt="Mola Dry Fish" width="60" /></div>
                        <div class="cart-product-info">
                            <p class="my-0">{{ $cartItem->product->name }} 
                                @if($cartItem->variation)
                                <span class="text-orange">({{ $cartItem->variation }})</span>
                                @endif
                            </p>
                            <p class="my-0"><span>{{ currency_symbol().$cartItem->product->unit_price }}/</span><span>{{ $cartItem->product->unit_value." ".$cartItem->product->unit }}</span></p>
                        </div>
                        <div class="cart-product-price"><p class="text-danger">{{ currency_symbol(). $cartItem->product->unit_price * $cartItem->quantity }}</p></div>
                        <button wire:click="destroy({{$cartItem->product_id}})" class="cart-item-remove-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13.41,12l4.3-4.29a1,1,0,1,0-1.42-1.42L12,10.59,7.71,6.29A1,1,0,0,0,6.29,7.71L10.59,12l-4.3,4.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L12,13.41l4.29,4.3a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42Z"></path>
                            </svg>
                        </button>
                    </li>
                @empty
                    <div class="cartisempty">
                        <div class="text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M19,7H16V6A4,4,0,0,0,8,6V7H5A1,1,0,0,0,4,8V19a3,3,0,0,0,3,3H17a3,3,0,0,0,3-3V8A1,1,0,0,0,19,7ZM10,6a2,2,0,0,1,4,0V7H10Zm8,13a1,1,0,0,1-1,1H7a1,1,0,0,1-1-1V9H8v1a1,1,0,0,0,2,0V9h4v1a1,1,0,0,0,2,0V9h2Z">
                                </path>
                            </svg>
                            <h6>Cart is Empty</h6>
                        </div>
                    </div>
                @endforelse


            </ul>
            @if ($path != "checkout" && $cartCount > 0)
                <div class="px-2 cart-proceed">
                    <a class="btn btn-primary cart-proceed-btn" href="/checkout" wire:navigate>
                        <span>Checkout</span><span class="ml-auto checkout-cart-total">{{$cartTotal}}</span>
                    </a>
                </div>
            @endif

        </div>
    </aside>

    @php
        $except = ["checkout", "purchased/".request('order_id')];
    @endphp

    @if (!in_array($path, $except))
    <div class="bottom-bar-container d-md-none">
        <div class="bottombar_bottombar">
            <a href="#" class="bottombar_bottomcarttotal">{{$cartTotal}}</a>
            <a href="#" class="bottombar_bottomcartproceed toggle-cart-sidebar">Place Order</a>
            <a href="#" class="bottombar_bottomcartcount toggle-cart-sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="currentColor">
                    <path
                        d="M19,7H16V6A4,4,0,0,0,8,6V7H5A1,1,0,0,0,4,8V19a3,3,0,0,0,3,3H17a3,3,0,0,0,3-3V8A1,1,0,0,0,19,7ZM10,6a2,2,0,0,1,4,0V7H10Zm8,13a1,1,0,0,1-1,1H7a1,1,0,0,1-1-1V9H8v1a1,1,0,0,0,2,0V9h4v1a1,1,0,0,0,2,0V9h2Z">
                    </path>
                </svg>
                <span>{{$cartCount}}</span>
            </a>
        </div>
    </div>
    @endif

</div>



