<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\V3\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Auth;

class CategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth:sanctum")->only(["b2b"]);
    }

    public function index(){

        $categories = Category::orderBy("order_level", "DESC")
                ->where("parent_id", 0)
                ->where("is_b2b_category", false)
                ->where("is_active", true)->get();

        return CategoryResource::collection($categories);

    }

    public function b2b(){

        $user = Auth::user();
        if($user->is_b2b_user != 1) return response()->json("Unauthorized",401);

        $categories = Category::orderBy("order_level", "DESC")
            ->where("parent_id", 0)
            ->where("is_b2b_category", true)
            ->where("is_active", true)->get();

        return CategoryResource::collection($categories);

    }
}
