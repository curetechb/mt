<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Category;
use App\Models\Cart;
use App\Http\Controllers\PublicSslCommerzPaymentController;
use App\Http\Controllers\OrderController;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Address;
use App\Models\CombinedOrder;
use Session;
use App\Utility\NotificationUtility;
use DB;

class CheckoutController extends Controller
{

    public function __construct()
    {
        //
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {


        if(!$request->address_id){
            Session::flash("error",translate('No Shipping Address Found'));
            return redirect()->route("checkout.shipping_info");
        }

        if(!$request->payment_option){
            Session::flash("error",translate('Select Payment Option.'));
            return redirect()->route("checkout.shipping_info");
        }

        $temp_user_id = $request->session()->get('temp_user_id');
        $carts = Cart::where('temp_user_id', $temp_user_id)
            ->get();

        if (count($carts) <= 0) {
            Session::flash("error",translate('Your cart is empty'));
            return redirect()->route("checkout.shipping_info");
        }
        DB::beginTransaction();
        try{


            (new OrderController)->store($request);

            $request->session()->put('payment_type', 'cart_payment');

            $data['order_id'] = $request->session()->get('order_id');
            $request->session()->put('payment_data', $data);

            if ($request->session()->get('order_id') != null) {

                if ($request->payment_option == 'sslcommerz') {
                    $sslcommerz = new PublicSslCommerzPaymentController;
                    return $sslcommerz->index($request);
                }elseif ($request->payment_option == 'nagad') {
                    $nagad = new NagadController;
                    return $nagad->getSession();
                } elseif ($request->payment_option == 'bkash') {
                    $bkash = new BkashController;
                    return $bkash->pay();
                } elseif ($request->payment_option == 'cash_on_delivery') {
                    DB::commit();
                    Session::flash("success",translate("Your order has been placed successfully"));
                    return redirect()->route('order_confirmed');
                } elseif ($request->payment_option == 'wallet') {
                    $user = Auth::user();
                    $order = Order::findOrFail($request->session()->get('order_id'));
                    if ($user->balance >= $order->grand_total) {
                        $user->balance -= $order->grand_total;
                        $user->save();
                        return $this->checkout_done($request->session()->get('order_id'), null);
                    }
                } else {
                    $order = Order::findOrFail($request->session()->get('order_id'));
                    foreach ($order->orders as $order) {
                        $order->manual_payment = 1;
                        $order->save();
                    }

                    DB::commit();
                    Session::flash("success",translate('Your order has been placed successfully. Please submit payment information from purchase history'));
                    return redirect()->route('order_confirmed');
                }
            }

            return redirect()->route('order_confirmed');

        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    //redirects to this method after a successfull checkout
    public function checkout_done($order_id, $payment)
    {
        $order = Order::findOrFail($order_id);
        // foreach ($order->orders as $key => $order) {
        //     $order = Order::findOrFail($order->id);
        //     $order->payment_status = 'paid';
        //     $order->payment_details = $payment;
        //     $order->save();

        //     calculateCommissionAffilationClubPoint($order);
        // }
        Session::put('order_id', $order_id);
        return redirect()->route('order_confirmed');
    }


    public function get_shipping_info(Request $request)
    {

        $delivery_dates = get_delivery_dates();
        $delivery_times = get_delivery_times(1);


        $temp_user_id = $request->session()->get('temp_user_id');
        if(Auth::check()){
            $carts = Cart::where('user_id', Auth::user()->id)->get();
        }else{
            $carts = Cart::where('temp_user_id', $temp_user_id)->get();
        }

        $subtotal = 0;
        $all_free = false;
        foreach($carts as $key => $cartItem){
            $product = $cartItem->product;
            if($product->free_shipping) $all_free = true;
            $subtotal = $subtotal + ($product->unit_price + $cartItem->tax) * $cartItem->quantity;
        }

        $shipping_cost = $all_free ? 0 : get_setting('flat_rate_shipping_cost');
        $total = $subtotal + $shipping_cost;

        return view('frontend.shipping_info', compact('carts', "subtotal","total", "shipping_cost", "delivery_dates", "delivery_times"));
    }

    public function store_shipping_info(Request $request)
    {
        if ($request->address_id == null) {
            Session::flash("error",translate("Please add shipping address"));
            return back();
        }

        $carts = Cart::where('user_id', Auth::user()->id)->get();

        foreach ($carts as $key => $cartItem) {
            $cartItem->address_id = $request->address_id;
            $cartItem->save();
        }

        return view('frontend.delivery_info', compact('carts'));
        // return view('frontend.payment_select', compact('total'));
    }

    public function store_delivery_info(Request $request)
    {
        if ($request->address_id == null) {
            Session::flash("error",translate("Please add shipping address"));
            return back();
        }

        $carts = Cart::where('user_id', Auth::user()->id)
                ->get();

        if($carts->isEmpty()) {
            Session::flash("error",translate('Your cart is empty'));
            return redirect()->route('home');
        }

        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();
        $total = 0;
        $tax = 0;
        $shipping = 0;
        $subtotal = 0;

        if ($carts && count($carts) > 0) {
            foreach ($carts as $key => $cartItem) {
                $product = \App\Models\Product::find($cartItem['product_id']);
                $tax += $cartItem['tax'] * $cartItem['quantity'];
                $subtotal += $product->unit_price * $cartItem['quantity'];

                if ($request['shipping_type_' . $product->user_id] == 'pickup_point') {
                    $cartItem['shipping_type'] = 'pickup_point';
                    $cartItem['pickup_point'] = $request['pickup_point_id_' . $product->user_id];
                } else {
                    $cartItem['shipping_type'] = 'home_delivery';
                }
                $cartItem['shipping_cost'] = 0;
                if ($cartItem['shipping_type'] == 'home_delivery') {
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key);
                }

                if(isset($cartItem['shipping_cost']) && is_array(json_decode($cartItem['shipping_cost'], true))) {

                    foreach(json_decode($cartItem['shipping_cost'], true) as $shipping_region => $val) {
                        if($shipping_info['city'] == $shipping_region) {
                            $cartItem['shipping_cost'] = (double)($val);
                            break;
                        } else {
                            $cartItem['shipping_cost'] = 0;
                        }
                    }
                } else {
                    if (!$cartItem['shipping_cost'] ||
                            $cartItem['shipping_cost'] == null ||
                            $cartItem['shipping_cost'] == 'null') {

                        $cartItem['shipping_cost'] = 0;
                    }
                }

                $shipping += $cartItem['shipping_cost'];
                $cartItem->save();

            }
            $total = $subtotal + $tax + $shipping;
            return view('frontend.payment_select', compact('carts', 'shipping_info', 'total'));

        } else {
            Session::flash("error",translate('Your Cart was empty'));
            return redirect()->route('home');
        }
    }



    public function applyCouponCode(Request $request)
    {

        $cart_items = Cart::where('user_id', auth()->user()->id)->get();
        $coupon = Coupon::where('code', $request->code)->first();

        if ($cart_items->isEmpty()) {
            return response()->json([
                'result' => false,
                'message' => translate('Cart is empty')
            ], 422);
        }

        if ($coupon == null) {
            return response()->json([
                'result' => false,
                'message' => translate('Invalid coupon code!')
            ], 422);
        }


        $in_range = strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date;

        if (!$in_range) {
            return response()->json([
                'result' => false,
                'message' => translate('Coupon expired!')
            ], 422);
        }

        $is_used = CouponUsage::where('user_id', auth()->user()->id)->where('coupon_id', $coupon->id)->first() != null;

        if ($is_used) {
            return response()->json([
                'result' => false,
                'message' => translate('You already used this coupon!')
            ], 422);
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

                $cart_costs = $this->getCartCosts();
                return response()->json([
                    'result' => true,
                    'coupon_discount' => single_price($coupon_discount),
                    'total' => single_price($cart_costs['grand_total'] - $coupon_discount),
                    'message' => translate('Coupon Applied')
                ]);


            }else{
                return response()->json([
                    'result' => false,
                    'message' => translate('Coupon Requires Minimum Order amount '. single_price($coupon_details->min_buy))
                ], 422);
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
            $cart_costs = $this->getCartCosts();
            return response()->json([
                'result' => true,
                'coupon_discount' => single_price($coupon_discount),
                'total' => single_price($cart_costs['grand_total'] - $coupon_discount),
                'message' => translate('Coupon Applied Successfully')
            ]);

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
            $total = $total + ($product->unit_price + $cartItem->tax) * $cartItem->quantity;
        }

        $shipping_cost = intval($all_free ? 0 : get_setting("flat_rate_shipping_cost"));

        return [
            "total" => $total,
            "shipping_cost" => $shipping_cost,
            "grand_total" => $total + $shipping_cost
        ];
    }

    public function removeCouponCode(Request $request)
    {

        $cart_costs = $this->getCartCosts();

        return response()->json([
            "result" => true,
            "total" => single_price($cart_costs['total'] + $cart_costs['shipping_cost'])
        ]);
        // Cart::where('user_id', auth()->user()->id)->update([
        //     'discount' => 0.00,
        //     'coupon_code' => "",
        //     'coupon_applied' => 0
        // ]);

        // return response()->json([
        //     'result' => true,
        //     'message' => translate('Coupon Removed')
        // ]);
    }

    public function apply_club_point(Request $request) {
        if (addon_is_activated('club_point')){

            $point = $request->point;

            if(Auth::user()->point_balance >= $point) {
                $request->session()->put('club_point', $point);
                Session::flash("success",translate('Point has been redeemed'));
            }
            else {
                Session::flash("error",translate('Invalid point!'));
            }
        }
        return back();
    }

    public function remove_club_point(Request $request) {
        $request->session()->forget('club_point');
        return back();
    }

    public function order_confirmed()
    {

        $order = Order::findOrFail(Session::get('order_id'));

        Cart::where('user_id', $order->user_id)
                ->delete();

        //Session::forget('club_point');
        //Session::forget('combined_order_id');
        return view('frontend.order_confirmed', compact('order'));
    }



    public function deliverySlots(){

        $current_date = request("delivery_date");
        $delivery_times = get_delivery_times($current_date);

        $html = "";

        foreach ($delivery_times as $dtime) {
            $html .= "<option value='".$dtime['value']."'>".$dtime['text']."</option>";
        }

        return response()->json($html, 200);
    }
}
