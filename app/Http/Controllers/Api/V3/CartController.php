<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Resources\V3\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Auth;
use DB;

class CartController extends Controller
{
    public function index(){

        if(Auth::check()){
            $carts = Cart::where("user_id", Auth::user()->id)->get();
        }else{
            $carts = Cart::where("temp_user_id", request("temp_id"))->get();
        }

        $all_free = false;
        foreach ($carts as $cartItem){
            $product = Product::find($cartItem['product_id']);
            if($product->free_shipping) $all_free = true;
        }

        // return CartResource::collection($carts);
        $cart_total = $this->getCartTotal();
        $shipping_cost = $all_free ? 0 : get_setting("flat_rate_shipping_cost");

        $user = Auth::user();

        if($user && $user->delivery_subscription && get_setting("delivery_subscription") == 1){
            $shipping_cost = 0;
        }

        // $user = Auth::user();
        // $emergency_due = $user->emergency_due;
        // $cart_total += $emergency_due;

        return [
            "items" => CartResource::collection($carts),
            "cart_count" => count($carts),
            "cart_total" => currency_symbol().$cart_total,
            "shipping_cost" => currency_symbol(). $shipping_cost,
            "total_cost" => currency_symbol().$cart_total + $shipping_cost
        ];

    }

    public function summary(){

        if(Auth::check()){
            $carts = Cart::where("user_id", Auth::user()->id)->get();
        }else{
            $carts = Cart::where("temp_user_id", request("temp_id"))->get();
        }

        $all_free = false;
        foreach ($carts as $cartItem){
            $product = Product::find($cartItem['product_id']);
            if($product->free_shipping) $all_free = true;
        }

        // return CartResource::collection($carts);
        $cart_total = $this->getCartTotal();
        $shipping_cost = $all_free ? 0 : get_setting("flat_rate_shipping_cost");

        $user = Auth::user();

        if($user && $user->delivery_subscription && get_setting("delivery_subscription") == 1){
            $shipping_cost = 0;
        }

        return [
            "cart_count" => count($carts),
            "cart_total" => currency_symbol().$cart_total,
            "shipping_cost" => currency_symbol(). $shipping_cost,
            "total_cost" => currency_symbol().$cart_total + $shipping_cost
        ];

    }

    public function getCartTotal(){

        $q = DB::table('carts')
            ->join('products', 'products.id', '=', 'carts.product_id')
            ->select(DB::raw('sum(carts.quantity * products.unit_price) as total'));

        if(Auth::check()){
            $q = $q->where("carts.user_id", Auth::user()->id);
        }else{
            $q = $q->where("carts.temp_user_id", request("temp_id"));
        }

        $cart_totals = $q->pluck("total")->toArray();

        return array_sum($cart_totals);
    }


    public function show()
    {


    }

    public function update(Request $request, $product_id){

        if(Auth::check()){
            $cartItem = Cart::where("user_id", Auth::user()->id)->where("product_id", $product_id)->first();
        }else{
            $cartItem = Cart::where("temp_user_id", $request->temp_id)->where("product_id", $product_id)->first();
        }

        $product = Product::findOrFail($product_id);

        if(!$cartItem){

            $cartItem = Cart::create([
                "user_id" => Auth::check() ? Auth::user()->id : null,
                "temp_user_id" => request("temp_id"),
                "product_id" => $product_id,
                "quantity" => $product->min_qty
            ]);

        }else{

            if($request->type == "+" && $product->current_stock <= $product->low_stock_quantity){
                return response([
                    "message" => "Product out of Stock",
                    "errors" => [
                        "message" => ["Product out of Stock"]
                    ]
                ], 422);
            }

            if($request->type == "+" && $product->max_qty && $cartItem->quantity >= $product->max_qty){
                return response([
                    "message" => "Your Desired Quantity is Not available for this product",
                    "errors" => [
                        "message" => ["Your Desired Quantity is Not available for this product"]
                    ]
                ], 422);
            }

            if($request->type == "-" && $cartItem->quantity <= $product->min_qty){

                $cartItem->delete();

                return response([
                    "success" => true,
                    "data" => [
                        "product_id" => $cartItem->product_id,
                        "quantity" => 0
                    ]
                ], 200);
            }

            if($request->type == "-" && $cartItem->quantity > 1){
                $cartItem->quantity = $cartItem->quantity - 1;
            }else{
                $cartItem->quantity = $cartItem->quantity + 1;
            }

            $cartItem->save();

        }

        return response([
            "success" => true,
            "data" => [
                "product_id" => $cartItem->product_id,
                "quantity" => $cartItem->quantity
            ]
        ], 200);
    }


    public function destroy(Request $request, $product_id){

        if(Auth::check()){
            $cartItem = Cart::where("user_id", Auth::user()->id)->where("product_id", $product_id)->first();
        }else{
            $cartItem = Cart::where("temp_user_id", $request->temp_id)->where("product_id", $product_id)->first();
        }

        $cartItem->delete();

        return response([
            "success" => true,
        ], 200);

    }


}
