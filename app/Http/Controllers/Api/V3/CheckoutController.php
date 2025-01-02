<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use Auth;
use App\Models\Order;

class CheckoutController extends Controller
{

    public function deliveryDates()
    {
        return get_delivery_dates();
    }


    public function deliveryTimes(){

        $current_date = request("delivery_date", 1);
        return get_delivery_times($current_date);

    }

    public function states(){
        $states = State::all();
        return response([
            "success" => true,
            "data" => $states
        ], 200);
    }

    public function cities(){
        $cities = City::all();
        return response([
            "success" => true,
            "data" => $cities
        ], 200);
    }

    public function syncCart(){


        if(!Auth::check()){
            return response()->json([
                "errors" => [
                    "message" => ["Unauthorized Access"]
                ]
            ], 401);

        }

        $temp_user_id = request("temp_user_id");

        if(!$temp_user_id){
            return response()->json([
                "errors" => [
                    "message" => ["Temp User ID is Null"]
                ]
            ], 401);
        }

        $user_id = Auth::user()->id;

        // $carts = Cart::where("temp_user_id", $temp_user_id)->get();

        // foreach ($carts as $item) {
        //     $item->user_id = $user_id;
        //     $item->save();
        // }

        Cart::where("temp_user_id", $temp_user_id)->update([
            "user_id" => $user_id
        ]);


        return response()->json([
            "success" => true,
            "message" => "Synced Successfully"
        ]);

    }

    public function emergencyDue(){

        $user = Auth::user();

        $emergency_order = Order::where("user_id", $user->id)->where("is_emergency_order", true)->where("payment_status", "!=", "paid")->first();

        if($emergency_order){
            $total = $emergency_order->grand_total - $emergency_order->reward_discount - $emergency_order->coupon_discount;
            return [
                "emergency_due" => $total
            ];
        }

        return [
            "emergency_due" => 0
        ];
    }
}
