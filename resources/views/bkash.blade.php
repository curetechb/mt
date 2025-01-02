<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>
<script type="text/javascript">

    var paymentID = '';
    bKash.init({
        paymentMode: 'checkout',
        paymentRequest: {
            amount: '{{ $amount }}',
            intent: 'sale'
        },
        createRequest: function(request) {
            $.ajax({
                url: '/bkash/createpayment',
                type: 'POST',
                contentType: 'application/json',
                success: function(data) {
                    console.log("create payment",data);
                    data = JSON.parse(data);
                    if (data && data.paymentID != null) {
                        paymentID = data.paymentID;
                        bKash.create().onSuccess(
                        data);
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
                url: '/bkash/executepayment',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    "paymentID": paymentID
                }),
                success: function(data) {
                    result = JSON.parse(data);
                    if (result && result.paymentID != null) {
                        window.location.href= "/purchased/{{ $order_id }}";
                    } else {
                        bKash.execute().onError();
                    }
                },
                error: function(data) {
                    var err = JSON.parse(data.responseText);
                    bKash.execute().onError();
                }
            });
        },
        onClose : function () {
            window.location.href= "/purchased/{{ $order_id }}";
        }
    });
</script>
