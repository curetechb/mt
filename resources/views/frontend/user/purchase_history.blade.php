@extends('frontend.layouts.user_panel')

@section('panel_content')



<div class="container">
    <div class="row">
        <div class="col-lg-5">
                <h2 class="mb-3">{{ translate('Purchase History') }}</h2>
                @php
                    $badges = [
                        "pending" => "badge-info",
                        "processing" => "badge-info",
                        "on_the_way" => "badge-warning",
                        "payment_received" => "badge-primary",
                        "delivered" => "badge-success",
                        "cancelled" => "badge-danger",
                    ]
                @endphp

                @foreach($orders as $key => $order)
                    @if (count($order->orderDetails) > 0)
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-2">
                                    <span class="badge badge-inline {{ $badges[$order->delivery_status] }}">
                                        {{ $order->delivery_status }}
                                    </span>
                                </div>
                                <h6>Order #{{ $order->code }}</h6>
                                <div class="d-flex justify-content-between">
                                    <h6 class="float:left">
                                        <span>{{ single_price($order->grand_total - $order->coupon_discount - $order->reward_discount) }}</span>
                                        @if ($order->payment_status == "paid")
                                            <span class="text-success">({{ $order->payment_status }})</span>
                                        @else
                                            <span class="text-danger">({{ $order->payment_status }})</span>
                                        @endif
                                    </h6>
                                    <h6>{{ $order->created_at->format("d-M-Y h:iA") }}</h6>
                                </div>
                            </div>
                            <a href="#" data-id="{{ $order->id }}" class="w-100 mt-2 order-button">
                                <span>
                                    <i class="las la-chevron-circle-down mr-1" style="font-size: 20px"></i>
                                    <span class="span-text">View Details</span>
                                </span>
                            </a>

                            <div class="order-{{$order->id}}-details-show details-under-order d-lg-none">

                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="d-flex justify-content-center">
                    {{ $orders->links() }}
                </div>
        </div>

        <div class="col-lg-6 d-none d-lg-block">
            <div class="text-center mt-5 od-loader" style="display: none">
                <i class="las la-spinner la-spin la-3x opacity-70"></i>
            </div>
            <h2 class="mb-3 purchase-details-h2 d-none">{{ translate('Purchase Details') }}</h2>
           <div class="order-details-show">

           </div>
        </div>
    </div>
</div>

@endsection



@section('modal')
@include('modals.delete_modal')

<div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div id="order-details-modal-body">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('userorder.cancel') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Cancel Reason</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" name="reason" id="" cols="30" rows="5" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="cancel_order_id" id="cancel_order_id" value="">
                        <button type="submit" class="btn btn-info">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


<div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div id="payment_modal_body">

            </div>
        </div>
    </div>
</div>

<button id="bKash_button" class="d-none">Pay With bKash</button>
<div class="payment-backdrop" style="display: none">
    <i class="las la-spinner la-spin la-3x"></i>
</div>

<input type="hidden" name='bkash_amount' id="bkash_amount">

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
<script type="text/javascript">

    $(document).ready(function() {

        $(".order-button").click(function(e) {
            e.preventDefault();

            var id = $(this).data("id");
            let mobclass = `.order-${id}-details-show`; // mobile

            const btntext = $(this).text().trim();
            if(btntext == "Hide Details"){

                $(".order-details-show").html(null);
                $(mobclass).html(null); // mobile

                $(this).find(".span-text").html("View Details");
                $(this).find("i").removeClass("la-chevron-circle-up");
                $(this).find("i").addClass("la-chevron-circle-down");
                $(".purchase-details-h2").removeClass("d-block");
                $(".purchase-details-h2").addClass("d-none");
                return false;
            }

            $(".details-under-order").hide(); // mobile'

            $(".order-details-show").html(null);
            $(".od-loader").show();


            const thisobj = $(this);

            $.ajax({
                url: "/purchase_history/details",
                type: 'POST',
                data: { _token: "{{ csrf_token() }}", order_id: id},
                success: function(res) {
                    $(".od-loader").hide();
                    $(".order-details-show").html(res);
                    $(mobclass).html(res); // mobile
                    $(mobclass).show(); // mobile

                    $(".purchase-details-h2").removeClass("d-none");
                    $(".purchase-details-h2").addClass("d-block");


                    $(".order-button").find(".span-text").html("View Details");
                    $(".order-button").find("i").removeClass("la-chevron-circle-up");
                    $(".order-button").find("i").addClass("la-chevron-circle-down");

                    thisobj.find(".span-text").html("Hide Details");
                    thisobj.find("i").removeClass("la-chevron-circle-down");
                    thisobj.find("i").addClass("la-chevron-circle-up");
                }
            });
        });

    });
</script>


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
                }, 4000);

            });



        });

        var paymentID = '';
        var bkashObject = {
            paymentMode: 'checkout', //fixed value ‘checkout’
            //paymentRequest format: {amount: AMOUNT, intent: INTENT}
            //intent options
            //1) ‘sale’ – immediate transaction (2 API calls)
            //2) ‘authorization’ – deferred transaction (3 API calls)
            paymentRequest: {
                amount: '', //max two decimal points allowed
                intent: 'sale'
            },
            createRequest: function(request) {
                //request object is basically the paymentRequest object, automatically pushed by the script in createRequest method
                bkashObject.paymentRequest.amount = $("#bkash_pay").data('total');
                $.ajax({
                    url: '{{ route('bkash.checkout') }}',
                    type: 'POST',
                    contentType: 'application/json',
                    success: function(data) {
                        //console.log(data);
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
                        //console.log(data);
                        result = JSON.parse(data);
                        if (result && result.paymentID != null) {
                            window.location.reload();
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
                window.location.reload();
            }
        };
        bKash.init(bkashObject);
    </script>


<script>
    $(document).ready(function(){


        $(document).on("click", "#cancelorderbtn",function(){

            var id = $(this).data("id");
            $("#cancel_order_id").val(id);
            $('#exampleModal').modal('show');

        });

    });
</script>

{{-- <script type="text/javascript">

    // Set the date we're counting down to
    var countDownDate = parseInt(@json($order->date)) + 1800;
    // Update the count down every 1 second
    var x = setInterval(function() {
        // Get today's date and time
        var now = Math.floor(new Date().getTime() / 1000);
        // Find the distance between now and the count down date
        var distance = countDownDate - now;
        // Time calculations for days, hours, minutes and seconds
        var minutes = Math.floor(distance / 60);
        var seconds = Math.floor(distance - minutes * 60);
        // Output the result in an element with id="demo"
        document.getElementById("ordercanceltimer").innerHTML = minutes + "m " + seconds + "s ";
        // If the count down is over, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("ordercanceltimer").parentElement.style = "display: none";
        }
    }, 1000);
</script> --}}


@endsection
