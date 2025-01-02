<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\OTPVerificationController;
use App\Mail\InvoiceEmailManager;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use App\Models\Cart;
use App\Models\Order;
use Mail;
use Session;
use App\Models\Page;
use Illuminate\Support\Str;

class ApiCustomizationController extends Controller
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

        if(!$user){
            $user = User::create([
                'name' => $request->phone,
                'phone' => "+88". $request->phone,
                'password' => \Hash::make('password'),
                'is_verified' => true
            ]);
        }

        // if($user && $user->is_verified == true){

        //     $token = $user->createToken('API Token')->plainTextToken;
        //     return response()->json([
        //         'result' => true,
        //         'is_verified' => true,
        //         'message' => translate('Successfully logged in'),
        //         'access_token' => $token,
        //         'token_type' => 'Bearer',
        //         'expires_at' => null,
        //         'user' => [
        //             'id' => $user->id,
        //             'type' => $user->user_type,
        //             'name' => $user->name,
        //             'email' => $user->email,
        //             'avatar' => $user->avatar,
        //             'avatar_original' => api_asset($user->avatar_original),
        //             'phone' => $user->phone
        //         ]
        //     ]);

        // }


        // if(get_setting('phone_verification') == 0){

        //     $user = User::create([
        //         'name' => $request->phone,
        //         'phone' => "+88". $request->phone,
        //         'password' => \Hash::make('password'),
        //         'is_verified' => true
        //     ]);

        //     // $customer = new Customer();
        //     // $customer->user_id = $user->id;
        //     // $customer->save();

        //     $token = $user->createToken('API Token')->plainTextToken;
        //     return response()->json([
        //         'result' => true,
        //         'is_verified' => true,
        //         'message' => translate('Successfully logged in'),
        //         'access_token' => $token,
        //         'token_type' => 'Bearer',
        //         'expires_at' => null,
        //         'user' => [
        //             'id' => $user->id,
        //             'type' => $user->user_type,
        //             'name' => $user->name,
        //             'email' => $user->email,
        //             'avatar' => $user->avatar,
        //             'avatar_original' => api_asset($user->avatar_original),
        //             'phone' => $user->phone
        //         ]
        //     ]);

        // }

        if($user && isset($request->otp)){

            if($user->verification_code == $request->otp){

                $user->is_verified = true;
                $user->save();
                $token = $user->createToken('API Token')->plainTextToken;
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
            }else{
                return response(["verified" => false, "message" => "Invalid Verification Code"], 422);
            }

        }


        if(!$user){

            $user = User::create([
                'name' => $request->phone,
                'phone' => "+88". $request->phone,
                'referral_code' => Str::random(8),
                'is_new_user' => true,
                'password' => \Hash::make('password'),
                'is_verified' => false
            ]);

            // $customer = new Customer();
            // $customer->user_id = $user->id;
            // $customer->save();

        }

        $otp = rand(1000, 9999);
        $user->verification_code = $otp;
        $user->save();

        $otpController = new OTPVerificationController();
        $otpController->send_code($user);

        return response([
            "otpsent" => true,
            'result' => true,
            'is_verified' => false,
            'message' => translate('Otp Sent Successfully'),
            'access_token' => null,
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
        ], 200);

    }


    // public function mailInvoice(Request $request){

    //     $email = $request->email;
    //     $user = Auth::user();
    //     $user->email = $email;
    //     $user->save();

    //     $order = Order::find(session('order_id'));
    //     $array['view'] = 'emails.invoice';
    //     $array['subject'] = translate('A new order has been placed') . ' - ' . $order->code;
    //     $array['from'] = env('MAIL_FROM_ADDRESS');
    //     $array['order'] = $order;
    //     Mail::to($order->user->email)->queue(new InvoiceEmailManager($array));

    //     return response(["success" => true]);
    // }

     public function faq(){
        $page =  Page::where('slug', 'faq')->first();
         return response([
            'result' => true,
            'content' => $page
        ], 200);
    }

    public function privacyPolicy(){
        $page_en =  Page::where('slug', 'privacy-policy-english')->first();
        $page_bn =  Page::where('slug', 'privacy-policy-bangla')->first();
         return response([
            'result' => true,
            'content_en' => $page_en->content,
            'content_bn' => $page_bn->content,
        ], 200);
    }

     public function termsAndConditions(){
        $page_en =  Page::where('slug', 'terms-english')->first();
        $page_bn =  Page::where('slug', 'terms-bangla')->first();
        return response([
            'result' => true,
            'content_en' => $page_en->content,
            'content_bn' => $page_bn->content,
        ], 200);
    }

    public function aboutUs(){
        //  $page = Page::where('slug', "about-us")->first();

        $team = [
            [
                "image" => "https://muslim.town/public/assets/img/team/_jalal-ahmed-mirza-ceo.png",
                "name" => "Jalal Ahmed Mirza",
                "designation" => "Chief Executive Officer(CEO)"
            ],
            [
                "image" => "https://muslim.town/public/assets/img/team/_quhelee-jinat-operations.png",
                "name" => "Quhelee Jinat",
                "designation" => "Project Head (Head of Operation)"
            ],
            [
                "image" => "https://muslim.town/public/assets/img/team/_sakeeb-khan.jpg",
                "name" => "Sakeeb Khan",
                "designation" => "Head of Project Office (HPO)"
            ],
            [
                "image" => "https://muslim.town/public/assets/img/team/_fuizul-amin.jpg",
                "name" => "Fuizul Amin",
                "designation" => "Admin (HR, Finance)"
            ],
            [
                "image" => "https://muslim.town/public/assets/img/team/_shagor-khan.jpg",
                "name" => "Habibullah Khan Shagor",
                "designation" => "HR Head and Maintenance"
            ],
            [
                "image" => "https://muslim.town/public/assets/img/team/_shakil-mahmud-bappi-campaign.png",
                "name" => "Shakil Mahmud Bappi",
                "designation" => "Campaign"
            ],
            [
                "image" => "https://muslim.town/public/assets/img/team/_monowar-parvej.jpg",
                "name" => "Monowar Perves",
                "designation" => "Project Admin"
            ],
            [
                "image" => "https://muslim.town/public/assets/img/team/_toufik-ahmed.jpg",
                "name" => "Md. Toufik Ahmed",
                "designation" => "Asst. Project Admin"
            ],
        ];

        $page_en = "";
        $page_en .= view("frontend.legal.about-us-en");

        $page_bn = "";
        $page_bn .= view("frontend.legal.about-us-bn");

        return response([
            'result' => true,
            'team' => $team,
            'content_en' => $page_en,
            'content_bn' => $page_bn
        ], 200);
    }

    public function disclaimer(){
        //  $page = Page::where('slug', "about-us")->first();
        $page = "";
        $page .= view("frontend.disclaimer");
        return response([
            'result' => true,
            'content' => $page
        ], 200);
    }


    public function rewardHistory(){

        $accrued = Order::where("points_accrued", '>', 0)
                ->where("user_id", Auth::user()->id)->get();
        $redeem  = Order::where("points_redeem", '>', 0)
            ->where("user_id", Auth::user()->id)->get();

        return response()->json([
            "success" => true,
            "accured" => $accrued,
            "redeem" => $redeem
        ]);
    }

    public function deliveryDates()
    {
        return get_delivery_dates();
    }


    public function deliveryTimes(){

        $current_date = request("delivery_date");
        return get_delivery_times($current_date);

    }


    public function deliverySlots(){

        $current_date = request("delivery_date");
        $delivery_times = get_delivery_times($current_date);

        $html = "";

        foreach ($delivery_times as $dtime) {
            $html .= "<option value='".$dtime['value']."'>".$dtime['text']."</option>";
        }

        return response()->json($html, 200);
    }

}
