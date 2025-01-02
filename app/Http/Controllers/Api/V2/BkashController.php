<?php


namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Models\Order;
use Session;

class BkashController extends Controller
{

    public function grantToken()
    {

        $request_data = array('app_key'=> env('BKASH_CHECKOUT_APP_KEY'), 'app_secret'=>env('BKASH_CHECKOUT_APP_SECRET'));

        if(get_setting('bkash_sandbox', 1)){
            $url = curl_init("https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/token/grant");
        }else{
            $url = curl_init("https://checkout.pay.bka.sh/v1.2.0-beta/checkout/token/grant");
        }
        $request_data_json=json_encode($request_data);

        $header = array(
            'Content-Type:application/json',
            'username:'.env('BKASH_CHECKOUT_USER_NAME'),
            'password:'.env('BKASH_CHECKOUT_PASSWORD')
        );
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        $token = json_decode($resultdata)->id_token;

        return $token;


    }

    public function webpage($order_id)
    {

        $token = $this->grantToken();
        $order = Order::findOrFail($order_id);

        return view('frontend.payment.bkash_app', compact('token', 'order'));
    }

    public function checkout()
    {
        $token = request("token");
        $order_id = request('order_id');

        $auth = $token;

        $callbackURL = route('home');
        $order = Order::findOrFail($order_id);

        $amount = $order->grand_total - $order->coupon_discount - $order->reward_discount;

        $requestbody = array(
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => "$order->code"
        );

        if(get_setting('bkash_sandbox', 1)){
            $url = curl_init("https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/create");
        }else{
            $url = curl_init("https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/create");
        }
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }

    public function execute(Request $request)
    {

        $token = $request->token;
        $paymentID = $request->paymentID;
        $order = Order::findOrFail($request->order_id);

        if(get_setting('bkash_sandbox', 1)){
            $url = curl_init("https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/execute/". $paymentID);
        }else{
            $url = curl_init("https://checkout.pay.bka.sh/v1.2.0-beta/checkout/payment/execute/". $paymentID);
        }

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $token,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);

        $data = json_decode($resultdata);

        if(isset($data->transactionStatus) && $data->transactionStatus == "Completed"){
            Session::flash("success",translate('Your Payment is Successful'));
            $order->payment_status = "paid";
            $order->payment_type = "bkash";
            $order->payment_details = $resultdata;
            $order->update();
        }

        return $resultdata;
    }

    public function process(Request $request)
    {
        try {

            $payment_type = $request->payment_type;

            if ($payment_type == 'cart_payment') {

                checkout_done($request->combined_order_id, $request->payment_details);
            }

            if ($payment_type == 'wallet_payment') {

                wallet_payment_done($request->user_id, $request->amount, 'Bkash', $request->payment_details);
            }

            return response()->json(['result' => true, 'message' => translate("Payment is successful")]);


        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }

    public function success(Request $request)
    {
        return response()->json([
            'result' => true,
            'message' => translate('Payment Success'),
            'payment_details' => $request->payment_details
        ]);

    }

    public function fail(Request $request)
    {
        return response()->json([
            'result' => false,
            'message' => translate('Payment Failed'),
            'payment_details' => $request->payment_details
        ]);
    }

}

