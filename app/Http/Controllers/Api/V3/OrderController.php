<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Address;
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
use App\Http\Resources\V3\CartResource;
use App\Http\Resources\V3\DeliveredOrderIndex;
use App\Http\Resources\V3\OrderIndexResource;
use App\Http\Resources\V3\OrderShowResource;
use App\Http\Resources\V3\UndeliveredOrderIndex;
use App\Models\ProductStock;
use Auth;

class OrderController extends Controller
{

   public function __construct()
    {
        $this->middleware("auth:sanctum")->except(["getPaymentAmount", "undeliveredOrders"]);
    }

    public function index(){

        $orders = Order::where("user_id", Auth::user()->id)->orderBy("created_at", "desc")->paginate(20);
        return OrderIndexResource::collection($orders);

    }


    public function store(Request $request)
    {

        if(!$request->address){
            return response()->json([
                "errors" => [
                    "message" => [translate('No Shipping Address Found')]
                ]
            ], 422);
        }

        if(!$request->payment_option){
            return response()->json([
                "errors" => [
                    "message" => [translate('Select Payment Option.')]
                ]
            ], 422);
        }

        $carts = Cart::where('user_id', auth()->user()->id)->get();
        // $carts = Cart::where('temp_user_id', $request->temp_id)->get();

        if (count($carts) <= 0) {
            return response()->json([
                "errors" => [
                    "message" => [translate('Cart is Empty')]
                ]
            ], 422);
        }

        $has_b2b_products = Cart::whereHas("product", function($c){
            $c->where("is_b2b_product", true);
        })->first();


        $user = Auth::user();
        // if($user->is_b2b_user != 1 && $has_b2b_products){
        //     return response()->json([
        //         "errors" => [
        //             "message" => [translate('Only B2B Customer can Order B2B Product')],
        //             "b2b" => $has_b2b_products
        //         ]
        //     ], 422);
        // }


        $emergency_order = Order::where("user_id", $user->id)->where("is_emergency_order", true)->where("payment_status", "!=", "paid")->first();

        if($request->use_emergency_balance  && get_setting("emergency_balance") == 1){


            $upper_limit = get_setting("emergency_balance_up_limit");
            $lower_limit = get_setting("emergency_balance_lower_limit");

            $cart_total = $this->getCartTotal();

            $error_message = "";

            if($cart_total > $upper_limit){
                $error_message = "Maximum Purchase Limit is ৳$upper_limit to use Emergency Balance";
            }

            if($cart_total < $lower_limit){
                $error_message = "Minimum Purchase Limit is ৳$lower_limit to use Emergency Balance";
            }


            if($emergency_order) $error_message = "You have Previous Due to Pay";

            if($user->orders()->where("delivery_status", "delivered")->count() <= 0){

                $error_message = "Please Do a Regular Purchase before using Emergency Balance";
            }

            if($error_message){
                return response()->json([
                    "errors" => [
                        "message" => [$error_message]
                    ]
                ], 422);
            }
        }

        if($user->lock_emergency && get_setting("emergency_balance") == 1){
            return response()->json([
                "errors" => [
                    "message" => ["Please Clear Previous Due to Re-Order"]
                ]
            ], 422);
        }

        $address = Address::where('id', $request->address)->first();
        $shippingAddress = [];
        if ($address != null) {
            $shippingAddress['name']        = $address->name;
            $shippingAddress['email']       = Auth::user()->email;
            $shippingAddress['address']     = $address->address;
            $shippingAddress['country']     = $address?->country->name ?? "Bangladesh";
            $shippingAddress['state']       = $address?->state->name ?? "";
            $shippingAddress['city']        = $address?->city->name ?? null;
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
            $order->ordered_from = $request->ordered_from ?: "web";
            $order->tracking_code = rand(1000, 9999);
            $order->city_id = $address->city_id;
            $order->preferred_delivery_date = $this->getPreferredDeliveryDate($request->delivery_date);
            $order->delivery_slot = $request->delivery_time;
            $order->user_id = Auth::user()->id;
            $order->shipping_address = json_encode($shippingAddress);
            $order->shipping_type = "delivery";
            $order->payment_type = $request->payment_option;
            $order->date = strtotime('now');
            $order->save();

            $all_free = false;
            foreach ($carts as $cartItem){

                $product = Product::find($cartItem['product_id']);

                if($product->free_shipping) $all_free = true;

                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;

                $order_detail->price = $product->unit_price * $cartItem['quantity'];
                $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];

                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->save();


                $product->current_stock -= $cartItem['quantity'];
                $product->num_of_sale += $cartItem['quantity'];
                $product->save();

                $order->seller_id = $product->user_id;

            }

            $cart_total = $this->getCartTotal();
            $shipping = $all_free ? 0 : get_setting('flat_rate_shipping_cost'); // default cost 50

            if($user->delivery_subscription && get_setting("delivery_subscription") == 1){
                $shipping = 0;
            }

            $grand_total = $cart_total + $shipping;


            $total_without_shipment = $cart_total;

            $reward_discount = 0;
            $coupon_discount = 0;


            if(!$has_b2b_products){

                $coupon_result = $this->applyCouponCode($request, true);

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
                // End of Redeem Point


                // Giving Coupon/Referral Discount
                if(array_key_exists("status_code", $coupon_result) &&  $coupon_result['status_code'] == 200){

                    $coupon_result_data = $coupon_result["data"];

                    if($coupon_result_data['type'] == 'referral'){

                        // $user_points = get_setting("referred_user_points") ?? 0;
                        // $referer_points = get_setting("referer_points") ?? 0;

                        // $user = Auth::user();
                        // $user->points += $user_points;
                        // $user->is_new_user = false;
                        // $user->save();

                        // $referer = User::where("referral_code", $request->code)->first();
                        // $referer->points += $referer_points;
                        // $referer->save();

                        $order->referral_code = $request->code;

                    }else if($coupon_result_data['type'] == 'coupon'){

                        $order->applied_coupon_code = $request->code;
                        $coupon_discount = $coupon_result_data['amount'];
                    }

                }else{
                    // Acrue Points or Giving Reward Points
                    if(get_setting('reward_activation') == 1){
                        $points_to_get = $total_without_shipment * 0.05;
                        $order->points_accrued = $points_to_get;
                    }
                }

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

            if($emergency_order && get_setting("emergency_balance") == 1){
                $user->lock_emergency = true;
                $user->save();
            }

            if($request->use_emergency_balance && get_setting("emergency_balance") == 1){
                $order->payment_status = "emergency";
                $order->is_emergency_order = true;
            }

            $order->save();



            // NotificationUtility::sendOrderPlacedNotification($order);
            // session()->put("order_id", $order->id);
            Cart::where('user_id', auth()->user()->id)->delete();

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

    public function show($id){

        $order = Order::findOrFail($id);
        // session()->put("order_id", $id);
        return new OrderShowResource($order);

    }


    // public function checkCoupon($code)
    // {
    //     $cart_items = Cart::where('user_id', Auth::user()->id)->get();
    //     $coupon = Coupon::where('code', $code)->first();

    //     if ($cart_items->isEmpty()) {
    //         return [
    //             'result' => false,
    //             'message' => translate('Cart is empty')
    //         ];
    //     }

    //     if ($coupon == null) {
    //         return [
    //             'result' => false,
    //             'message' => translate('Invalid coupon code!')
    //         ];
    //     }

    //     $in_range = strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date;

    //     if (!$in_range) {
    //         return [
    //             'result' => false,
    //             'message' => translate('Coupon expired!')
    //         ];
    //     }

    //     $is_used = CouponUsage::where('user_id', auth()->user()->id)->where('coupon_id', $coupon->id)->first() != null;

    //     if ($is_used) {
    //         return [
    //             'result' => false,
    //             'message' => translate('You already used this coupon!')
    //         ];
    //     }


    //     $coupon_details = json_decode($coupon->details);

    //     if ($coupon->type == 'cart_base') {

    //         $cart_total = $this->getCartTotal();

    //         if ($cart_total >= $coupon_details->min_buy) {

    //             if ($coupon->discount_type == 'percent') {

    //                 $coupon_discount = ($cart_total * $coupon->discount) / 100;

    //                 if ($coupon_discount > $coupon_details->max_discount) {
    //                     $coupon_discount = $coupon_details->max_discount;
    //                 }

    //             } elseif ($coupon->discount_type == 'amount') {

    //                 $coupon_discount = $coupon->discount;

    //             }

    //             // Cart::where('user_id', auth()->user()->id)->update([
    //             //     'discount' => $coupon_discount / count($cart_items),
    //             //     'coupon_code' => $request->coupon_code,
    //             //     'coupon_applied' => 1
    //             // ]);


    //             return [
    //                 'result' => true,
    //                 'coupon_discount' => $coupon_discount,
    //             ];


    //         }else{
    //             return [
    //                 'result' => false,
    //                 'message' => translate('Coupon Requires Minimum Order amount '. single_price($coupon_details->min_buy))
    //             ];
    //         }
    //     } elseif ($coupon->type == 'product_base') {
    //         $coupon_discount = 0;
    //         foreach ($cart_items as $key => $cartItem) {
    //             foreach ($coupon_details as $key => $coupon_detail) {
    //                 if ($coupon_detail->product_id == $cartItem['product_id']) {
    //                     if ($coupon->discount_type == 'percent') {
    //                         $coupon_discount += $cartItem->product->unit_price * $coupon->discount / 100;
    //                     } elseif ($coupon->discount_type == 'amount') {
    //                         $coupon_discount += $coupon->discount;
    //                     }
    //                 }
    //             }
    //         }


    //         // Cart::where('user_id', auth()->user()->id)->update([
    //         //     'discount' => $coupon_discount / count($cart_items),
    //         //     'coupon_code' => $request->coupon_code,
    //         //     'coupon_applied' => 1
    //         // ]);

    //         return [
    //             'result' => true,
    //             'coupon_discount' => $coupon_discount,
    //         ];

    //     }


    // }

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

    public function applyCouponCode(Request $request, $fromController = false)
    {

        $cart_items = Cart::where('user_id', Auth::user()->id)->get();

        if ($cart_items->isEmpty()) {
            $err = [
                "errors" => [
                    'code' => [translate('Cart is empty')]
                ]
            ];

            return $fromController ? $err : response()->json($err, 422);
        }

        $coupon = Coupon::where('code', $request->code)->first();
        $referral = User::where("referral_code", $request->code)->where("id", "!=", Auth::user()->id)->first();


        if ($coupon == null && $referral == null) {
            $err = [
                "errors" => [
                    'code' => [translate('Invalid code!')]
                ]
            ];
            return $fromController ? $err : response()->json($err, 422);
        }


        $user = Auth::user();

        if($user->is_b2b_user){

            $has_b2b_products = Cart::whereHas("product", function($c){
                $c->where("is_b2b_product", true);
            })->first();

            if($has_b2b_products){
                $err = [
                    "errors" => [
                        'code' => [translate('B2B Products are not Eligible to get Discount')]
                    ]
                ];
                return $fromController ? $err : response()->json($err, 422);
            }

        }


        if($coupon){
            // apply coupon code
            $res = $this->apply_coupon_code($coupon, $cart_items);
            return $fromController ? $res : response()->json($res['data'], $res['status_code']);
        }

        if($referral){
            // apply referral code
            $res = $this->apply_referral_code($referral, $cart_items);
            return $fromController ? $res : response()->json($res['data'], $res['status_code']);
        }

        // return invalid code

        $err = [
            "errors" => [
                'code' => [translate('Invalid code!')]
            ]
        ];

        return $fromController ? $err : response()->json($err, 422);

    }

    public function apply_referral_code($referer, $cart_items){

        // ami customer ami ekti referral code pelam
        // ami ei referral code amar ek friend k dilam
        // se referral code diye order korlo se pelo 10 ami pelam 20 taka

        // problems
        // se abar amar referral code use korlo keu pabe na karon ekjon referer ek bar e pabe ar je refer korbe se o ekbar
        //

        $user = Auth::user();

        // user already got referral bonus trying again
        // $already_used = Order::where("referral_code", "!=", null)
        //                 ->where("user_id", $user->id)->first();

        if(!$user->is_new_user){
            return [
                "data" => [
                    "errors" => [
                        'code' => [translate('You are not eligible!')]
                    ]
                ],
                "status_code" => 422
            ];
        }

        // user is eligible and not used referral before


        $cart_total = $this->getCartTotal();

        $all_free = false;
        foreach ($cart_items as $cartItem){
            $product = Product::find($cartItem['product_id']);
            if($product->free_shipping) $all_free = true;
        }

        $shipping_cost = $all_free ? 0 : get_setting("flat_rate_shipping_cost");

        $cart_total = $this->getCartTotal();

        $user_points = get_setting("referred_user_points") ?? 0;
        $referer_points = get_setting("referer_points") ?? 0;

        // $user = Auth::user();
        // $user->points = $user_points;
        // $user->save();

        // $referer->points = $referer_points;
        // $referer->save();

        $referral_discount = 0;

        return [
            "data" => [
                "cart" => [
                    "items" => CartResource::collection($cart_items),
                    "cart_count" => count($cart_items),
                    "cart_total" => currency_symbol().$cart_total,
                    "shipping_cost" => currency_symbol(). $shipping_cost,
                    "total_cost" => currency_symbol().$cart_total + $shipping_cost - $referral_discount
                ],
                'type' => "referral",
                "amount" => $user_points,
                "message" => "You will get $user_points Points"
            ],
            "status_code" => 200
        ];

    }

    public function apply_coupon_code($coupon, $cart_items){

        $in_range = strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date;

        if (!$in_range) {
            return [
                "data" => [
                    "errors" => [
                        'code' => [translate('Code expired!')]
                    ]
                ],
                "status_code" => 422
            ];
        }

        $is_used = CouponUsage::where('user_id', auth()->user()->id)->where('coupon_id', $coupon->id)->first() != null;

        if ($is_used) {
            return [
                "data" => [
                    "errors" => [
                        'code' => [translate('You already used this Code!')]
                    ]
                ],
                "status_code" => 422
            ];
        }

        $coupon_details = json_decode($coupon->details);

        $cart_total = $this->getCartTotal();


        if ($coupon->type == 'cart_base') {

            if ($cart_total >= $coupon_details->min_buy) {

                if ($coupon->discount_type == 'percent') {
                    $coupon_discount = ($cart_total * $coupon->discount) / 100;
                    if ($coupon_discount > $coupon_details->max_discount) {
                        $coupon_discount = $coupon_details->max_discount;
                    }
                } elseif ($coupon->discount_type == 'amount') {
                    $coupon_discount = $coupon->discount;
                }

                // return response()->json([
                //     'result' => true,
                //     'coupon_discount' => $coupon_discount,
                //     'total' => $this->getCartTotal() - $coupon_discount,
                //     'message' => translate('Coupon Applied')
                // ]);

                $cart_total = $this->getCartTotal();

                $all_free = false;
                foreach ($cart_items as $cartItem){
                    $product = Product::find($cartItem['product_id']);
                    if($product->free_shipping) $all_free = true;
                }

                $shipping_cost = $all_free ? 0 : get_setting("flat_rate_shipping_cost");

                return [
                    "data" => [
                        "cart" => [
                            "items" => CartResource::collection($cart_items),
                            "cart_count" => count($cart_items),
                            "cart_total" => currency_symbol().$cart_total,
                            "shipping_cost" => currency_symbol(). $shipping_cost,
                            "total_cost" => currency_symbol().$cart_total + $shipping_cost - $coupon_discount
                        ],
                        'type' => "coupon",
                        "amount" => $coupon_discount,
                        "message" => currency_symbol(). "$coupon_discount Discount Applied"
                    ],
                    "status_code" => 200
                ];


            }else{
                return [
                    "data" => [
                        "errors" => [
                            'code' => [translate('Coupon Requires Minimum Order amount '. single_price($coupon_details->min_buy))]
                        ]
                    ],
                    "status_code" => 422
                ];
            }
        }

    }

    public function remove_coupon_code(Request $request)
    {

        return response()->json([
            "result" => true,
            "total" => $this->getCartTotal()
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


    // get payment amount for bkash payment
    public function getPaymentAmount($id){

        $order = Order::findOrFail($id);
        $amount = $order->grand_total - $order->coupon_discount - $order->reward_discount;
        // $order_id = $order->id;
        return response(["data" => $amount], 200);

        // $html = "";
        // $html .= view("bkash", compact("amount", "order_id"));
        // return response(["data" => $html], 200);

    }

    public function profileUpdate(Request $request){

        $user = Auth::user();
        // $user = User::where("id", $id)->where("user_id", Auth::user()->id)->first();
        // $user = User::where("id", $id)->first();

        $user->name = $request->name ?? "";
        $user->email = $request->email;
        $user->password = $request->password ;
        $user->address = $request->address ;
        $user->city = $request->city;
        $user->postal_code = $request->postal_code;
        $user->postal_code = $request->postal_code;
        $user->phone = $request->phone;
        $user->country = $request->country;
        $user->referral_code = $request->referral_code;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => translate('Updated successfully')
        ]);

    }

    public function deliveredOrder(Request $request){

        $delivered = Order::where("delivery_status", 'delivered')->paginate($request->per_page ?? 10);
        return DeliveredOrderIndex::collection($delivered);
    }

    public function undeliveredOrder(Request $request){

        $delivered = Order::where("delivery_status", "!=", "delivered")->paginate($request->per_page ?? 10);

        return UndeliveredOrderIndex::collection($delivered);


    }

}
