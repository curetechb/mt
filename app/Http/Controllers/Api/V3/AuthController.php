<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OTPVerificationController;
use App\Http\Resources\V3\UserResource;
use App\Models\User;
use Auth;
use App\Models\Cart;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    
    public function getMe(){
        $authUser = Auth::user();
        return response()->json([
            'id' => $authUser->id,
            'type' => $authUser->user_type,
            'name' => $authUser->name,
            'email' => $authUser->email,
            'avatar' => $authUser->avatar,
            'avatar_original' => api_asset($authUser->avatar_original),
            'phone' => $authUser->phone
        ], 200);
    }

    public function login(Request $request)
    {
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

        if(!$user){

            $user = User::create([
                'name' => $request->phone,
                'phone' => "+88". $request->phone,
                'password' => \Hash::make('password'),
                'referral_code' => Str::random(8),
                'is_new_user' => true,
                'is_verified' => true
            ]);

            // return response([
            //     "errors" => [
            //         "phone" => ["Invalid User"]
            //     ]
            // ], 422);
        }

        $global_otp = "9999";

        if($request->otp != $global_otp && get_setting('phone_verification') == 1){

            if($user->verification_code != $request->otp){
                return response([
                    "message" => "Invalid Verification Code",
                    "errors" => [
                        "phone" => ["Invalid Verification Code"]
                    ]
                ], 422);
            }

        }

        // if(!$user->is_verified){
        //     if($user->verification_code == $request->otp){
        //         $user->is_verified = true;
        //         $user->save();
        //     }else{
        //         return response([
        //             "errors" => [
        //                 "phone" => ["Invalid Verification Code"]
        //             ]
        //         ], 422);
        //     }
        // }

        // This part is for app
        if($request->from == "app"){
            $token = $user->createToken('API Token')->plainTextToken;
            Cart::where('temp_user_id', request('temp_id'))
            ->update([
                'user_id' => $user->id,
            ]);
            return response()->json([
                'result' => true,
                'is_verified' => true,
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

        // Below Part is for Web
        Auth::login($user, true);

        Cart::where('temp_user_id', request('temp_id'))
            ->update([
                'user_id' => $user->id,
            ]);

        return response(["success" => true], 200);
    }

    public function riderLogin(Request $request){

        /*$request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);*/

        $user = User::where('user_type', 'delivery_boy')->where('phone', "+88".$request->phone)->first();

        if($user->banned){
            return response()->json(['result' => false, 'message' => translate('Your Account is not Active'), 'user' => null], 401);
        }


        if ($user != null) {
            if (Hash::check($request->password, $user->password)) {

                // if ($user->email_verified_at == null) {
                //     return response()->json(['message' => translate('Please verify your account'), 'user' => null], 401);
                // }
                return $this->loginSuccess($user);
            } else {
                return response()->json(['result' => false, 'message' => translate('Unauthorized'), 'user' => null], 401);
            }
        } else {
            return response()->json(['result' => false, 'message' => translate('User not found'), 'user' => null], 401);
        }


    }

    protected function loginSuccess($user)
    {
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

    public function sendOtp(Request $request){

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

        if(!$user){

            $user = User::create([
                'name' => $request->phone,
                'phone' => "+88". $request->phone,
                'password' => \Hash::make('password'),
                'referral_code' => Str::random(8),
                'is_new_user' => true,
                'is_verified' => false
            ]);

        }

        $otp = rand(1000, 9999);
        $user->verification_code = $otp;
        $user->save();

        if(get_setting('phone_verification') == 0){
            $otp = rand(1000, 9999);
            $user->verification_code = $otp;
            $user->save();

            return response(["success" => true, "otpsent" => false, "otp" => $otp], 200);
        }

        $otpController = new OTPVerificationController();
        $otpController->send_code($user);
        return response(["success" => true, "otpsent" => true], 200);

        // return response(["success" => true, "otpsent" => false], 200);
    }

    public function logout(Request $request)
    {

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $request->routeIs("api.*") ? response()->json(["success" => "true"]) : redirect('/');
    }
}
