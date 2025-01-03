<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Auth;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\DeliveryBoy;
use App\Models\DeliveryHistory;
use App\Models\DeliveryBoyPayment;
use App\Models\DeliveryBoyCollection;
use App\Models\Order;
use App\Models\User;
use DB;
use Str;
use Session;

class DeliveryBoyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $sort_search = null;
        $delivery_boys = DeliveryBoy::orderBy('created_at', 'desc');

        if ($request->has('search')){
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'delivery_boy')->where(function($user) use ($sort_search){
                $user->where('name', 'like', '%'.$sort_search.'%')
                     ->orWhere('email', 'like', '%'.$sort_search.'%');
            })->pluck('id')->toArray();
            $delivery_boys = $delivery_boys->where(function($delivery_boy) use ($user_ids){
                $delivery_boy->whereIn('user_id', $user_ids);
            });
        }

        $delivery_boys = $delivery_boys->paginate(15);
        return view('delivery_boys.index', compact('delivery_boys', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $countries = Country::where('status', 1)->get();
        $areas = City::all();
        return view('delivery_boys.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$validated = $request->validate([
            'name'          => 'required',
            // 'email'         => 'required|unique:users|max:255',
            'phone'         => 'required',
            // 'country_id'    => 'required',
            'area'   	=> 'required',
            // 'city_id'       => 'required',
        ]);

		// $country = Country::where('id', $request->country_id)->first();
        $country = Country::first();
		$state = State::where('id', $request->area)->first();
		$city = City::where('id', $request->area)->first();

        DB::beginTransaction();
        try{
            $user                       = new User;
            $user->user_type            = 'delivery_boy';
            $user->name                 = $request->name;
            // $user->email                = $request->email;
            $user->email                = Str::random(10)."@gmail.com";
            $user->phone                = $request->phone;
            $user->country              = $country->name;
            $user->state              	= $state->name;
            $user->city                 = $city->name ?? "";
            $user->avatar               = $request->avatar;
            $user->address              = $request->address;
            $user->email_verified_at    = date("Y-m-d H:i:s");
            $user->password             =  \Hash::make($request->password ?: "password");
            $user->save();

            $delivery_boy = new DeliveryBoy;

            $delivery_boy->dob      = date('Y-m-d', strtotime($request->date_of_birth));

            $delivery_boy->city_id = $request->area;
            $delivery_boy->user_id = $user->id;
            $delivery_boy->nid      = $request->nid;
            $delivery_boy->nid_image      = $request->nid_image;
            $delivery_boy->nid_image_backpart      = $request->nid_image_backpart;

            $delivery_boy->has_cycle      = $request->cycle ? true : false;
            $delivery_boy->has_motorbike  = $request->motor_bike ? true : false;
            $delivery_boy->by_walk        = $request->by_walk ? true : false;

            $delivery_boy->license      = $request->license;
            $delivery_boy->license_image      = $request->license_image;
            $delivery_boy->license_image_backpart      = $request->license_image_backpart;
            $delivery_boy->emergency_contact_number = $request->emergency_contact_number;
            $delivery_boy->registration_number = $request->registration_number;

            $delivery_boy->save();

            DB::commit();
            Session::flash("success",translate('Delivery Boy has been created successfully'));
            return redirect()->route('delivery-boys.index');

        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        DB::beginTransaction();
        try{
            $user = User::findOrFail($id);
            $user->delivery_boy->delete();
            $user->delete();

            Session::flash("success",translate('Delivery Boy Deleted successfully'));

            DB::commit();
            return back();
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $countries = Country::where('status', 1)->get();
        // $states = State::where('status', 1)->get();
        // $cities = City::where('status', 1)->get();
        $user = User::findOrFail($id);
        $delivery_boy = $user->delivery_boy;
        $areas = City::all();

        return view('delivery_boys.edit', compact('delivery_boy', 'areas', 'user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

		$request->validate([
			'name'       => 'required',
            // 'email' 	 => 'required|unique:users,email,'.$delivery_boy->id,
            'phone'      => 'required',
            // 'country_id' => 'required',
            'area'   => 'required',
            // 'city_id'    => 'required',
        ]);

        // $country = Country::where('id', $request->country_id)->first();
        $country = Country::first();
		$state = State::where('id', $request->area)->first();
		$city = City::where('id', $request->area)->first();

        DB::beginTransaction();
        try{
            $user->name             = $request->name;
            $user->email            = $request->email;
            $user->phone            = $request->phone;
            $user->country          = $country->name;
            $user->state            = $state->name;
            $user->city             = $city->name ?? "";
            $user->avatar  = $request->avatar;
            $user->address          = $request->address;

            if(strlen($request->password) > 0){
                $user->password = Hash::make($request->password);
            }

            $user->save();

            $dboy = $user->delivery_boy;

            $dboy->city_id = $request->area;

            $dboy->nid      = $request->nid;
            $dboy->dob      = date('Y-m-d', strtotime($request->date_of_birth));
            $dboy->nid_image      = $request->nid_image;
            $dboy->nid_image_backpart      = $request->nid_image_backpart;
            $dboy->has_cycle      = $request->cycle ? true : false;
            $dboy->has_motorbike      = $request->motor_bike ? true : false;
            $dboy->by_walk      = $request->by_walk ? true : false;
            $dboy->license      = $request->license;
            $dboy->license_image      = $request->license_image;
            $dboy->license_image_backpart      = $request->license_image_backpart;
            $dboy->emergency_contact_number = $request->emergency_contact_number;
            $dboy->registration_number = $request->registration_number;
            $dboy->save();

            DB::commit();
            Session::flash("success",translate('Delivery Boy has been updated successfully'));
            return back();
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    public function ban($id) {

        $delivery_boy = User::findOrFail($id);

        if($delivery_boy->banned == 1) {
            $delivery_boy->banned = 0;
            Session::flash("success",translate('Delivery Boy UnBanned Successfully'));
        } else {
            $delivery_boy->banned = 1;
            Session::flash("success",translate('Delivery Boy Banned Successfully'));
        }

        $delivery_boy->save();

        return back();
    }

    /**
     * Collection form from Delivery boy.
     *
     * @return \Illuminate\Http\Response
     */
    public function order_collection_form(Request $request) {
        $delivery_boy_info = DeliveryBoy::with('user')
                ->where('user_id', $request->id)
                ->first();

        return view('delivery_boys.order_collection_form', compact('delivery_boy_info'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function collection_from_delivery_boy(Request $request) {
        $delivery_boy = DeliveryBoy::where('user_id', $request->delivery_boy_id)->first();

		if($request->payout_amount > $delivery_boy->total_collection){
            Session::flash("error",translate('Collection Amount Can Not Be Larger Than Collected Amount'));
            return redirect()->route('delivery-boys.index');
        }

        $delivery_boy->total_collection -= $request->payout_amount;

        if($delivery_boy->save()){
            $delivery_boy_collection          = new DeliveryBoyCollection;
            $delivery_boy_collection->user_id = $request->delivery_boy_id;
            $delivery_boy_collection->collection_amount = $request->payout_amount;

            $delivery_boy_collection->save();

            Session::flash("success",translate('Collection From Delivery Boy Successfully'));
        } else {
            Session::flash("error",translate('Something went wrong'));
        }

        return redirect()->route('delivery-boys.index');
    }

    /**
     * Paid form for Delivery boy.
     *
     * @return \Illuminate\Http\Response
     */
    public function delivery_earning_form(Request $request) {
        $delivery_boy_info = DeliveryBoy::with('user')
                ->where('user_id', $request->id)
                ->first();

        return view('delivery_boys.delivery_earning_form', compact('delivery_boy_info'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paid_to_delivery_boy(Request $request) {
        $delivery_boy = DeliveryBoy::where('user_id', $request->delivery_boy_id)->first();

        if($request->paid_amount > $delivery_boy->total_earning){
            Session::flash("error",translate('Paid Amount Can Not Be Larger Than Payable Amount'));
            return redirect()->route('delivery-boys.index');
        }

        $delivery_boy->total_earning -= $request->paid_amount;

        if($delivery_boy->save()){
            $delivery_boy_payment          = new DeliveryBoyPayment;
            $delivery_boy_payment->user_id = $request->delivery_boy_id;
            $delivery_boy_payment->payment = $request->paid_amount;

            $delivery_boy_payment->save();

            Session::flash("success",translate('Pay To Delivery Boy Successfully'));
        } else {
            Session::flash("error",translate('Something went wrong'));
        }

        return redirect()->route('delivery-boys.index');
    }

    public function delivery_boys_payment_histories() {
        $delivery_boy_payment_query = DeliveryBoyPayment::query();
        if(Auth::user()->user_type == 'delivery_boy') {
            $order_query = $delivery_boy_payment_query->where('user_id', Auth::user()->id);
        }
        $delivery_boy_payment_query = $delivery_boy_payment_query->paginate(10);

        $delivery_boy_payments = $delivery_boy_payment_query;
        if(Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.frontend.cancel_request_list', compact('delivery_boy_payments'));
        }
        return view('delivery_boys.delivery_boys_payment_list', compact('delivery_boy_payments'));
    }

    public function delivery_boys_collection_histories() {

        $delivery_boy_collection_query = DeliveryBoyCollection::query();
        if(Auth::user()->user_type == 'delivery_boy') {
            $order_query = $delivery_boy_collection_query->where('user_id', Auth::user()->id);
        }
        $delivery_boy_collection_query = $delivery_boy_collection_query->paginate(10);

        $delivery_boy_collections = $delivery_boy_collection_query;
        if(Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.frontend.cancel_request_list', compact('delivery_boy_collections'));
        }
        return view('delivery_boys.delivery_boys_collection_list', compact('delivery_boy_collections'));
    }

    public function cancel_request_list() {
        $order_query = Order::query();
        if(Auth::user()->user_type == 'delivery_boy') {
            $order_query = $order_query->where('assign_delivery_boy', Auth::user()->id);
        }
        $order_query = $order_query->where('delivery_status', '!=', 'cancelled')->where('cancel_request', 1);
        $order_query = $order_query->paginate(10);

        $cancel_requests = $order_query;
        if(Auth::user()->user_type == 'delivery_boy') {
            return view('delivery_boys.frontend.cancel_request_list', compact('cancel_requests'));
        }
        return view('delivery_boys.cancel_request_list', compact('cancel_requests'));
    }

    /**
     * Configuration of delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delivery_boy_configure()
    {
        return view('delivery_boys.delivery_boy_configure');
    }

    /**
     * Show the list of assigned delivery by the admin.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assigned_delivery()
    {
        $order_query = Order::query();
        $order_query->where('assign_delivery_boy', Auth::user()->id);
        $order_query->where(function ($order_query) {
            $order_query->where('delivery_status', 'pending')
                    ->where('cancel_request', '0');
        })->orWhere(function ($order_query) {
            $order_query->where('delivery_status', 'confirmed')
                    ->where('cancel_request', '0');
        });

        $assigned_deliveries = $order_query->paginate(10);

        return view('delivery_boys.frontend.assigned_delivery', compact('assigned_deliveries'));
    }

    /**
     * Show the list of pickup delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pickup_delivery()
    {
        $pickup_deliveries = Order::where('assign_delivery_boy', Auth::user()->id)
                ->where('delivery_status', 'picked_up')
                ->where('cancel_request', '0')
                ->paginate(10);

        return view('delivery_boys.frontend.pickup_delivery', compact('pickup_deliveries'));
    }

    /**
     * Show the list of pickup delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function on_the_way_deliveries()
    {
        $on_the_way_deliveries = Order::where('assign_delivery_boy', Auth::user()->id)
                ->where('delivery_status', 'on_the_way')
                ->where('cancel_request', '0')
                ->paginate(10);

        return view('delivery_boys.frontend.on_the_way_delivery', compact('on_the_way_deliveries'));
    }

    /**
     * Show the list of completed delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function completed_delivery()
    {
        $completed_deliveries = DeliveryHistory::where('delivery_boy_id', Auth::user()->id)
                ->where('delivery_status', 'delivered')
                ->paginate(10);

        return view('delivery_boys.frontend.completed_delivery', compact('completed_deliveries'));
    }

    /**
     * Show the list of pending delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pending_delivery()
    {
        $pending_deliveries = Order::where('assign_delivery_boy', Auth::user()->id)
                ->where('delivery_status', '!=', 'delivered')
                ->where('delivery_status', '!=', 'cancelled')
                ->where('cancel_request', '0')
                ->paginate(10);

        return view('delivery_boys.frontend.pending_delivery', compact('pending_deliveries'));
    }

    /**
     * Show the list of cancelled delivery by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancelled_delivery()
    {
        $completed_deliveries = Order::where('assign_delivery_boy', Auth::user()->id)
                ->where('delivery_status', 'cancelled')
                ->paginate(10);

        return view('delivery_boys.frontend.cancelled_delivery', compact('completed_deliveries'));
    }

    /**
     * Show the list of total collection by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function total_collection()
    {
        $today_collections = DeliveryHistory::where('delivery_boy_id', Auth::user()->id)
                ->where('delivery_status', 'delivered')
                ->where('payment_type', 'cash_on_delivery')
                ->paginate(10);

        return view('delivery_boys.frontend.total_collection_list', compact('today_collections'));
    }

    /**
     * Show the list of total earning by the delivery boy.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function total_earning()
    {
        $total_earnings = DeliveryHistory::where('delivery_boy_id', Auth::user()->id)
                ->where('delivery_status', 'delivered')
                ->paginate(10);

        return view('delivery_boys.frontend.total_earning_list', compact('total_earnings'));
    }

    public function cancel_request($order_id) {
        $order = Order::findOrFail($order_id);
        $order->cancel_request = '1';
        $order->cancel_request_at = date("Y-m-d H:i:s");
        $order->save();

        return back();
    }

    /**
     * For only delivery boy while changing delivery status.
     * Call from order controller
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store_delivery_history($order) {
        $delivery_history = new DeliveryHistory;

        $delivery_history->order_id         = $order->id;
        $delivery_history->delivery_boy_id  = Auth::user()->id;
        $delivery_history->delivery_status  = $order->delivery_status;
        $delivery_history->payment_type     = $order->payment_type;
        if($order->delivery_status == 'delivered') {
            $delivery_boy = DeliveryBoy::where('user_id', Auth::user()->id)->first();

            if(get_setting('delivery_boy_payment_type') == 'commission') {
                $delivery_history->earning      = get_setting('delivery_boy_commission');
                $delivery_boy->total_earning   += get_setting('delivery_boy_commission');
            }
            if($order->payment_type == 'cash_on_delivery') {
                $delivery_history->collection    = $order->grand_total;
                $delivery_boy->total_collection += $order->grand_total;

                $order->payment_status           = 'paid';
                if($order->commission_calculated == 0) {
                    calculateCommissionAffilationClubPoint($order);
                    $order->commission_calculated = 1;
                }

            }

            $delivery_boy->save();

        }
        $order->delivery_history_date = date("Y-m-d H:i:s");

        $order->save();
        $delivery_history->save();

    }



    public function riderColections($user_id){

        $start_date = request("start_date");
        $end_date = request("end_date");

        $collections = Order::where("assign_delivery_boy", $user_id)->where("delivery_status", "delivered")
                    ->where("payment_status", "paid");

        if ($start_date != null) {
            $collections = $collections->whereDate('delivery_time', '>=', date('Y-m-d', strtotime($start_date)));
        }
        if ($end_date != null) {
            $collections = $collections->whereDate('delivery_time', '<=', date('Y-m-d', strtotime($end_date)));
        }

        $collections = $collections->paginate(20);

        return view("delivery_boys.collections", compact("collections", "start_date", "end_date"));

    }
}
