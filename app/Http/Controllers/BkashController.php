<?php

namespace App\Http\Controllers;

use App\Models\BkashToken;
use Illuminate\Http\Request;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Models\CombinedOrder;
use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\Seller;
use Carbon\Carbon;
use Session;

class BkashController extends Controller
{
    private $base_url;
    public function __construct()
    {
        if(get_setting('bkash_sandbox', 1)){
            $this->base_url = "https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/";
        }
        else {
            $this->base_url = "https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/";
        }
    }

    // public function pay(){
    //     $amount = 0;
    //     if(Session::has('payment_type')){
    //         if(Session::get('payment_type') == 'cart_payment'){
    //             $order = Order::findOrFail(Session::get('order_id'));
    //             $amount = round($order->grand_total);
    //         }
    //         elseif (Session::get('payment_type') == 'wallet_payment') {
    //             $amount = round(Session::get('payment_data')['amount']);
    //         }
    //         elseif (Session::get('payment_type') == 'customer_package_payment') {
    //             $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
    //             $amount = round($customer_package->amount);
    //         }
    //         elseif (Session::get('payment_type') == 'seller_package_payment') {
    //             $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
    //             $amount = round($seller_package->amount);
    //         }
    //     }

    //     $request_data = array('app_key'=> env('BKASH_CHECKOUT_APP_KEY'), 'app_secret'=>env('BKASH_CHECKOUT_APP_SECRET'));

    //     $url = curl_init($this->base_url.'checkout/token/grant');
    //     $request_data_json=json_encode($request_data);

    //     $header = array(
    //         'Content-Type:application/json',
    //         'username:'.env('BKASH_CHECKOUT_USER_NAME'),
    //         'password:'.env('BKASH_CHECKOUT_PASSWORD')
    //     );
    //     curl_setopt($url,CURLOPT_HTTPHEADER, $header);
    //     curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
    //     curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($url,CURLOPT_POSTFIELDS, $request_data_json);
    //     curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
    //     curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

    //     $resultdata = curl_exec($url);
    //     curl_close($url);

    //     $token = json_decode($resultdata)->id_token;

    //     Session::put('bkash_token', $token);
    //     Session::put('payment_amount', $amount);

    //     return view('frontend.bkash.index');
    // }

    public function refreshToken($bkashtoken){

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
    }

    public function grantToken(){

        $bkashtoken = BkashToken::first();

        if($bkashtoken){
            if(Carbon::now()->lt($bkashtoken->expires_at)){
                return $bkashtoken->id_token;
            }else{
                return $this->refreshToken($bkashtoken);
            }
        }

        if(!$bkashtoken){
            $bkashtoken = new BkashToken();
        }

        $request_data = array('app_key' => env('BKASH_CHECKOUT_APP_KEY'), 'app_secret' => env('BKASH_CHECKOUT_APP_SECRET'));

        $url = curl_init($this->base_url.'checkout/token/grant');
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
    }

    public function checkout(Request $request){

        $token = $this->grantToken();

        $order_id = session('order_id'); // get order id from session

        $order = Order::findOrFail($order_id);
        $amount = $order->grand_total - $order->coupon_discount - $order->reward_discount;
        $requestbody = array(
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => "$order->code"
        );

        $url = curl_init($this->base_url.'checkout/payment/create');
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
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }

    public function excecute(Request $request){

        $paymentID = $request->paymentID;

        $auth = $this->grantToken();

        $order_id = session('order_id'); // get order id from session

        $order = Order::findOrFail($order_id);

        $url = curl_init($this->base_url.'checkout/payment/execute/'.$paymentID);
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:'.env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);

        $data = json_decode($resultdata);

        if(isset($data->transactionStatus) && $data->transactionStatus == "Completed"){
            Session::flash("success",translate('Your Payment is Successful'));
            $order->payment_status = "paid";
            $order->payment_type = "bkash";
            $order->payment_details = $resultdata;
            $order->update();
            return $resultdata;
        }else{
            return response($resultdata, 422);
        }

    }

    // public function success(Request $request){

    //     $payment_type = Session::get('payment_type');

    //     if ($payment_type == 'cart_payment') {
    //         $checkoutController = new CheckoutController();
    //         return $checkoutController->checkout_done(Session::get('order_id'), $request->payment_details);
    //     }


    // }


    public function refund(Request $request){


        $order_id = $request->order_id;

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
            Session::flash("success", translate('Refunded Successfully'));
        }else{
            Session::flash("error", translate('Failed to Refund'));
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
