<div class="container my-3">

    <div class="checkout_checkoutsection">
        <div class="delivery-address-section mb-3">
            <div class="pt-3">

                <div class="row">
                    <div class="col-md-6 mx-auto pt-2 checkout-col">

                        {{-- Delivery Information --}}
                        <div>
                       
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="text-black">Delivery Information</h6>
                            </div>
                            <hr>
                            <div class="mb-3">
                                {{-- <input type="text" wire:keydown="findPhoneInDb" wire:model="phone_number" name="phone_number" class="form-control shadow-none @error('phone_number') is-invalid @enderror" placeholder="Your Phone Number">
                                <div class="invalid-feedback">@error('phone_number') {{ $message }} @enderror</div> --}}
                                <div class="input-group">
                                    <span class="input-group-text bg-white @error('phone_number') red-border @enderror"
                                        id="basic-addon1">
                                        <img src="{{ asset('livewire/flag.png') }}" alt="" width="25"
                                            class="img-fluid">
                                        <span class="ms-2">+88</span>
                                    </span>
                                    <input type="tel" wire:model="phone_number" name="phone_number"
                                        class="form-control @error('phone_number') is-invalid @enderror shadow-none"
                                        placeholder="Your Phone Number" wire:keydown="findPhoneInDb">
                                </div>
                                <div class="text-danger">
                                    @error('phone_number')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="text" wire:model="name" name="name"
                                    class="form-control shadow-none @error('name') is-invalid @enderror"
                                    placeholder="Your Full Name">
                                <div class="invalid-feedback">
                                    @error('name')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea rows="3" wire:model="address" name="address"
                                    class="form-control shadow-none @error('address') is-invalid @enderror" placeholder="Your Address"></textarea>
                                <div class="invalid-feedback">
                                    @error('address')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Cart Items --}}
                        <div class="my-4">
                       
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="text-black">Items</h6>
                            </div>
                            <hr>
                            <ul class="cart-listsss">
                                @forelse ($cartItems as $cartItem)
                                    <li class="right-sidebar-cart">
                                        <div class="cart-counter">
                                            <button
                                                wire:click="$dispatchTo('cart-sidebar','cart-updated', {product_id: {{ $cartItem->product_id }}, type: '-'})"
                                                type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M17,9.17a1,1,0,0,0-1.41,0L12,12.71,8.46,9.17a1,1,0,0,0-1.41,0,1,1,0,0,0,0,1.42l4.24,4.24a1,1,0,0,0,1.42,0L17,10.59A1,1,0,0,0,17,9.17Z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <div>{{ $cartItem->quantity }}</div>
                                            <button
                                                wire:click="$dispatchTo('cart-sidebar','cart-updated', {product_id: {{ $cartItem->product_id }}, type: '+'})"
                                                type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="currentColor">
                                                    <path
                                                        d="M17,13.41,12.71,9.17a1,1,0,0,0-1.42,0L7.05,13.41a1,1,0,0,0,0,1.42,1,1,0,0,0,1.41,0L12,11.29l3.54,3.54a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29A1,1,0,0,0,17,13.41Z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="cart-image"><img
                                                src="{{ api_asset($cartItem->product->thumbnail_img) }}"
                                                class="img-fluid px-1" alt="Mola Dry Fish" width="60" /></div>
                                        <div class="cart-product-info">
                                            <p class="my-0">{{ $cartItem->product->name }}
                                                @if ($cartItem->variation)
                                                    <span class="text-orange">({{ $cartItem->variation }})</span>
                                                @endif
                                            </p>
                                            <p class="my-0">
                                                <span>{{ currency_symbol() . $cartItem->product->unit_price }}/</span><span>{{ $cartItem->product->unit_value . ' ' . $cartItem->product->unit }}</span>
                                            </p>
                                        </div>
                                        <div class="cart-product-price">
                                            <p class="text-danger">
                                                {{ currency_symbol() . $cartItem->product->unit_price * $cartItem->quantity }}
                                            </p>
                                        </div>
                                        <button
                                            wire:click="$dispatchTo('cart-sidebar','cart-updated', {product_id: {{ $cartItem->product_id }}, type: '0'})"
                                            class="cart-item-remove-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <path
                                                    d="M13.41,12l4.3-4.29a1,1,0,1,0-1.42-1.42L12,10.59,7.71,6.29A1,1,0,0,0,6.29,7.71L10.59,12l-4.3,4.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L12,13.41l4.29,4.3a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42Z">
                                                </path>
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
                        </div>


                        {{-- Payment Methods --}}
                        <div>
                  
                            <div>
                                <h6 class="text-black">Payment Methods</h6>
                            </div>
                            <hr>
                            <div class="row">

                                <div class="col-12">
                                    <div class="paymentmethods">
                                        <label
                                            class="{{ $payment_method == 'cash_on_delivery' ? 'payment-method-active' : '' }} rounded-3 p-3 text-center d-flex align-items-center ps-0"
                                            wire:click="updatePaymentMethod('cash_on_delivery')">
                                            {{-- <img src="{{asset('livewire/cod.png')}}" width="70" class="rounded-3 mb-2"> --}}
                                            <input id="cash_on_delivery" type="radio" name="payment_method"
                                                value="cash_on_delivery" checked>
                                            <p class="text-center text-black mb-0 ms-2">Cash on Delivery</p>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="paymentmethods">
                                        <label
                                            class="{{ $payment_method == 'sslcommerz' ? 'payment-method-active' : '' }} rounded-3 p-3 text-center d-flex align-items-center ps-0"
                                            wire:click="updatePaymentMethod('sslcommerz')">
                                            {{-- <img src="{{asset('livewire/sslcommerz.png')}}" width="185" class="rounded-3 mb-2"> --}}
                                            <input id="cash_on_delivery" type="radio" name="payment_method"
                                                value="cash_on_delivery">
                                            <p class="text-center text-black mb-0 ms-2">Pay
                                                Online(Card/bKash/Rocket/Nagad)</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>


                        <div class="checkout_coupon_area d-flex align-items-center">
                            <input @if ($coupon_discount > 0) disabled @endif wire:model="coupon_code"
                                type="text" class="form-control rounded-0 py-2" name="code"
                                placeholder="Apply Coupon" value="">
                            @if ($coupon_discount > 0)
                                <button wire:click="initializeData" type="button"
                                    class="btn btn-danger rounded-0 py-2">Remove</button>
                            @else
                                <button wire:click="checkCoupon" type="button"
                                    class="btn btn-primary rounded-0 py-2">Apply</button>
                            @endif
                        </div>
                        @if ($coupon_discount > 0)
                            <p class="text-success">{{ currency_symbol() . $coupon_discount }} discount applied.
                            </p>
                        @endif


                        @if ($general_error)
                            <div class="d-flex justify-content-center mt-3">
                                <div class="alert alert-danger">
                                    <strong>Error! </strong>{{ $general_error }}
                                </div>
                            </div>
                        @endif
                        <div class="mt-2">
                            <div class="d-flex justify-content-end checkout-btn-row">
                                <div>
                                    <div class="text-right mb-2">
                                        @if ($shippingCost == '৳0')
                                            <small>
                                                Delivery Charge Free
                                            </small>
                                        @else
                                            <small>
                                                <span class="shipping_cost">{{ $shippingCost }}</span> Delivery Charge
                                                Included
                                            </small>
                                        @endif
                                    </div>
                                    <div class="checkout-proceed-btn-container">
                                        <button wire:click="placeOrder" type="submit"
                                            class="btn btn-primary cart-proceed-btn"><span>Confirm</span><span>{{ currency_symbol() . $totalCost }}</span></button>
                                    </div>
                                    <div><small>By clicking/tapping proceed, I agree to Muslim Town <a
                                                href="/page/terms" wire:navigate>Terms of Services</a></small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>


    </div>

</div>

{{-- @push('scripts')
<script>
    document.getElementById("sidebar-container").style.display = 'none';
</script>
@endpush --}}
