<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmergencyBalanceController extends Controller
{

    public function emergencyBalance(){

        $user = Auth::user();

        $start_date = new Carbon($user->created_at);

        if($start_date->diffInMonths(Carbon::now()) <= 2){
            return response()->json([
                "errors" => [
                    "message" => ["You Account must be 2 months old to get Emergency Balance"]
                ]
            ], 422);
        }

        if($user->orders()->count() <= 0){
            return response()->json([
                "errors" => [
                    "message" => ["You are not Eligible"]
                ]
            ], 422);
        }

        $giveable_balance = get_setting('emergency_balance');


        if($user->balance_due > 0){
            return response()->json([
                "errors" => [
                    "message" => ["Balance Already Taken"]
                ]
            ], 422);
        }


        $user->update([
            "balance" => $giveable_balance,
            "balance_due" => $giveable_balance
        ]);

        return response()->json([
            "message" => "Congratulation! ৳$giveable_balance Added to your Account"
        ]);

    }
}
