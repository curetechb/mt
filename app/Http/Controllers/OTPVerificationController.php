<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Auth;
use Nexmo;
use App\Models\OtpConfiguration;
use App\Models\User;
use App\Utility\SmsUtility;
use Twilio\Rest\Client;
use Hash;
use Session;

class OTPVerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function verification(Request $request){
        if (Auth::check() && Auth::user()->email_verified_at == null) {
            return view('otp_systems.frontend.user_verification');
        }
        else {
            Session::flash("error",translate('You have already verified your number'));
            return redirect()->route('home');
        }
    }


    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function verify_phone(Request $request){
        $user = Auth::user();
        if ($user->verification_code == $request->verification_code) {
            $user->email_verified_at = date('Y-m-d h:m:s');
            $user->save();

            Session::flash("success",translate('Your phone number has been verified successfully'));
            return redirect()->route('home');
        }
        else{
            Session::flash("error",'Invalid Code');
            return back();
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function resend_verificcation_code(Request $request){
        $user = Auth::user();
        $user->verification_code = rand(100000,999999);
        $user->save();
        SmsUtility::phone_number_verification($user);

        return back();
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function reset_password_with_code(Request $request){
        if (($user = User::where('phone', $request->phone)->where('verification_code', $request->code)->first()) != null) {
            if($request->password == $request->password_confirmation){
                $user->password = Hash::make($request->password);
                $user->email_verified_at = date('Y-m-d h:m:s');
                $user->save();
                event(new PasswordReset($user));
                auth()->login($user, true);

                if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')
                {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('home');
            }
            else {
                Session::flash("success","Password and confirm password didn't match");
                return back();
            }
        }
        else {
            Session::flash("error","Verification code mismatch");
            return back();
        }
    }

    /**
     * @param  User $user
     * @return void
     */

    public function send_code($user){
        SmsUtility::phone_number_verification($user);
    }

    /**
     * @param  Order $order
     * @return void
     */
    public function send_order_code($order){
        // $phone = json_decode($order->shipping_address)->phone;
        $phone = Auth::user()->phone;
        if($phone != null){
            SmsUtility::order_placement($phone, $order);
        }
    }

    /**
     * @param  Order $order
     * @return void
     */
    public function send_delivery_status($order){
        $phone = json_decode($order->shipping_address)->phone;
        if($phone != null){
            SmsUtility::delivery_status_change($phone, $order);
        }
    }

    /**
     * @param  Order $order
     * @return void
     */
    public function send_payment_status($order){
        $phone = json_decode($order->shipping_address)->phone;
        if($phone != null){
            SmsUtility::payment_status_change($phone, $order);
        }
    }
}
