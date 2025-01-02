            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-2">

                        @php
                            $address = json_decode($order->shipping_address);
                        @endphp
                        <h5>Address</h6>
                            {{ $address->address ?? "" }},
                            {{ $address->state }}
                            {{ $address->floor_no  ? ", Floor: $address->floor_no" : "" }}
                            {{ $address->apartment ? ", Apartment: $address->apartment" : "" }}
                        @if ($order->payment_status == "unpaid")
                            <div class="d-flex justify-content-center align-items-center">
                                @if (get_setting('bkash') == 1)
                                <div class="rounded text-center p-2 d-flex border mt-2" role="button" id="bkash_pay" data-total="{{ $order->grand_total - $order->coupon_discount - $order->reward_discount }}">
                                    <img src="{{ static_asset('assets/img/cards/bkash.png')}}" width="40" class="img-fluid">
                                    <div class="d-block text-center ml-2">
                                        <span class="d-block fw-600 fs-15">{{ translate('Pay with Bkash')}}</span>
                                    </div>
                                </div>
                                @endif

                                @php
                                    $now = new \DateTime();
                                    $currentTimestamp = $now->getTimestamp();
                                    $expired = false;
                                    if($order->date + 1800 <= $currentTimestamp){
                                        $expired = true;
                                    }
                                @endphp

                                @if ($order->delivery_status == 'pending' && !$expired)
                                    <a href="javascript:void(0)" class="ml-2 btn btn-soft-danger btn-sm confirm-delete" data-id="{{ $order->id }}" id="cancelorderbtn" style="margin-top: 5px;padding: 9px 20px;">
                                        <span id="ordercanceltimer"></span>
                                        Cancel Order
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-5">
                        <h6>#{{ $order->code }}</h6>
                        <h6>{{$order->created_at->format("d-M-Y h:iA")}}</h6>
                    </div>

                    @foreach ($order->orderDetails as $key => $orderDetail)
                        <div class="purchase-details">
                            <!-- <div class="d-flex"> -->
                            <div class="float-left">
                                <img class="img-fluid lazyload mr-2" width="40" src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($orderDetail->product->thumbnail_img) }}" alt="{{ $orderDetail->product->name  }}" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            </div>
                            <div class="">
                                {{ $orderDetail->product->name }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>{{ $orderDetail->product->unit_value > 0 ? $orderDetail->product->unit_value : "" }} {{ $orderDetail->product->unit }}</p>
                                <p>Qty. {{ $orderDetail->quantity }}</p>
                                <p>Tk.{{ $orderDetail->price }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
