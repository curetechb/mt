<button id="bKash_button" class="d-none">Pay With bKash</button>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="https://scripts.pay.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout.js"></script>

<script type="text/javascript">

    $(document).ready(function(){
        $('#bKash_button').trigger('click');
    });

    var paymentID = '';
    bKash.init({
        paymentMode: 'checkout', //fixed value ‘checkout’
        paymentRequest: {
            amount: '50', //max two decimal points allowed
            intent: 'sale'
        },
        createRequest: function(request) { //request object is basically the paymentRequest object, automatically pushed by the script in createRequest method
            $.ajax({
                url: '/bkash/createpayment',
                type: 'POST',
                contentType: 'application/json',
                success: function(data) {
                    //console.log(data);
                    data = JSON.parse(data);
                    if (data && data.paymentID != null) {
                        paymentID = data.paymentID;
                        bKash.create().onSuccess(data); //pass the whole response data in bKash.create().onSucess() method as a parameter
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
                    //console.log(data);
                    result = JSON.parse(data);
                    if (result && result.paymentID != null) {
                        window.location.href = "google.com"; //Merchant’s success page
                    } else {
                        bKash.execute().onError();
                    }
                },
                error: function() {
                    bKash.execute().onError();
                }
            });
        }
    });
</script>
