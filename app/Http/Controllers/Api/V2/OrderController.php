<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\BusinessSetting;
use App\Models\User;
use DB;
use \App\Utility\NotificationUtility;
use App\Models\CombinedOrder;
use App\Http\Controllers\AffiliateController;
use App\Models\ProductStock;
use Auth;

class OrderController extends Controller
{

   public function __construct()
    {
        $this->middleware("auth:sanctum");
    }


    public function store(Request $request, $set_paid = false)
    {

        if(!$request->address_id){
            return response()->json([
                'result' => false,
                'message' => translate('No Shipping Address Found')
            ]);
        }

        if(!$request->payment_option){
            return response()->json([
                'result' => false,
                'message' => translate('Select Payment Option.')
            ]);
        }


        // $cartItems = Cart::where('user_id', auth()->user()->id)->get();
        $temp_user_id = $request->temp_user_id;
        $carts = Cart::where('temp_user_id', $temp_user_id)->get();

        if (count($carts) <= 0) {
            return response()->json([
                'result' => false,
                'message' => translate('Cart is Empty')
            ]);
        }

        $user = Auth::user();

        $coupon_result = $this->checkCoupon($request->code);
        $coupon_discount = $coupon_result != null && $coupon_result['result'] == true ? $coupon_result['coupon_discount'] : 0;

        $address = Address::where('id', $request->address_id)->first();
        $shippingAddress = [];
        if ($address != null) {
            $shippingAddress['name']        = $address->name;
            $shippingAddress['email']       = Auth::user()->email;
            $shippingAddress['address']     = $address->address;
            $shippingAddress['country']     = $address->country->name ?? "Bangladesh";
            $shippingAddress['state']       = $address->state->name ?? "";
            $shippingAddress['city']        = $address->city->name ?? null;
            $shippingAddress['postal_code'] = $address->postal_code;
            $shippingAddress['phone']       = $address->phone;
            $shippingAddress['floor_no']       = $address->floor_no;
            $shippingAddress['apartment']       = $address->apartment;
            if ($address->latitude || $address->longitude) {
                $shippingAddress['lat_lang'] = $address->latitude . ',' . $address->longitude;
            }
        }

	    $user_orders = $user->orders()->count();
        if($user_orders <= 0){
            $user->name = $address->name;
            $user->save();
        }

        DB::beginTransaction();
        try{
            $order = new Order;
            $order->ordered_from = "app";
            $order->tracking_code = rand(1000, 9999);
            $order->city_id = $address->city_id;
            $order->applied_coupon_code = $request->code;
            $order->preferred_delivery_date = $this->getPreferredDeliveryDate($request->delivery_date);
            $order->delivery_slot = $request->delivery_time;
            // $order->combined_order_id = $combined_order->id;
            $order->user_id = Auth::user()->id;
            $order->shipping_address = json_encode($shippingAddress);
            // $order->shipping_type = $carts[0]['shipping_type'];
            $order->payment_type = $request->payment_option;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';

            $order->date = strtotime('now');
            $order->save();

            $subtotal = 0;
            $tax = 0;
            // $shipping = get_setting('flat_rate_shipping_cost'); // default cost 50
            // $total_weight = 0;
            // $coupon_discount = 0;

            $all_free = false;
            foreach ($carts as $cartItem){

                    $product = Product::find($cartItem['product_id']);
                    if($product->free_shipping) $all_free = true;

                    // $subtotal += $cartItem['price'] * $cartItem['quantity'];
                    $subtotal += $product->unit_price * $cartItem['quantity'];
                    $tax += $cartItem['tax'] * $cartItem['quantity'];
                    // $coupon_discount += $cartItem['discount'];


                    // if ($product->digital != 1 && $product_stock && $cartItem['quantity'] > $product_stock->qty) {
                    //     flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                    //     $order->delete();
                    //     return redirect()->route('cart')->send();
                    // } elseif ($product_stock &&  $product->digital != 1) {
                    //     $product_stock->qty -= $cartItem['quantity'];
                    //     $product_stock->save();
                    // }

                    $product->current_stock -= $cartItem['quantity'];


                    $order_detail = new OrderDetail();
                    $order_detail->order_id = $order->id;
                    $order_detail->seller_id = $product->user_id;
                    $order_detail->product_id = $product->id;
                    // $order_detail->price = $cartItem['price'] * $cartItem['quantity'];
                    $order_detail->price = $product->unit_price * $cartItem['quantity'];
                    $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];
                    // $order_detail->shipping_type = $cartItem['shipping_type'] ?? null;
                    // $order_detail->product_referral_code = $cartItem['product_referral_code'] ?? null;
                    // $order_detail->shipping_cost = $cartItem['shipping_cost'] ?? 0;


                    // $total_weight += $product->weight * $cartItem['quantity']; // product total weight
                    // $shipping += $order_detail->shipping_cost;
                    //End of storing shipping cost

                    $order_detail->quantity = $cartItem['quantity'];
                    $order_detail->save();

                    $product->num_of_sale += $cartItem['quantity'];
                    $product->save();

                    $order->seller_id = $product->user_id;



                // if ($product->coupon_code != null) {
                //     // if (Session::has('club_point')) {
                //     //     $order->club_point = Session::get('club_point');
                //     // }
                //     $order->coupon_discount = $coupon_discount;
                //     $order->grand_total -= $coupon_discount;

                //     $coupon_usage = new CouponUsage;
                //     $coupon_usage->user_id = Auth::user()->id;
                //     $coupon_usage->coupon_id = Coupon::where('code', $product->coupon_code)->first()->id;
                //     $coupon_usage->save();
                // }

            }

            $shipping = $all_free ? 0 : get_setting('flat_rate_shipping_cost'); // default cost 50
            $grand_total = $subtotal + $tax + $shipping;


            $total_without_shipment = $subtotal + $tax;
            $reward_discount = 0;

            // Redeem Point
            $user_points = $user->points; // 5

            if($user_points > 0 && get_setting('reward_activation') == 1){


                $tk_to_redeem = $user_points * (1 / get_setting("club_point_convert_rate")); // 250

                if($tk_to_redeem > $total_without_shipment){ // 250 > 30

                    $tk_to_points = $total_without_shipment / get_setting("club_point_convert_rate"); /// 30
                    $order->points_redeem  = $tk_to_points;
                    $user->points = $user_points - $tk_to_points;
                    $reward_discount = $total_without_shipment;

                }else{

                    $reward_discount = $tk_to_redeem;
                    $order->points_redeem = $user_points;
                    $user->points = 0;

                }

                $user->save();

            }

            $order->coupon_discount = $coupon_discount;
            $order->reward_discount = $reward_discount;
            $order->grand_total = $grand_total;
            $order->shipping_cost = $shipping;

            $order_code = "100001";
            $order_code_len = strlen($order_code);
            $order_id_len = strlen("$order->id");
            $new_order_code = $order->id;

            if($order_id_len < $order_code_len){
                $order_code = substr($order_code, 0, $order_code_len - $order_id_len);
                $new_order_code = $order_code.$order->id;
            }

            $order->code = $new_order_code;

            $order->save();

            // Acrue Points
            if(get_setting('reward_activation') == 1){
                $points_to_get = $total_without_shipment * 0.05;
                // if($user->points >= 200) $points_to_get += 50; // give 50 bounus if has 200 point
                $order->points_accrued = $points_to_get;
                // $user->points = $user->points + $points_to_get;
                // $user->save();
            }

            // $points_to_tk = get_setting("club_point_convert_rate") * $points_to_get;
            // $user->balance = $user->balance + $points_to_tk;

            $order->save();

            // NotificationUtility::sendOrderPlacedNotification($order);
            Cart::where('temp_user_id', $temp_user_id)->delete();

            DB::commit();

            return response()->json([
                'order_id' => $order->id,
                'result' => true,
                'order_no' => $order->code,
                'total' => $order->grand_total - $order->coupon_discount - $order->reward_discount,
                'coupon_discount' => $order->coupon_discount,
                'reward_discount' => $order->reward_discount,
                'address' => $shippingAddress,
                'message' => translate('Your order has been placed successfully')
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }


    public function checkCoupon($code)
    {
        $cart_items = Cart::where('temp_user_id', request('temp_user_id'))->get();
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



    public function getPreferredDeliveryDate($index){
        $delivery_slots = [];

        foreach (range(1,5) as $i => $day) {
            array_push($delivery_slots, date('Y-m-d', strtotime("+$i day")));
        }

        return $index ? $delivery_slots[$index - 1] : $delivery_slots[0];
    }

    public function cancelUserOrder(Request $request){

        $order = Order::findOrFail($request->order_id);

        $now = new \DateTime();
        $currentTimestamp = $now->getTimestamp();
        if($order->date + 1800 <= $currentTimestamp){
            return response()->json([
                "message" => "Cancellation time Expired",
                "errors" => [
                    "order" => ["Cancellation time Expired"]
                ]
            ], 422);
        }

        if($order->delivery_status != "pending"){
            return response()->json([
                "message" => "Order is already in Processing",
                "errors" => [
                    "order" => ["Order is already in Processing"]
                ]
            ], 422);
        }


        DB::beginTransaction();
        try{
            if ($order != null) {
                foreach ($order->orderDetails as $key => $orderDetail) {

                    $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variation)->first();
                    if ($product_stock != null) {
                        $product_stock->qty += $orderDetail->quantity;
                        $product_stock->save();
                    }

                    // $orderDetail->delete();
                }
                // $order->delete();

                $user = Auth::user();
                // $points = $user->points - $order->points_accrued;
                $points = $user->points + $order->points_redeem;
                $user->points = $points;
                $user->save();

                $order->delivery_status = "cancelled";
                $order->reason = $request->reason;
                // $order->points_accrued = 0;
                // $order->points_redeem = 0;
                $order->update();

                DB::commit();

                return response()->json([
                    "success" => true,
                    "message" => "Order Cancelled Successfully"
                ], 200);
            }

            return response()->json([
                "success" => false,
                "message" => "Failed to Cancel Order",
                "errors" => [
                    "order" => ["Failed to Cancel Order"]
                ]
            ], 422);

            return back();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }

    

}
