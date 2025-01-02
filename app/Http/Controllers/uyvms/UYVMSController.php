<?php

namespace App\Http\Controllers\UYVMS;

use App\Http\Controllers\Controller;
use App\Http\Resources\UYVMS\OrderListIndexResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UYVMS\OrderDetailsIndexResource;
use App\Http\Resources\UYVMS\OrderDetailsShowResource;
use App\Models\OrderDetail;
use App\Models\User;
use Illuminate\Support\Carbon;

class UYVMSController extends Controller
{

    private $akey = "PDneKUFGiuAZnY54fYyZ";

    public function orderIndex(){

        $rapikey = request()->api_key;

        if($this->akey != $rapikey){
            return response()->json([
                "message" => "Invalid API key"
            ], 422);
        }

        $orders = Order::paginate(10);
        return OrderDetailsIndexResource::collection($orders);
    }

    public function orderDetails($id){

        $rapikey = request()->api_key;

        if($this->akey != $rapikey){
            return response()->json([
                "message" => "Invalid API key"
            ], 422);
        }

        $order_details = Order::where('id',$id)->first();
        return new OrderDetailsShowResource($order_details);
    }

    public function orderList(){

        $rapikey = request()->api_key;

        if($this->akey != $rapikey){
            return response()->json([
                "message" => "Invalid API key"
            ], 422);
        }

        $month = Carbon::now()->subMonth()->month;
        if(Carbon::now()->month == 1){
            $year = Carbon::now()->subYear()->year;
        }else{
            $year = Carbon::now()->year;
        }

        $order_list = OrderDetail::whereHas('order', function($q){

            $q->where('delivery_status','delivered')->where('payment_status','paid');

        })->whereMonth('created_at', $month)
        ->whereYear("created_at", $year)
        ->get();

        return OrderListIndexResource::collection($order_list);
    }

    public function newUser(){

        $rapikey = request()->api_key;

        if($this->akey != $rapikey){
            return response()->json([
                "message" => "Invalid API key"
            ], 422);
        }

    //     $new_user =  User::whereMonth('created_at', now()->month) // checking if the month of created_at is current month
    //    ->whereYear('created_at', now()->year) // checking if the year of created_at is current year
    //    ->count();

        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $new_user = User::whereMonth('created_at',$month)->whereYear('created_at', $year)->count();


        return response()->json([
            "newuser" => $new_user
        ], 200);
    }

}
