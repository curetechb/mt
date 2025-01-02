<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Models\ShopNetwork;
use Illuminate\Http\Request;

class ShopNetworkController extends Controller
{
    public function join(Request $request){

        $request->validate([
            "shop_name" => ["required", "unique:shop_networks"],
            "name" => ["required"],
            "phone" => ["required", "unique:shop_networks"],
            "area" => ["required"],
            "address" => ["required"],
        ]);

        $shopNetwork = new ShopNetwork();
        $shopNetwork->shop_name = $request->shop_name;
        $shopNetwork->name = $request->name;
        $shopNetwork->phone = $request->phone;
        $shopNetwork->area_id = $request->area;
        $shopNetwork->address = $request->address;
        

        if($shopNetwork->save()){
            return response()->json([
                "success" => true,
                "message" => "saved successfully",
            ], 200);
        }else{
            return response()->json([
                "success" => false,
                "message" => "Failed to Save",
            ], 500);
        }

    }
}
