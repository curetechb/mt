<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscription_configuration(){

        $subscription_fee = get_setting('subscription_fee');

        return response()->json([
            "subscription_fee" => $subscription_fee
        ], 200);

    }
}
