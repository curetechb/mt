@extends('backend.layouts.app')

@section('content')
<div>
    <h5 class="h6">{{ translate('Payment Details') }}</h5>
</div>

<div class="card">
    <div class="card-header">
        Payment Info
    </div>
    <div class="card-body">
        <table>
            <tbody>
                <tr>
                    <td class="text-main text-bold">{{ translate('Rider name') }}</td>
                    <td class="px-2"></td>
                    <td class="fw-600">{{ $payment->rider->user->name ?? "" }}</td>
                </tr>
                <tr>
                    <td class="text-main text-bold">{{ translate('Requested Amount') }}</td>
                    <td class="px-2"></td>
                    <td class="fw-600">{{ single_price($payment->amount) }}</td>
                </tr>
                <tr>
                    <td class="text-main text-bold">{{ translate('Status') }}</td>
                    <td class="px-2"></td>
                    <td class="fw-600 badge badge-inline bg-info">{{ $payment->status }}</td>
                </tr>
                @if ($payment->reject_reason)
                    <tr>
                        <td class="text-main text-bold">{{ translate('Reject Reason') }}</td>
                        <td class="px-2"></td>
                        <td class="fw-600">{{ $payment->reject_reason }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if ($payment->status != "paid")
            {{-- <div class="rounded text-center p-2 d-flex justify-content-center border mt-2 align-items-center" role="button" id="bkash_pay" >
                <img src="{{ static_asset('assets/img/cards/bkash.png')}}" width="50" class="img-fluid">
                <div class="d-block text-center ml-2">
                    <span class="d-block fw-600 fs-15">{{ translate('Pay with Bkash')}}</span>
                </div>
            </div> --}}
            <a href="{{ route("rider.payment.paid", $payment->id) }}" class="btn btn-primary">Mark as Paid</a>
        @endif
    </div>
</div>

<div>
    <h5 class="h6">{{ translate('Collection') }}</h5>
</div>
<div class="card">
    <div class="card-header">Collection</div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>{{ translate('Order Code') }}</th>
                    <th data-breakpoints="md">{{ translate('Rider') }}</th>
                    <th data-breakpoints="md">{{ translate('Collection Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Commision Amount') }}</th>
                    <th data-breakpoints="md">{{ translate('Collection Time') }}</th>
                    {{-- <th class="text-right" width="15%">{{translate('options')}}</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($histories as $key => $history)
                <tr>

                    <td>
                       #{{ $history->order->code ?? "" }}
                    </td>
                    <td>
                        {{ $history->rider->user->name ?? "" }}
                    </td>

                    <td>
                        {{ single_price($history->collection) }}
                    </td>

                    <td>
                        {{ single_price($history->earning) }}
                    </td>

                    <td>
                        {{ $history->created_at->format("d-m-Y h:iA") }}

                    </td>

                    {{-- <td class="text-right">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('all_orders.show', encrypt($order->id))}}" title="{{ translate('View') }}">
                            <i class="las la-eye"></i>
                        </a>
                    </td> --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<button id="bKash_button" class="d-none">Pay With bKash</button>

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


        });
    </script>


    <script type="text/javascript">



        var paymentID = '';
        bKash.init({
            paymentMode: 'checkout', //fixed value ‘checkout’
            //paymentRequest format: {amount: AMOUNT, intent: INTENT}
            //intent options
            //1) ‘sale’ – immediate transaction (2 API calls)
            //2) ‘authorization’ – deferred transaction (3 API calls)
            paymentRequest: {
                amount: '{{ $payment->amount }}', //max two decimal points allowed
                intent: 'sale'
            },
            createRequest: function(
            request) { //request object is basically the paymentRequest object, automatically pushed by the script in createRequest method
                $.ajax({
                    url: '{{ route('rider.bkash.payment.checkout') }}',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        "rider_payment_id": "{{ $payment->id }}"
                    }),
                    success: function(data) {
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
                    url: '{{ route('rider.bkash.payment.execute') }}',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        "paymentID": paymentID,
                        "rider_payment_id": "{{ $payment->id }}"
                    }),
                    success: function(data) {
                        // console.log('data: ',data);
                        result = JSON.parse(data);
                        if (result && result.paymentID != null) {
                            window.location.href= "{{ route("rider.payment.show", $payment->id) }}";
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
                window.location.href= "{{ route("rider.payment.show", $payment->id) }}";
            }
        });
    </script>
@endsection
