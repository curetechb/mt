<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyPayment;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Str;
use Session;

class RiderBkashController extends Controller
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

    public function webpage(Request $request)
    {

        $rider_id = request("rider_id");
        $token = $this->grantToken();
        $rider = DeliveryBoy::findOrFail($rider_id);

        return view('backend.rider-bkash.bkash_app', compact('token', 'rider'));
    }

    public function checkout()
    {
        $token = request("token");
        $rider = DeliveryBoy::findOrFail(request('rider_id'));
        $amount = $rider->total_collection;

        $auth = $token;

        $rand = Str::random(3);
        $invoice_number =  "$rand-$rider->id";

        $requestbody = array(
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => "$invoice_number"
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

        $rider = DeliveryBoy::findOrFail($request->rider_id);
        // $amount = $rider->total_collection;

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

            // $rider->total_collection = $rider->total_collection - $amount - $rider->total_earning;
            // $rider->total_earning = 0;
            $rider->total_collection = 0;
            $rider->total_earning = $rider->total_earning + $rider->invisible_earning;
            $rider->invisible_earning = 0;
            $rider->update();

        }

        return $resultdata;
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
