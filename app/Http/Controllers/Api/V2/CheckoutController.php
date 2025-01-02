<?php


namespace App\Http\Controllers\Api\V2;


use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use App\Models\Cart;
use Auth;

class CheckoutController
{
    public function apply_coupon_code(Request $request)
    {

        $cart_items = Cart::where('temp_user_id', $request->temp_user_id)->get();
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


                return response()->json([
                    'result' => true,
                    'coupon_discount' => $coupon_discount,
                    'total' => $this->getCartTotal() - $coupon_discount,
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

            return response()->json([
                'result' => true,
                'coupon_discount' => $coupon_discount,
                'total' => $this->getCartTotal() - $coupon_discount,
                'message' => translate('Coupon Applied Successfully')
            ]);

        }


    }

    public function getCartTotal(){

        $cart = \App\Models\Cart::where('temp_user_id', request('temp_user_id'))->get();
        $total = 0;
        foreach($cart as $key => $cartItem){
            $total = $total + ($cartItem->price + $cartItem->tax) * $cartItem->quantity;
        }

        return $total;
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
}
