@extends('frontend.layouts.app')

@section('content')
    {{-- @if ($order->payment_status == "unpaid")
        <section class="pt-5">
            <div class="container">
                <div class="row">
                    @if ($order->payment_type == "bkash")
                        <div class="col-md-3">
                            <div class="rounded text-center p-2" role="button" id="bkash_pay" >
                                <img src="{{ static_asset('assets/img/cards/bkash.png')}}" class="img-fluid mb-2">
                                <span class="d-block text-center">
                                    <span class="d-block fw-600 fs-15">{{ translate('Pay with Bkash')}}</span>
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @else
        <div class="alert alert-success fade in alert-dismissible show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true" style="font-size:20px">×</span>
            </button>
            Your Payment is Successfull. Thank You
        </div>
    @endif --}}
    <section class="py-4">
        <div class="container text-left">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="text-center pt-4">
                        <i class="la la-check-circle la-3x text-success mb-3"></i>
                        <h1 class="h3 fw-600">{{ translate('Thank You for Your Order!')}}</h1>
                        {{-- @if (!Auth::user()->email)
                        <p class="text text-primary fw-600 givemailaddress" role="button">{{  translate('Get Invoice in you Mail') }}</p>
                        <div class="d-none mb-4">
                            <form class="form-inline givemailform" method="POST" action="{{ route('invoice.mail') }}">
                                @csrf
                                <div class="form-group mb-0">
                                    <input type="email" class="form-control" placeholder="{{ translate('Your Email Address') }}" name="email" id="invoicemail" required>
                                </div>
                                <button type="submit" class="btn btn-primary invbtn">
                                    {{ translate('Get Invoice') }}
                                </button>
                            </form>
                        </div>
                        @else
                            <p class="opacity-70 font-italic">{{  translate('A copy or your order summary has been sent to your Email') }}</p>
                        @endif --}}
                    </div>

                    <div class="mb-4 bg-white p-4 rounded shadow-sm">
                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <div class="p-3" style="background: #f8f8f8; border-radius: 4px">
                                    <h5 class="fw-600 my-3 fs-17 pb-2 text-center">{{ translate('Order Details')}}</h5>
                                    <table class="table">
                                        <tr>
                                            <td class="w-50 fw-600">{{ translate('Order Number')}}:</td>
                                            <td>{{ $order->code }}</td>
                                        </tr>
                                        <tr>
                                            <td class="w-50 fw-600">{{ translate('Total')}}:</td>
                                            <td class="w-50 fw-600"> {{ single_price($order->grand_total - $order->coupon_discount - $order->reward_discount) }} <small>(Inc. Shipping Cost)</small></td>
                                        </tr>
                                        @if ($order->reward_discount > 0)
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Reward Discount')}}:</td>
                                                <td class="w-50 fw-600"> <span class="text-success">-{{ single_price($order->reward_discount) }}</span></td>
                                            </tr>
                                        @endif
                                        @if ($order->coupon_discount > 0)
                                            <tr>
                                                <td class="w-50 fw-600">{{ translate('Coupon Discount')}}:</td>
                                                <td class="w-50 fw-600"> <span class="text-success">-{{ single_price($order->coupon_discount) }}</span></td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="w-50 fw-600">{{ translate('Address')}}:</td>
                                            <td>
                                                {{ json_decode($order->shipping_address)->address }},
                                                {{ json_decode($order->shipping_address)->state }}
                                                {{ json_decode($order->shipping_address)->floor_no ? ", Floor No - ".json_decode($order->shipping_address)->floor_no : "" }}
                                                {{ json_decode($order->shipping_address)->apartment ? ", Apartment - ".json_decode($order->shipping_address)->apartment : "" }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                @if ($order->payment_status == "unpaid" && get_setting('bkash') == 1)
                                    <div class="rounded text-center p-2 d-flex justify-content-center border mt-2 align-items-center" role="button" id="bkash_pay" >
                                        <img src="{{ static_asset('assets/img/cards/bkash.png')}}" width="50" class="img-fluid">
                                        <div class="d-block text-center ml-2">
                                            <span class="d-block fw-600 fs-15">{{ translate('Pay with Bkash')}}</span>
                                        </div>
                                    </div>
                                @endif
                                <a href="{{ route('home') }}" class="btn btn-primary fw-600 btn-block w-100 mt-3">
                                    {{ translate('Back to Shopping')}}
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="mb-4 bg-white p-4 rounded shadow-sm">
                        <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Details')}}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Order date')}}:</td>
                                        <td>{{ date('d-m-Y H:i A', $order->date) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Name')}}:</td>
                                        <td>{{ json_decode($order->shipping_address)->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Shipping')}}:</td>
                                        <td>{{ translate('Flat shipping rate')}}</td>
                                    </tr>

                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Order status')}}:</td>
                                        <td>{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Total order amount')}}:</td>
                                        <td>{{ single_price($order->grand_total) }}</td>
                                    </tr>

                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Payment method')}}:</td>
                                        <td>{{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Shipping address')}}:</td>
                                        <td>
                                            {{ json_decode($order->shipping_address)->address }}, {{ json_decode($order->shipping_address)->state }},
                                            Floor No - {{ json_decode($order->shipping_address)->floor_no  ?? "" }}, Apartment - {{ json_decode($order->shipping_address)->apartment ?? "" }}<br/>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div> --}}

                    {{-- <div class="card shadow-sm border-0 rounded">
                        <div class="card-body">
                            <div class="text-center py-4 mb-4">
                                <h2 class="h5">{{ translate('Order Number:')}} <span class="fw-700 text-primary">{{ $order->code }}</span></h2>
                            </div>
                            <div>
                                <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Details')}}</h5>
                                <div>
                                    <table class="table table-responsive-md">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th width="30%">{{ translate('Product')}}</th>
                                                <th>{{ translate('Quantity')}}</th>
                                                <th class="text-right">{{ translate('Price')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($order->orderDetails as $key => $orderDetail)
                                                <tr>
                                                    <td>{{ $key+1 }}</td>
                                                    <td>
                                                        @if ($orderDetail->product != null)
                                                            <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank" class="text-reset">
                                                                {{ $orderDetail->product->getTranslation('name') }}
                                                                @php
                                                                    if($orderDetail->combo_id != null) {
                                                                        $combo = \App\ComboProduct::findOrFail($orderDetail->combo_id);

                                                                        echo '('.$combo->combo_title.')';
                                                                    }
                                                                @endphp
                                                            </a>
                                                        @else
                                                            <strong>{{  translate('Product Unavailable') }}</strong>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $orderDetail->quantity }}
                                                    </td>
                                                    <td class="text-right">{{ single_price($orderDetail->price) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-xl-5 col-md-6 ml-auto mr-0">
                                        <table class="table ">
                                            <tbody>
                                                <tr>
                                                    <th>{{ translate('Subtotal')}}</th>
                                                    <td class="text-right">
                                                        <span class="fw-600">{{ single_price($order->orderDetails->sum('price')) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ translate('Shipping')}}</th>
                                                    <td class="text-right">
                                                        <span class="font-italic">{{ single_price($order->shipping_cost) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>{{ translate('Coupon Discount')}}</th>
                                                    <td class="text-right">
                                                        <span class="font-italic">{{ single_price($order->coupon_discount) }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><span class="fw-600">{{ translate('Total')}}</span></th>
                                                    <td class="text-right">
                                                        <strong><span>{{ single_price($order->grand_total) }}</span></strong>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </section>

    <button id="bKash_button" class="d-none">Pay With bKash</button>

    <div class="payment-backdrop" style="display: none">
        <i class="las la-spinner la-spin la-3x"></i>
    </div>

    <div class="modal" tabindex="-1" id="paymenterror">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Payment Failed</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body text-center">
                <i class="lar la-times-circle la-3x text-danger"></i>
                <p id="bkash-error">Please Try Again</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

@endsection


@section('script')
    @if (get_setting('bkash_sandbox', 1))
        <script src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
    @else
        <script src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
    @endif

    <script type="text/javascript">



        $(document).ready(function(){

            $(document).on("click", "#bkash_pay", function(){

                $(".payment-backdrop").show();

                $('#bKash_button').trigger('click');
                setTimeout(function(){
                    $(".payment-backdrop").hide();
                },5000);

            });

            @if (request("payment_method") == "bkash" && $order->payment_status == "unpaid")

                $(".payment-backdrop").show();

                $('#bKash_button').trigger('click');
                setTimeout(function(){
                    $(".payment-backdrop").hide();
                },5000);

            @endif

        });

        var paymentID = '';
        bKash.init({
            paymentMode: 'checkout', //fixed value ‘checkout’
            //paymentRequest format: {amount: AMOUNT, intent: INTENT}
            //intent options
            //1) ‘sale’ – immediate transaction (2 API calls)
            //2) ‘authorization’ – deferred transaction (3 API calls)
            paymentRequest: {
                amount: '{{ $order->grand_total - $order->reward_discount - $order->coupon_discount }}', //max two decimal points allowed
                intent: 'sale'
            },
            createRequest: function(
            request) { //request object is basically the paymentRequest object, automatically pushed by the script in createRequest method
                $.ajax({
                    url: '{{ route('bkash.checkout') }}',
                    type: 'POST',
                    contentType: 'application/json',
                    success: function(data) {
                        console.log("create payment",data);
                        data = JSON.parse(data);
                        if (data && data.paymentID != null) {
                            paymentID = data.paymentID;
                            bKash.create().onSuccess(
                            data); //pass the whole response data in bKash.create().onSucess() method as a parameter
                        } else {
                            bKash.create().onError();
                        }
                    },
                    error: function() {
                        bKash.create().onError();
                    }
                });
            },
            executeRequestOnAuthorization: function() {
                $.ajax({
                    url: '{{ route('bkash.excecute') }}',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        "paymentID": paymentID
                    }),
                    success: function(data) {
                        // console.log('data: ',data);
                        result = JSON.parse(data);
                        if (result && result.paymentID != null) {
                            window.location.href = "{{ route("order_confirmed", $order->id) }}";
                        } else {
                            bKash.execute().onError();
                        }
                    },
                    error: function(data) {
                        var err = JSON.parse(data.responseText);
                        $("#bkash-error").html(err.errorMessage);
                        $("#paymenterror").modal("show");
                        bKash.execute().onError();
                    }
                });
            },
            onClose : function () {
                window.location.href= "{{ route("order_confirmed", $order->id) }}";
            }
        });
    </script>
@endsection
