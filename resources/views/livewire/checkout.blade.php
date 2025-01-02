<div class="container my-3">
 
    <div class="checkout_checkoutsection">
        <div class="delivery-address-section mb-3">
            <div class="pt-3">
                <hr>
                <div class="d-flex align-items-center justify-content-between">
                    <h6>Delivery Address</h6>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6 mx-auto py-4">
            
                            <div class="mb-3">
                                <input type="text" wire:keydown="findPhoneInDb" wire:model="phone_number" name="phone_number" class="form-control shadow-none @error('phone_number') is-invalid @enderror" placeholder="Phone Number">
                                <div class="invalid-feedback">@error('phone_number') {{ $message }} @enderror</div>
                                {{-- <div class="input-group">
                                    <span class="input-group-text bg-white" id="basic-addon1">
                                        <img src="{{ asset('livewire/flag.png') }}" alt="" width="25" class="img-fluid"> 
                                        <span class="ms-2 fw-bold">+88</span>
                                    </span>
                                    <input type="text" name="phone_number" class="form-control shadow-none" placeholder="Phone Number">
                                </div> --}}
                            </div>
                            <div class="mb-3">
                                <input type="text" wire:model="name" name="name" class="form-control shadow-none @error('name') is-invalid @enderror" placeholder="Name">
                                <div class="invalid-feedback">@error('name') {{ $message }} @enderror</div>
                            </div>
                            <div class="mb-3">
                                <textarea rows="3" wire:model="address" name="address" class="form-control shadow-none @error('address') is-invalid @enderror" placeholder="Address"></textarea>
                                <div class="invalid-feedback">@error('address') {{ $message }} @enderror</div>
                            </div>
                    
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div>
                <hr>
                <div>
                    <h6>Payment Methods</h6>
                </div>
                <hr>
                <div class="row">
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="checkout_paymentmethods">
                            <div class="payment-method-active cash-on-delivery">
                                <img src="{{asset('livewire/cod.png')}}" width="50">
                                <h6 class="text-center">Cash on Delivery</h6>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="checkout_paymentmethods">
                        <div class=" bkash">
                            <img src="{{asset('livewire/bkash.png')}}" width="50">
                            <h6 class="text-center">Bkash</h6>
                        </div>
                    </div>
                    </div> --}}
                </div>
            </div>
            <hr>
        </div>
        @if ($general_error)
            <div class="d-flex justify-content-center">
                <div class="alert alert-danger">
                    <strong>Error! </strong>{{$general_error}}
                </div>
            </div>
        @endif
        <div class="mt-5">
            <div class="d-flex justify-content-end checkout-btn-row">
                <div>
                    <div class="text-right mb-2"><small><span class="shipping_cost">{{ $shippingCost }}</span> Delivery Charge Included</small></div>
                    <div class="checkout-proceed-btn-container">
                        <button wire:click="placeOrder" type="submit" class="btn btn-primary cart-proceed-btn"><span>Proceed</span><span>{{ $totalCost }}</span></button>
                    </div>
                    <div><small>By clicking/tapping proceed, I agree to Muslim Town <a href="/page/terms" wire:navigate>Terms of Services</a></small></div>
                </div>
            </div>
        </div>
    </div>

 </div>
