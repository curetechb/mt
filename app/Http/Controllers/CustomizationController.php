<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceEmailManager;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use App\Models\Cart;
use App\Models\Order;
use Mail;
use Session;

class CustomizationController extends Controller
{
    public function phoneVerification(){

    }

    public function verifyPhone(Request $request){

        $this->validate($request, [
            "phone" => ["required", "min:11", "max: 11","regex:/(01)[0-9]{9}/"]
        ],
        [
            'phone.required' => 'The phone number is required!',
            'phone.min' => 'The phone number must be 11 digits',
            'phone.max' => 'The phone number must be 11 digits',
            'phone.regex' => 'Invalid phone number'
        ]);

        $user = User::where("phone", "+88$request->phone")->first();
        if($user && $user->is_verified == true){
            Auth::login($user, true);
            if (session('temp_user_id') != null) {
                Cart::where('temp_user_id', session('temp_user_id'))
                    ->update([
                        'user_id' => auth()->user()->id,
                        // 'temp_user_id' => null
                    ]);

                Session::forget('temp_user_id');
            }
            return response(["verified" => true], 200);
        }

        if(get_setting('phone_verification') == 0){

            $user = User::create([
                'name' => $request->phone,
                'phone' => "+88". $request->phone,
                'password' => \Hash::make('password'),
                'is_verified' => true
            ]);

            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->save();

            Auth::login($user, true);
            if (session('temp_user_id') != null) {
                Cart::where('temp_user_id', session('temp_user_id'))
                    ->update([
                        'user_id' => auth()->user()->id,
                        // 'temp_user_id' => null
                    ]);

                Session::forget('temp_user_id');
            }
            return response(["verified" => true], 200);

        }

        if($user && isset($request->otp)){

            if($user->verification_code == $request->otp){

                if($request->routeIs("api.*")){
                    $token = $user->createToken('API Token')->plainTextToken;
                    return response()->json([
                        'result' => true,
                        'message' => translate('Successfully logged in'),
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                        'expires_at' => null,
                        'user' => [
                            'id' => $user->id,
                            'type' => $user->user_type,
                            'name' => $user->name,
                            'email' => $user->email,
                            'avatar' => $user->avatar,
                            'avatar_original' => api_asset($user->avatar_original),
                            'phone' => $user->phone
                        ]
                    ]);
                }

                $user->is_verified = true;
                $user->save();
                Auth::login($user, true);

                if (session('temp_user_id') != null) {
                    Cart::where('temp_user_id', session('temp_user_id'))
                        ->update([
                            'user_id' => auth()->user()->id,
                            // 'temp_user_id' => null
                        ]);

                    Session::forget('temp_user_id');
                }

                return response(["verified" => true], 200);
            }else{
                return response([
                    "verified" => false,
                    "errors" => [
                        "phone" => ["Invalid Verification Code"]
                    ]
                ], 422);
            }

        }

        if(!$user){

            $user = User::create([
                'name' => $request->phone,
                'phone' => "+88". $request->phone,
                'password' => \Hash::make('password'),
                'is_verified' => false
            ]);

            $customer = new Customer();
            $customer->user_id = $user->id;
            $customer->save();

        }

        $otp = rand(1000, 9999);
        $user->verification_code = $otp;
        $user->save();

        $otpController = new OTPVerificationController();
        $otpController->send_code($user);
        return response(["otpsent" => true], 200);

    }


    public function mailInvoice(Request $request){

        $email = $request->email;
        $user = Auth::user();
        $user->email = $email;
        $user->save();

        $order = Order::find(session('order_id'));
        $array['view'] = 'emails.invoice';
        $array['subject'] = translate('A new order has been placed') . ' - ' . $order->code;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['order'] = $order;
        Mail::to($order->user->email)->queue(new InvoiceEmailManager($array));

        return response(["success" => true]);
    }
}
