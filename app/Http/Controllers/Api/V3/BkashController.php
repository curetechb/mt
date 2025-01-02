<?php

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Controllers\Controller;
use App\Models\BkashToken;
use Carbon\Carbon;
use Session;
use URL;

class BkashController extends Controller
{
    private $base_url = "https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/";

    // public function __construct()
    // {
    //     if(get_setting('bkash_sandbox', 1)){
    //         $this->base_url = "https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/";
    //     }
    //     else {
    //         $this->base_url = "https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/";
    //     }
    // }

    public function refreshToken($bkashtoken){

        try{
            $request_data = array(
                'app_key' => env('BKASH_CHECKOUT_APP_KEY'), 
                'app_secret' => env('BKASH_CHECKOUT_APP_SECRET'),
                'refresh_token' => "$bkashtoken->refresh_token"
            );
    
            $url = curl_init($this->base_url.'checkout/token/refresh');
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
            $refresh_token = json_decode($resultdata)->refresh_token;
            $expires_in = json_decode($resultdata)->expires_in;
            
            $bkashtoken->id_token = $token;
            $bkashtoken->refresh_token = $refresh_token;
            $bkashtoken->expires_at = Carbon::now()->addSeconds($expires_in);
            $bkashtoken->save();
    
            return $token;
        }catch(\Exception $e){
            return null;
        }
    }

    public function grantToken(){

        $bkashtoken = BkashToken::first();

        if($bkashtoken){
            if(Carbon::now()->lt($bkashtoken->expires_at)){
                return $bkashtoken->id_token;
            }else{
                $ref_token = $this->refreshToken($bkashtoken);
                if($ref_token) return $ref_token;
            }
        }

        if(!$bkashtoken){
            $bkashtoken = new BkashToken();
        }

        $request_data = array('app_key'=> env('BKASH_CHECKOUT_APP_KEY'), 'app_secret'=>env('BKASH_CHECKOUT_APP_SECRET'));
        $request_data_json=json_encode($request_data);

        $header = array(
                'Content-Type:application/json',
                'username:'.env('BKASH_CHECKOUT_USER_NAME'),
                'password:'.env('BKASH_CHECKOUT_PASSWORD')
                );

        $url = curl_init($this->base_url.'checkout/token/grant');
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        $token = json_decode($resultdata)->id_token;
        $refresh_token = json_decode($resultdata)->refresh_token;
        $expires_in = json_decode($resultdata)->expires_in;
        
        $bkashtoken->id_token = $token;
        $bkashtoken->refresh_token = $refresh_token;
        $bkashtoken->expires_at = Carbon::now()->addSeconds($expires_in);
        $bkashtoken->save();

        return $token;
    }

    public function checkout(Request $request){

        $token = $this->grantToken();

        $order_id =  $request->order_id; // get order id from session

        // $order_id = 2191;

        $order = Order::findOrFail($order_id);
        $amount = $order->grand_total - $order->coupon_discount - $order->reward_discount;

        // $website_url = URL::to("/");

        $requestbody = array(
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => "$order->code",
            'mode' => '0011',
            'payerReference' => ' ',
            'callbackURL' => route('bkash.callback', ['order_id' => $order_id]),
        );

        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $token,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
        );

