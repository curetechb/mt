<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerListExport;
use App\Models\CustomersExports;
use App\Models\User;
use App\Models\Order;
use Maatwebsite\Excel\Facades\Excel;
use Twilio\Rest\Api\V2010\Account\NewKeyList;
use Session;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $users = User::where('user_type', 'customer')->orderBy('created_at', 'desc');

        if ($request->has('search')){
            $sort_search = $request->search;
            $users = $users->where(function ($q) use ($sort_search){
                $q->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%')->orWhere('phone', 'like', '%'.$sort_search.'%');
            });
        }




        if($request->filter_by == 'have_points'){

                $users = $users->where('points', '>', 0);
         }

        if ($request->download == 'download') {
            return Excel::download(New CustomersExports($users), 'customers.xlsx');
        }

        $users = $users->paginate(15);
        // $users = User::paginate(10);
        return view('backend.customer.customers.index', compact('users', 'sort_search'));
    }


    public function b2bRequest(){

        $sort_search = null;
        $users = User::where('user_type', 'customer')->where("is_b2b_user", 0)->orderBy('created_at', 'desc');

        $users = $users->paginate(15);
        // $users = User::paginate(10);
        return view('backend.customer.customers.b2b-requests', compact('users', 'sort_search'));

    }

    public function b2bCustomers(Request $request){

        $sort_search = null;
        $users = User::where('user_type', 'customer')->where("is_b2b_user", 1)->orderBy('created_at', 'desc');

        if ($request->has('search')){
            $sort_search = $request->search;
            $users = $users->where(function ($q) use ($sort_search){
                $q->where('name', 'like', '%'.$sort_search.'%')->orWhere('email', 'like', '%'.$sort_search.'%')->orWhere('phone', 'like', '%'.$sort_search.'%');
            });
        }


        if($request->filter_by == 'have_points'){

                $users = $users->where('points', '>', 0);
         }

        if ($request->download == 'download') {
            return Excel::download(New CustomersExports($users), 'b2b-customers.xlsx');
        }

        $users = $users->paginate(15);
        // $users = User::paginate(10);
        return view('backend.customer.customers.b2b-customers', compact('users', 'sort_search'));

    }

    /**
     *
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.customer.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = User::where("phone", "+88$request->phone")->first();
        if($user){
            return redirect()->back()->with("error", "User Already Exists");
        }

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => "+88". $request->phone,
            "password" => $request->password,
            "user_type" => "customer",
            "is_verified" => true,
            "is_b2b_user" => $request->is_b2b_user ? true : false
        ]);


        $address = new Address();
        $address->user_id   = $user->id;

        if ($user->addresses()->where("set_default", 1)->first() == null) {
            $address->set_default = 0;
            $address->save();
        }

        $address->name = $request->name;
        $address->address       = $request->address;
        $address->country_id    = $request->country_id ?? 1;
        $address->state_id      = $request->area;
        $address->city_id       = $request->city_id ?? null;
        $address->longitude     = $request->longitude;
        $address->latitude      = $request->latitude;
        $address->postal_code   = $request->postal_code;
        $address->phone         = $request->alternative_phone;
        $address->floor_no = $request->floor_no;
        $address->apartment = $request->apartment;
        $address->save();

        return redirect()->route("customers.index")->with("success", "Customer Added Successfully");

        // $request->validate([
        //     'name'          => 'required',
        //     'email'         => 'required|unique:users|email',
        //     'phone'         => 'required|unique:users',
        //     'password'      => 'required|unique:users'
        // ]);

        // $response['status'] = 'Error';

        // $user = User::create($request->all());

        // $customer = new Customer;

        // $customer->user_id = $user->id;
        // $customer->save();

        // if (isset($user->id)) {
        //     $html = '';
        //     $html .= '<option value="">
        //                 '. translate("Walk In Customer") .'
        //             </option>';
        //     foreach(Customer::all() as $key => $customer){
        //         if ($customer->user) {
        //             $html .= '<option value="'.$customer->user->id.'" data-contact="'.$customer->user->email.'">
        //                         '.$customer->user->name.'
        //                     </option>';
        //         }
        //     }

        //     $response['status'] = 'Success';
        //     $response['html'] = $html;
        // }

        // echo json_encode($response);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user =User::find($id);
        $address = $user->addresses()->where("set_default", 1)->first();
        if(!$address){
            $address = $user->addresses()->first();
        }
        return view('backend.customer.customers.edit', compact('user', 'address'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $id)
    {

        $user = User::find($id);

        $user->update([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "password" => $request->password,
            "is_b2b_user" => $request->is_b2b_user ? true : false
        ]);

        if($user->addresses()->count() <= 0){
            $address = new Address();
            $address->set_default = 1;
        }else{

            $address = $user->addresses()->where("set_default", 1)->first();

            if(!$address){
                $address = $user->addresses()->first();
                $address->set_default = 1;
            }
        }


        $address->user_id = $id;
        $address->name = $request->name;
        $address->address       = $request->address;
        $address->country_id    = $request->country_id ?? 1;
        $address->state_id      = $request->area;
        $address->city_id       = $request->city_id ?? null;
        $address->longitude     = $request->longitude;
        $address->latitude      = $request->latitude;
        $address->postal_code   = $request->postal_code;
        $address->phone         = $request->alternative_phone;
        $address->floor_no = $request->floor_no;
        $address->apartment = $request->apartment;
        $address->save();

        if($request->redirect_to && $request->redirect_to == "b2b"){
            return redirect()->route("customers.b2b")->with("success", "Customer Updated Successfully");
        }

        return redirect()->route("customers.index")->with("success", "Customer Updated Successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->route('customers.index')->with('success', 'Customer Deleted Successfully');

        // User::destroy($id);
        // flash(translate('Customer has been deleted successfully'))->success();
        // return redirect()->route('customers.index');
    }

    public function customerExport(){
        return Excel::download(new CustomerListExport, 'customer.xlsx');
    }

    public function bulk_customer_delete(Request $request) {
        if($request->id) {
            foreach ($request->id as $customer_id) {
                $this->destroy($customer_id);
            }
        }

        return 1;
    }

    public function login($id)
    {
        $user = User::findOrFail(decrypt($id));

        auth()->login($user, true);

        return redirect()->route('dashboard');
    }

    public function ban($id) {
        $user = User::findOrFail(decrypt($id));

        if($user->banned == 1) {
            $user->banned = 0;
            Session::flash("success",translate('Customer UnBanned Successfully'));
        } else {
            $user->banned = 1;
            Session::flash("success",translate('Customer Banned Successfully'));
        }

        $user->save();

        return back();
    }

    public function emergencyBalance(){
        return view('backend.customer.emergency_balance.index');
    }
}
