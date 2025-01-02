<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Cart;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{


    public function summary(Request $request)
    {
        //$user = User::where('id', auth()->user()->id)->first();
        // $items = auth()->user()->carts;
        $items = Cart::where("temp_user_id", $request->temp_user_id)->get();
        if ($items->isEmpty()) {
            return response()->json([
                'sub_total' => format_price(0.00),
                'tax' => format_price(0.00),
                'shipping_cost' => "0",
                'discount' => format_price(0.00),
                'grand_total' => format_price(0.00),
                'grand_total_value' => 0.00,
                'coupon_code' => "",
                'coupon_applied' => false,
            ]);
        }

        $sum = 0.00;
        $subtotal = 0.00;
        $tax = 0.00;
        $all_free = false;
        foreach ($items as $cartItem) {
            $product = $cartItem->product;
            if($product->free_shipping) $all_free = true;
            $item_sum = 0.00;
            $item_sum += ($product->unit_price + $cartItem->tax) * $cartItem->quantity;
            $item_sum += $cartItem->shipping_cost - $cartItem->discount;
            $sum +=  $item_sum  ;   //// 'grand_total' => $request->g

            $subtotal += $product->unit_price * $cartItem->quantity;
            $tax += $cartItem->tax * $cartItem->quantity;
        }


        $shipping_cost = intval($all_free ? 0 : get_setting("flat_rate_shipping_cost"));

        return response()->json([
            'sub_total' => format_price($subtotal),
            'tax' => format_price($tax),
            'discount' => format_price($items->sum('discount')),
            'grand_total' => format_price($sum),
            'grand_total_value' => convert_price($sum),
            'shipping_cost' => "$shipping_cost",
            'coupon_code' => $items[0]->coupon_code,
            'coupon_applied' => $items[0]->coupon_applied == 1,
        ]);


    }


    public function getCartTotal(){
        $temp_user_id = request()->temp_user_id;
        $cart = \App\Models\Cart::where('temp_user_id', $temp_user_id)->get();
        $total = 0;
        foreach($cart as $key => $cartItem){
            $total = $total + ($cartItem->price + $cartItem->tax) * $cartItem->quantity;
        }
        return single_price($total);
    }


    public function getList($temp_user_id)
    {
        $owner_ids = Cart::where('temp_user_id', $temp_user_id)
        ->select('owner_id')->groupBy('owner_id')->pluck('owner_id')->toArray();

        $currency_symbol = currency_symbol();
        $shops = [];
        if (!empty($owner_ids)) {
            foreach ($owner_ids as $owner_id) {
                $shop = array();
                $shop_items_raw_data = Cart::where('temp_user_id', $temp_user_id)->where('owner_id', $owner_id)->get()->toArray();
                $shop_items_data = array();
                if (!empty($shop_items_raw_data)) {
                    foreach ($shop_items_raw_data as $shop_items_raw_data_item) {
                        $product = Product::where('id', $shop_items_raw_data_item["product_id"])->first();
                        $shop_items_data_item["id"] = intval($shop_items_raw_data_item["id"]) ;
                        $shop_items_data_item["owner_id"] =intval($shop_items_raw_data_item["owner_id"]) ;
                        $shop_items_data_item["user_id"] =intval($shop_items_raw_data_item["user_id"]) ;
                        $shop_items_data_item["product_id"] =intval($shop_items_raw_data_item["product_id"]) ;
                        $shop_items_data_item["product_name"] = $product->getTranslation('name');
                        $shop_items_data_item["product_thumbnail_image"] = api_asset($product->thumbnail_img);
                        $shop_items_data_item["variation"] = $shop_items_raw_data_item["variation"];
                        // $shop_items_data_item["price"] =(double) $shop_items_raw_data_item["price"];
                        $shop_items_data_item["price"] = $product->unit_price;
                        $shop_items_data_item["currency_symbol"] = $currency_symbol;
                        $shop_items_data_item["tax"] =(double) $shop_items_raw_data_item["tax"];
                        $shop_items_data_item["shipping_cost"] =(double) $shop_items_raw_data_item["shipping_cost"];
                        $shop_items_data_item["quantity"] =intval($shop_items_raw_data_item["quantity"]) ;
                        $shop_items_data_item["lower_limit"] = intval($product->min_qty);
                        $shop_items_data_item["upper_limit"] = intval($product->stocks->where('variant', $shop_items_raw_data_item['variation'])->first()->qty) ;

                        $shop_items_data_item["unit"] = $product->unit_value." ".$product->unit;

                        $shop_items_data_item["min_qty"] = $product->min_qty;
                        $shop_items_data_item["max_qty"] = $product->max_qty;
                        $shop_items_data_item["in_cart"] = intval($shop_items_raw_data_item["quantity"]);
                        $stock = $product->stocks->where('variant', null)->first();
                        $shop_items_data_item["out_of_stock"] = $stock->qty <= $product->low_stock_quantity;

                        $shop_items_data[] = $shop_items_data_item;

                    }
                }


                $shop_data = Shop::where('user_id', $owner_id)->first();
                if ($shop_data) {
                    $shop['name'] = $shop_data->name;
                    $shop['owner_id'] =(int) $owner_id;
                    $shop['cart_items'] = $shop_items_data;
                } else {
                    $shop['name'] = "Inhouse";
                    $shop['owner_id'] =(int) $owner_id;
                    $shop['cart_items'] = $shop_items_data;
                }
                $shops[] = $shop;
            }
        }

        //dd($shops);

        return response()->json($shops);
    }


    public function add(Request $request)
    {

        $product = Product::findOrFail($request->id);

        $tax = 0;

        $price = $product->unit_price;


        // if ($product->min_qty > $request->quantity) {
        //     return response()->json(['result' => false, 'message' => translate("Minimum")." {$product->min_qty} ".translate("item(s) should be ordered")], 200);
        // }

        // if ($stock < $request->quantity) {
        //     if ($stock == 0) {
        //         return response()->json(['result' => false, 'message' => "Stock out"], 200);
        //     } else {
        //         return response()->json(['result' => false, 'message' => translate("Only") ." {$stock} ".translate("item(s) are available")." {$variant_string}"], 200);
        //     }
        // }

        $temp_user_id = $request->temp_user_id;
        // if(!$temp_user_id){
        //     $temp_user_id = bin2hex(random_bytes(10));
        // }

        $cart = Cart::updateOrCreate([
            'user_id' => $request->user_id ?? null,
            'temp_user_id' => $temp_user_id,
            'owner_id' => $product->user_id,
            'product_id' => $request->id,
            'variation' => ""
        ], [
            'price' => $price,
            'tax' => $tax,
            'shipping_cost' => 0,
            'quantity' => DB::raw("quantity + $request->quantity")
        ]);

        // if(\App\Utility\NagadUtility::create_balance_reference($request->cost_matrix) == false){
        //     return response()->json(['result' => false, 'message' => 'Cost matrix error' ]);
        // }

        return response()->json([
            'result' => true,
            'cart_id' => $cart->id,
            // 'temp_user_id' => $temp_user_id,
            'cart_total' => $this->getCartTotal(),
            'product_id' => $cart->product_id,
            'message' => translate('Product added to cart successfully')
        ]);
    }

    public function changeQuantity(Request $request)
    {
        $cart = Cart::where("product_id", $request->product_id)->where("temp_user_id", $request->temp_user_id)->first();
        if ($cart != null) {

            if($cart->quantity == 1){
                $cart->delete();
            }else{
                 $cart->update([
                    'quantity' => $cart->quantity - 1
                ]);
            }

            return response()->json([
                'result' => true,
                'cart_id' => $cart->id,
                'cart_total' => $this->getCartTotal(),
                'quantity' => $cart->quantity,
                'product_id' => $cart->product_id,
                'message' => translate('Cart Updated')
            ], 200);
        }

        return response()->json([
            'result' => false,
            'cart_id' => $cart->id,
            'cart_total' => $this->getCartTotal(),
            'quantity' => $cart->quantity,
            'product_id' => $cart->product_id,
            'message' => translate('Something went wrong')
        ], 200);
    }


    public function destroy($id)
    {
        Cart::destroy($id);
        return response()->json(['result' => true, 'message' => translate('Product is successfully removed from your cart')], 200);
    }


    public function syncCart($temp_user_id){

        Cart::where("temp_user_id", $temp_user_id)->update([
            "user_id" => auth()->user()->id
        ]);


        return response()->json([
            "success" => true,
            "message" => "Synced Successfully"
        ]);
    }


    public function outOfStock($temp_user_id){

        $carts = Cart::where('temp_user_id', $temp_user_id)->get();

        $out_of_stock = false;
        foreach ($carts as $cartItem) {
            $product = $cartItem->product;
            $stock = $product->stocks->where('variant', null)->first();
            if($stock->qty <= $product->low_stock_quantity){
                $out_of_stock = true;
            }
        }

        return response()->json([
            "success" => true,
            "out_of_stock" => $out_of_stock,
            "message" => $out_of_stock ? "Out of Stock" : "Not Out of Stock"
        ]);
    }
}
