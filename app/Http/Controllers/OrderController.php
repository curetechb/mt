<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\OTPVerificationController;
use Illuminate\Http\Request;
use App\Http\Controllers\ClubPointController;
use App\Http\Resources\V3\OrderIndexResource;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\CommissionHistory;
use App\Models\Color;
use App\Models\OrderDetail;
use App\Models\CouponUsage;
use App\Models\Coupon;
use App\OtpConfiguration;
use App\Models\User;
use App\Models\BusinessSetting;
use App\Models\CombinedOrder;
use App\Models\SmsTemplate;
use Auth;
use Session;
use DB;
use Mail;
use App\Mail\InvoiceEmailManager;
use App\Models\City;
use App\Models\ClubPoint;
use App\Models\DeliveryBoy;
use App\Utility\NotificationUtility;
use App\Utility\SmsUtility;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\OrdersExport;
use App\Models\OrdersExport2;
use App\Models\SalePayment;
use App\Models\SkywalkOrderExport;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $orders = DB::table('orders')
            ->orderBy('id', 'desc')
            //->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->where('seller_id', Auth::user()->id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_status != null) {
            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }

        $orders = $orders->paginate(15);

        foreach ($orders as $key => $value) {
            $order = \App\Models\Order::find($value->id);
            $order->viewed = 1;
            $order->save();
        }

        return view('frontend.user.seller.orders', compact('orders', 'payment_status', 'delivery_status', 'sort_search'));
    }

    // All Orders
    public function all_orders(Request $request)
    {

        $sort_search = null;
        $delivery_status = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $orders = Order::orderBy('id', 'desc')->where("is_b2b_order", false);

        if ($request->has('search')) {
            $sort_search = $request->search;
            // $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            $orders = $orders->where(function($p) use($sort_search){

                $p->where('code', 'like', '%' . $sort_search . '%')
                    ->orWhereHas('user', function($q) use ($sort_search){
                        $q->where("phone", "like", "%$sort_search%");
                    });

            });
        }

        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }

        if($request->filter_by == "delivery_time"){

            if ($start_date != null) {
                $orders = $orders->whereDate('delivery_time', '>=', date('Y-m-d', strtotime($start_date)));
            }
            if ($end_date != null) {
                $orders = $orders->whereDate('delivery_time', '<=', date('Y-m-d', strtotime($end_date)));
            }

        }else{

            if ($start_date != null) {
                $orders = $orders->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)));
            }
            if ($end_date != null) {
                $orders = $orders->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }

        }


        if($request->export){
            if($request->export == "export1"){
                return Excel::download(new OrdersExport($orders), 'orders.xlsx');
            }else{
                return Excel::download(new OrdersExport2($orders), 'orders.xlsx');
            }
        }

        if(Auth::user()->user_type != 'admin' && Auth::user()->staff->role->id == 11){
            $orders = $orders->whereDate('created_at', ">=",  "2023-02-15");
        }

        // $orders = $orders->paginate(15);

        if (Auth::user()->user_type == 'admin' || Auth::user()->staff->role->id != 4){
            $orders = $orders->paginate(15);
        }else{
            $orders = $orders->where('manage_by', 'sonar')->paginate(15);
        }

        // if($request->export && $request->export == "export"){
        //     return Excel::download(new OrdersExport($orders), 'orders.xlsx');
        // }else{
        //     return view('backend.sales.all_orders.index', compact('orders', 'sort_search', 'delivery_status', 'start_date', 'end_date'));
        // }

        return view('backend.sales.all_orders.index', compact('orders', 'sort_search', 'delivery_status', 'start_date', 'end_date'));
    }

    public function export(){
        return Excel::download(new SkywalkOrderExport,'orders.xlsx');
    }

    public function all_orders_show($id)
    {
        // $products = Product::all();
        $order = Order::findOrFail(decrypt($id));

        session()->put("order_id", $order->id);

        $order_shipping_address = json_decode($order->shipping_address);
        // $delivery_boys = User::where('state', $order_shipping_address->state)
        //     ->where('user_type', 'delivery_boy')
        //     ->get();
        $delivery_boys = User::where('state', '!=',null)
            ->where('user_type', 'delivery_boy')
            ->get();


        $socket_order = new OrderIndexResource($order);


        $sale_payments = SalePayment::where("order_id", $order->id)->get();
        return view('backend.sales.all_orders.show', compact('order', 'delivery_boys', 'order_shipping_address', 'socket_order', 'sale_payments'));
        // return view('backend.sales.all_orders.show', compact('order'));
    }

    public function add_product(Request $request){


        $order = Order::find($request->order_id);
        $product = Product::find($request->product_id);

        $subtotal = $product->unit_price * $request->quantity;


        // $tax = $product->tax * $request->quantity;
        // $coupon_discount = $product->discount;
        $product_variation = $product->variation;

        $total = $order->grand_total + $subtotal;
        $order->grand_total = $total;
        $order->save();

                // $subtotal += $cartItem['price'] * $cartItem['quantity'];
                // $tax += $cartItem['tax'] * $cartItem['quantity'];
                // $coupon_discount += $cartItem['discount'];

                // $product_variation = $cartItem['variation'];

                // $product_stock = $product->stocks->where('variant', $product_variation)->first();

                // if ($product->digital != 1) {
                //     $product_stock->qty -= $cartItem['quantity'];
                //     $product_stock->save();
                // }

                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->price = $product->unit_price * $request->quantity;
                $order_detail->tax = $product->tax * $request->quantity;



                // $order_detail->shipping_type = $cartItem['shipping_type'];
                // $order_detail->product_referral_code = $cartItem['product_referral_code'];
                // $order_detail->shipping_cost = $cartItem['shipping_cost'];


                // $total_weight += $product->weight * $cartItem['quantity']; // product total weight
                // $shipping += $order_detail->shipping_cost;
                //End of storing shipping cost

                // $total = $order->grand_total + $order->sub_total+ $order->tax+ $order->shipping;


                $order_detail->quantity = $request->quantity;
                $order_detail->save();

                $product->num_of_sale = $request->quantity;
                $product->save();

                $order->seller_id = $product->user_id;

                return redirect()->back();

    }

    public function order_destroy($id){

        $orderDetails = OrderDetail::find($id);

        $order  = $orderDetails->order;
        // $product = $orderDetails->product;

        $total = $order->grand_total - $orderDetails->price;
        $order->grand_total = $total;
        $order->save();

        $orderDetails->delete();
        return redirect()->back();
    }

    public function all_orders_cancel(Request $request,$id)
    {
        DB::commit();
        try{
            $order = Order::findOrFail($id);

            $user = $order->user;
            // $points = $user->points - $order->points_accrued;
            // $points = $user->points + $order->points_redeem;
            // $user->points = $points;
            // $user->save();

            $order->delivery_status = "cancelled";
            $order->reason = $request->reason;
            foreach ($order->orderDetails as $key => $orderDetail) {

                $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variation)->first();
                if ($product_stock != null) {
                    $product_stock->qty += $orderDetail->quantity;
                    $product_stock->save();
                }

                $product = $orderDetail->product;
                $product->current_stock = $product->current_stock + $orderDetail->quantity;
                $product->save();
                // $orderDetail->delete();
            }
            // $order->points_accrued = 0;
            // $order->points_redeem = 0;
            $order->update();


            DB::beginTransaction();
            Session::flash("error", translate('Order has been Cancelled'));
            return redirect()->back();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    // Inhouse Orders
    public function admin_orders(Request $request)
    {

        $date = $request->date;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = Order::orderBy('id', 'desc')
                        ->where('seller_id', $admin_user_id);

        if ($request->payment_type != null) {
            $orders = $orders->where('payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->whereDate('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.inhouse_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'date'));
    }

    public function show($id)
    {

        $order = Order::findOrFail(decrypt($id));
        $order_shipping_address = json_decode($order->shipping_address);
        $delivery_boys = User::where('city', $order_shipping_address->city)
            ->where('user_type', 'delivery_boy')
            ->get();

        $order->viewed = 1;
        $order->save();
        return view('backend.sales.inhouse_orders.show', compact('order', 'delivery_boys'));
    }

    // Seller Orders
    public function seller_orders(Request $request)
    {

        $date = $request->date;
        $seller_id = $request->seller_id;
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $admin_user_id = User::where('user_type', 'admin')->first()->id;
        $orders = Order::orderBy('code', 'desc')
            ->where('orders.seller_id', '!=', $admin_user_id);

        if ($request->payment_type != null) {
            $orders = $orders->where('payment_status', $request->payment_type);
            $payment_status = $request->payment_type;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($date != null) {
            $orders = $orders->whereDate('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
        }
        if ($seller_id) {
            $orders = $orders->where('seller_id', $seller_id);
        }

        $orders = $orders->paginate(15);
        return view('backend.sales.seller_orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'admin_user_id', 'seller_id', 'date'));
    }

    public function seller_orders_show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order->viewed = 1;
        $order->save();
        return view('backend.sales.seller_orders.show', compact('order'));
    }


    // Pickup point orders
    public function pickup_point_order_index(Request $request)
    {
        $date = $request->date;
        $sort_search = null;
        $orders = Order::query();
        if (Auth::user()->user_type == 'staff' && Auth::user()->staff->pick_up_point != null) {
            $orders->where('shipping_type', 'pickup_point')
                    ->where('pickup_point_id', Auth::user()->staff->pick_up_point->id)
                    ->orderBy('code', 'desc');

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders', 'sort_search', 'date'));
        } else {
            $orders->where('shipping_type', 'pickup_point')->orderBy('code', 'desc');

            if ($request->has('search')) {
                $sort_search = $request->search;
                $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
            }
            if ($date != null) {
                $orders = $orders->whereDate('orders.created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])))->whereDate('orders.created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])));
            }

            $orders = $orders->paginate(15);

            return view('backend.sales.pickup_point_orders.index', compact('orders', 'sort_search', 'date'));
        }
    }

    public function pickup_point_order_sales_show($id)
    {
        if (Auth::user()->user_type == 'staff') {
            $order = Order::findOrFail(decrypt($id));
            $order_shipping_address = json_decode($order->shipping_address);
            $delivery_boys = User::where('city', $order_shipping_address->city)
                ->where('user_type', 'delivery_boy')
                ->get();

            return view('backend.sales.pickup_point_orders.show', compact('order', 'delivery_boys'));
        } else {
            $order = Order::findOrFail(decrypt($id));
            $order_shipping_address = json_decode($order->shipping_address);
            $delivery_boys = User::where('city', $order_shipping_address->city)
                ->where('user_type', 'delivery_boy')
                ->get();

            return view('backend.sales.pickup_point_orders.show', compact('order', 'delivery_boys'));
        }
    }

    /**
     * Display a single sale to admin.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function getPreferredDeliveryDate($index){
        $delivery_slots = [];

        foreach (range(1,5) as $i => $day) {
            array_push($delivery_slots, date('Y-m-d', strtotime("+$i day")));
        }

        return $index ? $delivery_slots[$index - 1] : $delivery_slots[0];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if(!$request->address_id){
            Session::flash("error",translate('No Shipping Address Found'));
            return redirect()->route("checkout.shipping_info");
        }

        if(!$request->payment_option){
            Session::flash("error",translate('Select Payment Option.'));
            return redirect()->route("checkout.shipping_info");
        }

        // $temp_user_id = $request->session()->get('temp_user_id');

        $carts = Cart::where("user_id", Auth::user()->id)
            ->get();

        if (count($carts) <= 0) {
            Session::flash("error",translate('Your cart is empty'));
            return redirect()->route("checkout.shipping_info");
        }

        foreach($carts as $key => $cartItem){
            $product = $cartItem->product;
            $stock = $product->stocks->where('variant', null)->first();
            if ($stock->qty <= $product->low_stock_quantity) {
                Session::flash("error",translate('Some Product in the Cart are Out of Stock'));
                return redirect()->route("checkout.shipping_info");
            }
        }

        // if (!$request->delivery_date || !$request->delivery_time) {
        //    Session::flash("success",translate('Please Select Preferred Delivery Time'))->warning();
        //     return redirect()->route("checkout.shipping_info");
        // }


        $coupon_result = $this->checkCoupon($request->code);
        $coupon_discount = $coupon_result != null && $coupon_result['result'] == true ? $coupon_result['coupon_discount'] : 0;


        $address = Address::where('id', $request->address_id)->first();

        $shippingAddress = [];
        if ($address != null) {
            $shippingAddress['name']        = $address->name;
            $shippingAddress['email']       = Auth::user()->email;
            $shippingAddress['address']     = $address->address;
            $shippingAddress['country']     = $address->country->name ?? "Bangladesh";
            $shippingAddress['state']       = $address->state->name;
            $shippingAddress['city']        = $address->city->name ?? null;
            $shippingAddress['postal_code'] = $address->postal_code;
            $shippingAddress['phone']       = $address->phone ?: Auth::user()->phone;
            $shippingAddress['floor_no']       = $address->floor_no;
            $shippingAddress['apartment']       = $address->apartment;
            if ($address->latitude || $address->longitude) {
                $shippingAddress['lat_lang'] = $address->latitude . ',' . $address->longitude;
            }
        }

        // $combined_order = new CombinedOrder;
        // $combined_order->user_id = Auth::user()->id;
        // $combined_order->shipping_address = json_encode($shippingAddress);
        // $combined_order->save();

	    $user = Auth::user();
        $user_orders = $user->orders()->count();
        if($user_orders <= 0){
            $user->name = $address->name;
            $user->save();
        }


        DB::beginTransaction();
        try{

            $order = new Order;
            $order->ordered_from = "web";
            $order->tracking_code = rand(1000, 9999);
            $order->city_id = $address->city_id;
            $order->applied_coupon_code = $request->code;
            $order->preferred_delivery_date = $this->getPreferredDeliveryDate($request->delivery_date);
            $order->delivery_slot = $request->delivery_time;
            $order->user_id = Auth::user()->id;
            $order->shipping_address = json_encode($shippingAddress);
            $order->payment_type = $request->payment_option;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->date = strtotime('now');
            $order->save();

            $subtotal = 0;
            $tax = 0;
            // $shipping = get_setting('flat_rate_shipping_cost'); // default cost 50
            // $total_weight = 0;

            $all_free = false;
            foreach ($carts as $cartItem){

                    $product = Product::find($cartItem['product_id']);
                    if($product->free_shipping) $all_free = true;

                    $subtotal += $cartItem->product->unit_price * $cartItem['quantity'];
                    $tax += $cartItem['tax'] * $cartItem['quantity'];
                    // $coupon_discount += $cartItem['discount'];

                    $product_variation = $cartItem['variation'];

                    $product_stock = $product->stocks->where('variant', $product_variation)->first();

                    if ($product->digital != 1) {
                        $product_stock->qty -= $cartItem['quantity'];
                        $product_stock->save();
                    }

                    $order_detail = new OrderDetail;
                    $order_detail->order_id = $order->id;
                    $order_detail->seller_id = $product->user_id;
                    $order_detail->product_id = $product->id;
                    $order_detail->variation = $product_variation;
                    $order_detail->price = $cartItem->product->unit_price * $cartItem['quantity'];
                    $order_detail->tax = $cartItem['tax'] * $cartItem['quantity'];

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
            // $request->session()->put('order_id', $order->id);
            // $request->session()->put('order_code', $order->code);

            // store order id for bkash payment
            session()->put("order_id", $order->id);

            Cart::where('user_id', Auth::user()->id)->delete();


            DB::commit();
            if($order->payment_type == "bkash"){
                return redirect()->route('order_confirmed', ['id' => $order->id, 'payment_method' => 'bkash']);
            }

            return redirect()->route('order_confirmed', $order->id);

        }catch(\Exception $e){
            dd($e);
            DB::rollBack();
            throw $e;
        }

    }



    public function checkCoupon($code)
    {
        $cart_items = Cart::where('user_id', auth()->user()->id)->get();
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



    public function order_confirmed($id)
    {

        $order = Order::where('id', $id)->where("user_id", Auth::user()->id)->first();

        //Session::forget('club_point');
        //Session::forget('combined_order_id');
        return view('frontend.order_confirmed', compact('order'));
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    public function cancelUserOrder(Request $request){

        $order = Order::findOrFail($request->cancel_order_id);

        $now = new \DateTime();
        $currentTimestamp = $now->getTimestamp();
        if($order->date + 1800 <= $currentTimestamp){
           Session::flash("error",translate('Cancellation time Expired'));
            return back();
        }

        if($order->delivery_status != "pending"){
           Session::flash("error",translate('Order is already in Processing'));
            return back();
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

                Session::flash("success", translate('Order Cancelled successfully'));
            } else {
                Session::flash("error", translate('Something went wrong'));
            }

            DB::commit();
            return back();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                try {

                    $product_stock = ProductStock::where('product_id', $orderDetail->product_id)->where('variant', $orderDetail->variation)->first();
                    if ($product_stock != null) {
                        $product_stock->qty += $orderDetail->quantity;
                        $product_stock->save();
                    }

                } catch (\Exception $e) {

                }

                $orderDetail->delete();
            }
            $order->delete();
           Session::flash("success",translate('Order has been deleted successfully'));
        } else {
           Session::flash("error",translate('Something went wrong'));
        }
        return back();
    }


    public function bulk_order_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $order_id) {
                $this->destroy($order_id);
            }
        }

        return 1;
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        return view('frontend.user.seller.order_details_seller', compact('order'));
    }

    public function update_delivery_status(Request $request)
    {

        DB::beginTransaction();
        try{
            $order = Order::findOrFail($request->order_id);
            $order->delivery_status = $request->status;

            if($request->status == "delivered"){
                $order->delivery_time = Carbon::now();

                // Give Point
                // ------------------------------------------------------
                // if($order->referral_code != null){

                //     $user_points = get_setting("referred_user_points") ?? 0;
                //     $referer_points = get_setting("referer_points") ?? 0;

                //     $user = $order->user;
                //     $user->points += $user_points;
                //     $user->is_new_user = false;
                //     $user->save();

                //     $referer = User::where("referral_code", $order->referral_code)->first();
                //     $referer->points += $referer_points;
                //     $referer->save();

                // }else{
                //     $user = $order->user;
                //     $user->points = $user->points + $order->points_accrued;
                //     $user->save();
                // }

            }

            $order->save();

            if ($request->status == 'cancelled') {

                foreach ($order->orderDetails as $key => $orderDetail) {

                    $product = $orderDetail->product;
                    $product->current_stock += $orderDetail->quantity;
                    $product->save();

                }

            }


            // if($order->delivery_status == "processing" || $order->delivery_status == "on_the_way" || $order->delivery_status == "next_day"){
            //     SmsUtility::delivery_status_change($order->user->phone, $order);
            // }

            DB::commit();
            return 1;
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

   public function update_tracking_code(Request $request) {
        $order = Order::findOrFail($request->order_id);
        $order->tracking_code = $request->tracking_code;
        $order->save();

        return 1;
   }

    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);

        if($order->payment_status == "emergency" && $request->status == "paid" && $order->is_emergency_order){

            $user = $order->user;
            $user->lock_emergency = false;
            $user->save();

        }

        $order->payment_status_viewed = '0';
        $order->payment_status = $request->status;
        $order->save();



        // if($order->delivery_status == "processing")
        //     NotificationUtility::sendNotification($order, $request->status);
        // if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
        //     $request->device_token = $order->user->device_token;
        //     $request->title = "Order updated !";
        //     $status = str_replace("_", "", $order->payment_status);
        //     $request->text = " Your order {$order->code} has been {$status}";

        //     $request->type = "order";
        //     $request->id = $order->id;
        //     $request->user_id = $order->user->id;

        //     NotificationUtility::sendFirebaseNotification($request);
        // }


        // if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'payment_status_change')->first()->status == 1) {
        //     try {
        //         SmsUtility::payment_status_change($order->user->phone, $order);
        //     } catch (\Exception $e) {

        //     }
        // }
        return 1;
    }

    public function assign_delivery_boy(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        if($order->delivery_status == "pending"){
            $order->delivery_status = "processing";
        }
        $order->assign_delivery_boy = $request->delivery_boy;

        $area = City::findOrFail($order->city_id);

        $warehouse = $area->warehouses->first();
        if($warehouse){
            $order->latitude = $warehouse->latitude;
            $order->longitude = $warehouse->longitude;
        }

        $order->delivery_history_date = date("Y-m-d H:i:s");
        $order->save();

        $user = User::findOrFail($request->delivery_boy);
        $rider = $user->delivery_boy;

        return response()->json([
            "token" => $rider->fcm_token
        ]);
    }


    public function manageby(Request $request){

        $order = Order::find($request->order_id);
        $order->manage_by = $request->manage_by;
        $order->save();

        return response(["success" => true], 200);

    }


    public function giveDiscount(Request $request, $order_id){

        $order = Order::find($order_id);

        if($request->discount_type == "percent"){
            $dis = ($request->discount / 100) * ($order->grand_total - $order->shipping_cost);
            $order->coupon_discount = $dis;
        }else{
            $order->coupon_discount = $request->discount;
        }
        $order->update();
        Session::flash('success',translate('Discount Updated'));
        return redirect()->back();

    }


    public function newSale(){

        $delivery_boys = User::where('state', '!=',null)
            ->where('user_type', 'delivery_boy')
            ->get();


        $customers = User::where("is_b2b_user", true)->get();


        return view('backend.sales.all_orders.newsale', compact('delivery_boys', 'customers'));

    }


    public function storeNewSale(Request $request){


        $request->validate([
            "customer" => ["required"],
            "sale_time" => ["required"],
            "order_status" => ["required"],
        ], [
            "customer.required" => "Please Select a Customer",
            "sale_time.required" => "Please Enter Sale Time",
            "order_status.required" => "Order Status Field is Required",
        ]);


        $customer = User::findOrFail($request->customer);

        $address = $customer->addresses()->first();

        if(!$address){
            return response()->json([
                "message" => "Customer has no Address",
                "errors" => [
                    "message" => ["Address is missing"]
                ]
            ], 422);
        }

        $invoice = Order::where("invoice_number", $request->invoice_number)->first();
        if($request->invoice_number && $invoice){
            return response()->json([
                "message" => "Invoice Number already Exist",
                "errors" => [
                    "message" => ["Invoice Number already Exist"]
                ]
            ], 422);
        }

        if(count($request->products) <= 0){
            return response()->json([
                "message" => "No Products are added",
                "errors" => [
                    "message" => ["Please add at least one product"]
                ]
            ], 422);
        }

        $shippingAddress = [];
        if ($address != null) {
            $shippingAddress['name']        = $address->name;
            $shippingAddress['email']       = Auth::user()->email;
            $shippingAddress['address']     = $address->address;
            $shippingAddress['country']     = $address->country->name ?? "Bangladesh";
            $shippingAddress['state']       = $address->state->name;
            $shippingAddress['city']        = $address->city->name ?? null;
            $shippingAddress['postal_code'] = $address->postal_code;
            $shippingAddress['phone']       = $address->phone ?: Auth::user()->phone;
            $shippingAddress['floor_no']       = $address->floor_no;
            $shippingAddress['apartment']       = $address->apartment;
            if ($address->latitude || $address->longitude) {
                $shippingAddress['lat_lang'] = $address->latitude . ',' . $address->longitude;
            }
        }


        DB::beginTransaction();
        try{

            $order = new Order;
            $order->ordered_from = "web";
            $order->tracking_code = rand(1000, 9999);
            $order->city_id = $address->city_id;
            // $order->preferred_delivery_date = $this->getPreferredDeliveryDate(0);
            // $order->delivery_slot = 0;
            $order->user_id = $customer->id;
            $order->shipping_address = json_encode($shippingAddress);
            $order->payment_type = $request->payment_option;
            $order->date = strtotime('now');
            $order->save();

            $subtotal = 0;
            $tax = 0;

            $product_data = $request->products;

            foreach ($product_data as $pdata){

                $product = Product::find($pdata['id']);

                $subtotal += $product->unit_price * $pdata['quantity'];
                // $tax += $cartItem['tax'] * $pdata['quantity'];
                // $coupon_discount += $cartItem['discount'];
                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->price = $product->unit_price * $pdata['quantity'];
                // $order_detail->tax = $cartItem['tax'] * $pdata['quantity'];

                $order_detail->quantity = $pdata['quantity'];
                $order_detail->save();

                $product->num_of_sale += $pdata['quantity'];
                $product->save();

            }

            $shipping = $request->shipping ?? 0;
            $grand_total = $subtotal + $tax + $shipping;


            $order->reward_discount = $request->discount ?? 0;
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

            $order->invoice_number = $request->invoie_number;

            $order->is_b2b_order = true;
            $order->created_by = Auth::user()->id;
            $order->due_amount = $grand_total - $request->discount;
            $order->save();
            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "New Sale Created"
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }



        // All Orders
        public function b2bOrders(Request $request)
        {

            $sort_search = null;
            $delivery_status = null;
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $orders = Order::orderBy('id', 'desc')->where("is_b2b_order", true)->where("created_by", Auth::user()->id);

            if ($request->has('customer')) {
                $customer = $request->customer;
                // $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
                $orders = $orders->whereHas('user', function($q) use ($customer){
                    $q->where("id", $customer);
                });
            }

            if ($request->has('search')) {
                $sort_search = $request->search;
                // $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
                $orders = $orders->where(function($p) use($sort_search){

                    $p->where('code', 'like', '%' . $sort_search . '%')
                        ->orWhereHas('user', function($q) use ($sort_search){
                            $q->where("phone", "like", "%$sort_search%");
                        });

                });
            }

            if ($request->delivery_status != null) {
                $orders = $orders->where('delivery_status', $request->delivery_status);
                $delivery_status = $request->delivery_status;
            }

            if ($start_date != null) {
                $orders = $orders->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)));
            }
            if ($end_date != null) {
                $orders = $orders->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)));
            }


            if($request->export){
                if($request->export == "export1"){
                    return Excel::download(new OrdersExport($orders), 'orders.xlsx');
                }else{
                    return Excel::download(new OrdersExport2($orders), 'orders.xlsx');
                }
            }
            // $orders = $orders->paginate(15);

            if (Auth::user()->user_type == 'admin' || Auth::user()->staff->role->id != 4){
                $orders = $orders->paginate(15);
            }else{
                $orders = $orders->where('manage_by', 'sonar')->paginate(15);
            }


            $customers = User::where("is_b2b_user", true)->get();
            return view('backend.sales.b2b_orders.index', compact('orders', 'sort_search', 'delivery_status', 'start_date', 'end_date', 'customers'));
    }


    public function newsalePayment(Request $request, $order_id){

        DB::beginTransaction();
        try{
            $order = Order::findOrFail($order_id);

            $sp = new SalePayment();
            $sp->order_id = $order->id;
            $sp->notes = $request->notes;
            $sp->amount = $request->amount;
            $sp->payment_date = $request->payment_date;

            $order->paid_amount = $order->paid_amount + $request->amount;
            $order->due_amount = $order->due_amount - $request->amount;
            $order->save();

            $sp->save();

            DB::commit();
            return redirect()->back()->with("success", "Payment Added");
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

    }

}
