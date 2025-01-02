<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Auth;
use Session;
use Cookie;

class CartController extends Controller
{
    public function index(Request $request)
    {

        // $carts = Cart::where('temp_user_id', $temp_user_id)->get();

        if(Auth::check()){
            $carts = Cart::where('user_id', Auth::user()->id)->get();
        }else{
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        return view('frontend.view_cart', compact('carts'));
    }

    public function showCartModal(Request $request)
    {
        $product = Product::find($request->id);
        return view('frontend.partials.addToCart', compact('product'));
    }

    public function showCartModalAuction(Request $request)
    {
        $product = Product::find($request->id);
        return view('auction.frontend.addToCartAuction', compact('product'));
    }


    public function checkCoupon($code)
    {
        $cart_items = Cart::where('user_id', auth()->user()->id ?? "")->get();
        $coupon = Coupon::where('code', $code)->first();

        if ($cart_items->isEmpty()) {
            return [
                'result' => false,
                'message' => translate('Cart is empty')
            ];
        }

        if ($coupon == null) {
            return [
                'result' => false,
                'message' => translate('Invalid coupon code!')
            ];
        }

        $in_range = strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date;

        if (!$in_range) {
            return [
                'result' => false,
                'message' => translate('Coupon expired!')
            ];
        }

        $is_used = CouponUsage::where('user_id', auth()->user()->id)->where('coupon_id', $coupon->id)->first() != null;

        if ($is_used) {
            return [
                'result' => false,
                'message' => translate('You already used this coupon!')
            ];
        }


        $coupon_details = json_decode($coupon->details);

        if ($coupon->type == 'cart_base') {
            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            foreach ($cart_items as $key => $cartItem) {
                $subtotal += $cartItem->product->unit_price * $cartItem['quantity'];
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $shipping += $cartItem['shipping'] * $cartItem['quantity'];
            }
            $sum = $subtotal + $tax + $shipping;

            if ($sum >= $coupon_details->min_buy) {
                if ($coupon->discount_type == 'percent') {
                    $coupon_discount = ($sum * $coupon->discount) / 100;
                    if ($coupon_discount > $coupon_details->max_discount) {
                        $coupon_discount = $coupon_details->max_discount;
                    }
                } elseif ($coupon->discount_type == 'amount') {
                    $coupon_discount = $coupon->discount;
                }

                // Cart::where('user_id', auth()->user()->id)->update([
                //     'discount' => $coupon_discount / count($cart_items),
                //     'coupon_code' => $request->coupon_code,
                //     'coupon_applied' => 1
                // ]);


                return [
                    'result' => true,
                    'coupon_discount' => $coupon_discount,
                ];


            }else{
                return [
                    'result' => false,
                    'message' => translate('Coupon Requires Minimum Order amount '. single_price($coupon_details->min_buy))
                ];
            }
        } elseif ($coupon->type == 'product_base') {
            $coupon_discount = 0;
            foreach ($cart_items as $key => $cartItem) {
                foreach ($coupon_details as $key => $coupon_detail) {
                    if ($coupon_detail->product_id == $cartItem['product_id']) {
                        if ($coupon->discount_type == 'percent') {
                            $coupon_discount += $cartItem->product->unit_price * $coupon->discount / 100;
                        } elseif ($coupon->discount_type == 'amount') {
                            $coupon_discount += $coupon->discount;
                        }
                    }
                }
            }


            // Cart::where('user_id', auth()->user()->id)->update([
            //     'discount' => $coupon_discount / count($cart_items),
            //     'coupon_code' => $request->coupon_code,
            //     'coupon_applied' => 1
            // ]);

            return [
                'result' => true,
                'coupon_discount' => $coupon_discount,
            ];

        }


    }

    public function getCartCosts(){

        if(Auth::check()){
            $cart = \App\Models\Cart::where('user_id', Auth::user()->id)->get();
        }else{
            $temp_user_id = Session()->get('temp_user_id');
            $cart = \App\Models\Cart::where('temp_user_id', $temp_user_id)->get();
        }

        $total = 0;
        $all_free = false;
        foreach($cart as $key => $cartItem){
            $product = $cartItem->product;
            if($product->free_shipping) $all_free = true;
            $total = $total + ($cartItem->product->unit_price + $cartItem->tax) * $cartItem->quantity;
        }

        $shipping_cost = intval($all_free ? 0 : get_setting("flat_rate_shipping_cost"));

        $coupon_result = $this->checkCoupon(request('coupon_code'));
        $coupon_discount = $coupon_result != null && $coupon_result['result'] == true ? $coupon_result['coupon_discount'] : 0;

        return [
            "total" => $total,
            "shipping_cost" => $shipping_cost,
            "coupon_discount" => $coupon_discount,
            "grand_total" => $total + $shipping_cost - $coupon_discount
        ];
    }

    public function addToCart(Request $request)
    {
        $product = Product::find($request->product_id);

        $temp_user_id = $request->session()->get('temp_user_id');
        if(Auth::check()){
            $cart = Cart::where("user_id", Auth::user()->id)->where("product_id", $product->id)->first();
        }else{
            $cart = Cart::where("temp_user_id", $temp_user_id)->where("product_id", $product->id)->first();
        }

        if(!$temp_user_id){
            $temp_user_id = bin2hex(random_bytes(10));
            $request->session()->put('temp_user_id', $temp_user_id);
        }


        $product_stock = $product->stocks->where('variant', "")->first();
        $price = $product_stock->price;
        $quantity = $product_stock->qty;

        if($quantity <= $product->low_stock_quantity){
            if(Auth::check()){
                $carts = Cart::where('user_id', Auth::user()->id)->get();
            }else{
                $carts = Cart::where('temp_user_id', $temp_user_id)->get();
            }
            $edata['message'] = 'Out of Stock';
            $cart_costs = $this->getCartCosts();
            return array(
                'status' => 0,
                'cart_count' => count($carts),
                'cart_item_count' => $cart->quantity ?? 0,
                'cart_total' => single_price($cart_costs['total']),
                'grand_total' => single_price($cart_costs['grand_total']),
                'shipping_cost' => single_price($cart_costs['shipping_cost']),
                "coupon_discount" => single_price($cart_costs['coupon_discount']),
                'modal_view' => view('frontend.partials.outOfStockCart', $edata)->render(),
                'nav_cart_view' => view('frontend.partials.cart')->render(),
            );
        }



        if(!$cart){
            $data = array();
            $data['temp_user_id'] = $temp_user_id;
            if(Auth::check()){
                $data['user_id'] = Auth::user()->id;
            }
            $data['product_id'] = $product->id;
            $data['owner_id'] = $product->user_id;


            $data['quantity'] = $product->min_qty;
            $data['price'] = $price;
            $data['tax'] = 0;
            //$data['shipping'] = 0;
            $data['shipping_cost'] = 0;
            $data['product_referral_code'] = null;
            $data['cash_on_delivery'] = $product->cash_on_delivery;
            $data['digital'] = $product->digital;

            $cart = Cart::create($data);
        }else{

            if($request->data_id != "minus" && $product->max_qty && ($cart->quantity > $product->max_qty - 1)){
                if(Auth::check()){
                    $carts = Cart::where('user_id', Auth::user()->id)->get();
                }else{
                    $carts = Cart::where('temp_user_id', $temp_user_id)->get();
                }
                $data['message'] = 'Your desired quantity is not available for this product';

                $cart_costs = $this->getCartCosts();
                return array(
                    'status' => 1,
                    'cart_count' => count($carts),
                    'cart_item_count' => $cart->quantity ?? 0,
                    'cart_total' => single_price($cart_costs['total']),
                    'grand_total' => single_price($cart_costs['grand_total']),
                    'shipping_cost' => single_price($cart_costs['shipping_cost']),
                    "coupon_discount" => single_price($cart_costs['coupon_discount']),
                    'modal_view' => view('frontend.partials.outOfStockCart', $data)->render(),
                    'nav_cart_view' => view('frontend.partials.cart')->render(),
                );
            }

            if($request->data_id == "minus"){
                if($cart->quantity == 1){
                    $cart->delete();
                    $cart = null;
                }else{

                    if($cart->quantity <= $product->min_qty){
                        $cart->delete();
                        $cart = null;
                    }else{
                        $cart->quantity = $cart->quantity - 1;
                        $cart->update();
                    }

                }
            }else{
                $cart->quantity = $cart->quantity + 1;
                $cart->update();
            }
        }

        if(Auth::check()){
            $carts = Cart::where('user_id', Auth::user()->id)->get();
        }else{
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        $cart_costs = $this->getCartCosts();
        return array(
            'status' => 1,
            'cart_item_count' => $cart->quantity ?? 0,
            'cart_count' => count($carts),
            'cart_total' => single_price($cart_costs['total']),
            'grand_total' => single_price($cart_costs['grand_total']),
            'shipping_cost' => single_price($cart_costs['shipping_cost']),
            "coupon_discount" => single_price($cart_costs['coupon_discount']),
            // 'modal_view' => view('frontend.partials.addedToCart', compact('product', 'data'))->render(),
            'nav_cart_view' => view('frontend.partials.cart')->render(),
        );

    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        Cart::destroy($request->id);
        // if(auth()->user() != null) {
        //     $user_id = Auth::user()->id;
        //     $carts = Cart::where('user_id', $user_id)->get();
        // } else {
        //     $temp_user_id = $request->session()->get('temp_user_id');
        //     $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        // }

        if(Auth::check()){
            $carts = Cart::where('user_id', Auth::user()->id)->get();
        }else{
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        $cart_costs = $this->getCartCosts();
        return array(
            'cart_count' => count($carts),
            'cart_total' => single_price($cart_costs['total']),
            'grand_total' => single_price($cart_costs['grand_total']),
            'shipping_cost' => single_price($cart_costs['shipping_cost']),
            "coupon_discount" => single_price($cart_costs['coupon_discount']),
            'cart_view' => view('frontend.partials.cart_details', compact('carts'))->render(),
            'nav_cart_view' => view('frontend.partials.cart')->render(),
        );
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {

        $cartItem = Cart::findOrFail($request->cart_id);

        if(Auth::check()){
            $carts = Cart::where('user_id', Auth::user()->id)->get();
        }else{
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        if($cartItem['id'] == $request->cart_id){

            $product = Product::find($cartItem['product_id']);


            if($product->max_qty && ($request->quantity > $product->max_qty)){
                $data['message'] = 'Your desired quantity is not available for this product';
                $cart_costs = $this->getCartCosts();
                return array(
                    'status' => 0,
                    'cart_count' => count($carts),
                    'cart_total' => single_price($cart_costs['total']),
                    'grand_total' => single_price($cart_costs['grand_total']),
                    'shipping_cost' => single_price($cart_costs['shipping_cost']),
                    "coupon_discount" => single_price($cart_costs['coupon_discount']),
                    'modal_view' => view('frontend.partials.outOfStockCart', $data)->render(),
                    'nav_cart_view' => view('frontend.partials.cart')->render(),
                );
            }

            $product_stock = $product->stocks->where('variant', $cartItem['variation'])->first();
            $quantity = $product_stock->qty;
            $price = $product_stock->price;

			//discount calculation
            $discount_applicable = false;

            if ($product->discount_start_date == null) {
                $discount_applicable = true;
            }
            elseif (strtotime(date('d-m-Y H:i:s')) >= $product->discount_start_date &&
                strtotime(date('d-m-Y H:i:s')) <= $product->discount_end_date) {
                $discount_applicable = true;
            }

            if ($discount_applicable) {
                if($product->discount_type == 'percent'){
                    $price -= ($price*$product->discount)/100;
                }
                elseif($product->discount_type == 'amount'){
                    $price -= $product->discount;
                }
            }

            // if($quantity >= $request->quantity) {
            //     if($request->quantity >= $product->min_qty){
            //         $cartItem['quantity'] = $request->quantity;
            //     }
            // }
            $cartItem['quantity'] = $cartItem->quantity - 1; // added by salim

            // if($product->wholesale_product){
            //     $wholesalePrice = $product_stock->wholesalePrices->where('min_qty', '<=', $request->quantity)->where('max_qty', '>=', $request->quantity)->first();
            //     if($wholesalePrice){
            //         $price = $wholesalePrice->price;
            //     }
            // }

            $cartItem['price'] = $price;
            $cartItem->save();
        }


        // if(auth()->user() != null) {
        //     $user_id = Auth::user()->id;
        //     $carts = Cart::where('user_id', $user_id)->get();
        // } else {
        //     $temp_user_id = $request->session()->get('temp_user_id');
        //     $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        // }

        $cart_costs = $this->getCartCosts();

        return array(
            'cart_count' => count($carts),
            'cart_id' => $cartItem->id,
            'cart_item_count' => $cartItem->quantity,
            'cart_total' => single_price($cart_costs['total']),
            'grand_total' => single_price($cart_costs['grand_total']),
            'shipping_cost' => single_price($cart_costs['shipping_cost']),
            "coupon_discount" => single_price($cart_costs['coupon_discount']),
            'cart_view' => view('frontend.partials.cart_details', compact('carts'))->render(),
            'nav_cart_view' => view('frontend.partials.cart')->render(),
        );
    }
}
