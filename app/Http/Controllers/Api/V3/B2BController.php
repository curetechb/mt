<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class B2BController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }

    public function makeB2Buser(Request $request)
    {

        $user = Auth::user();

        $user->is_b2b_user = 0;
        $user->save();

        return response()->json([
            "success" => true,
            "message" => "Request Sent Successfully"
        ]);

    }
}