        $url = curl_init($this->base_url.'checkout/create');
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return json_decode($resultdata);
        // return redirect((json_decode($resultdata)->bkashURL));
    }

    public function callback(Request $request)
    {

        $allRequest = $request->all();

        $website_url = env("FRONTEND_URL", "https://muslim.town");

        if(isset($allRequest['status']) && $allRequest['status'] == 'failure'){

            return redirect()->to("$website_url/purchased/".$allRequest['order_id']."?status=failure&error_message=Payment Failed");

        }else if(isset($allRequest['status']) && $allRequest['status'] == 'cancel'){
            return redirect()->to("$website_url/purchased/".$allRequest['order_id']."?status=cancel&error_message=Payment Cancelled");
        }else{

            $resultdata = $this->execute($allRequest['paymentID']);
            $result_data_array = json_decode($resultdata,true);

            if(array_key_exists("statusCode",$result_data_array) && $result_data_array['statusCode'] != '0000'){
                return redirect()->to("$website_url/purchased/".$allRequest['order_id']."?status=validation_error&error_message=".$result_data_array['statusMessage']);
            }else if(array_key_exists("message",$result_data_array)){
                // if execute api failed to response
                sleep(1);
                return redirect()->to("$website_url/purchased/".$allRequest['order_id']."?status=failure&error_message=Payment Failed");

            }


            $order = Order::findOrFail($allRequest['order_id']);
            $order->payment_status = "paid";
            $order->payment_type = "bkash";
            $order->payment_details = $resultdata;
            $order->update();
            return redirect()->to("$website_url/purchased/".$allRequest['order_id']."?status=success&success_message=Payment Successful");

        }

    }



    public function execute($paymentID){

        $auth = $this->grantToken();

        $requestbody = array(
            'paymentID' => $paymentID
        );
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
        );

        $url = curl_init($this->base_url.'checkout/execute');
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

    // public function excecute(Request $request){

    //     $paymentID = $request->paymentID;

    //     $auth = $this->grantToken();

    //     $order_id = session('order_id'); // get order id from session

    //     // $order_id = 2191;

    //     $order = Order::findOrFail($order_id);

    //     $url = curl_init($this->base_url.'/checkout/execute/'.$paymentID);
    //     $header = array(
    //         'Content-Type:application/json',
    //         'Authorization:' . $auth,
    //         'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
    //     );

    //     curl_setopt($url,CURLOPT_HTTPHEADER, $header);
    //     curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
    //     curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
    //     $resultdata = curl_exec($url);
    //     curl_close($url);

    //     $data = json_decode($resultdata);

    //     if(isset($data->transactionStatus) && $data->transactionStatus == "Completed"){
    //         $order->payment_status = "paid";
    //         $order->payment_type = "bkash";
    //         $order->payment_details = $resultdata;
    //         $order->update();
    //         return $resultdata;
    //     }else{
    //         return response($resultdata, 422);
    //     }

    // }

    // public function success(Request $request){

    //     $payment_type = Session::get('payment_type');

    //     if ($payment_type == 'cart_payment') {
    //         $checkoutController = new CheckoutController();
    //         return $checkoutController->checkout_done(Session::get('order_id'), $request->payment_details);
    //     }


    // }


    public function refund(Request $request){

        $order_id = session("order_id");

        $order = Order::findOrFail($order_id);

        $payment_details = json_decode($order->payment_details);

        $token = $this->grantToken();

        $requestbody = array(
            'amount' => $request->amount,
            'paymentID' => $payment_details->paymentID,
            'trxID' => $payment_details->trxID,
            'sku' => $order->code,
            'reason' => $request->reason
        );

        $url = curl_init($this->base_url.'checkout/payment/refund');
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $token,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($url, CURLOPT_TIMEOUT, 60);
        $resultdata = curl_exec($url);
        curl_close($url);

        $data = json_decode($resultdata);
        if(isset($data->transactionStatus) && $data->transactionStatus == "Completed"){
            $order->refund_details = $resultdata;
            $order->payment_status = "refunded";
            $order->save();
            Session::flash("success",translate('Refunded Successfully'));
        }else{
            Session::flash("error",translate('Failed to Refund'));
        }

        return redirect()->back();

    }


    public function refundDetails(){


        $token = $this->grantToken();

        $order = Order::findOrFail(request()->order_id);

        $payment_details = json_decode($order->payment_details);

        $requestbody = array(
            'paymentID' => $payment_details->paymentID,
            'trxID' => $payment_details->trxID,
        );

        $url = curl_init($this->base_url.'checkout/payment/refund');
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $token,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($url, CURLOPT_TIMEOUT, 60);
        $resultdata = curl_exec($url);
        curl_close($url);

        $data = json_decode($resultdata);

        if(isset($data->transactionStatus) && $data->transactionStatus == "Completed"){
            return $data;
        }else{
            return response("Failed to Fetch", 422);
        }
    }


}





