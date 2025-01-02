<?php

namespace App\Http\Controllers;

use App\Models\CustomerComplain;
use App\Models\CustomerComplainExport;
use App\Models\Order;
use Illuminate\Http\Request;
use Excel;
use Session;

class CustomerComplainController extends Controller
{
    public function index()
    {
        $complains = CustomerComplain::orderBy('created_at','desc')->get();
        return view('backend.customer_complain.index', compact('complains'));
    }

    public function create()
    {
        $orders = Order::all();
        return view('backend.customer_complain.create', compact('orders'));
    }

    public function export(){
        return Excel::download(new CustomerComplainExport(), 'compalins.xlsx');
    }

    public function store(Request $request)
    {
        $feedback = CustomerComplain::create([
            'order_id' => $request->order_id,
            'description' => $request->description,
            'status' => $request->status
        ]);

        Session::flash("success",translate('Customer Complain Create Successfully'));
        return redirect()->route('complain.index');
    }

    public function show(CustomerComplain $complain)
    {
        $complain->delete();
        Session::flash("success",(translate('Customer Complain Deleted Successfully')));
        return redirect()->route('complain.index');
    }

    public function edit(CustomerComplain $complain)
    {
        $orders = Order::all();
        return view('backend.customer_complain.edit',compact('complain', 'orders'));
    }

    public function update(Request $request, CustomerComplain $complain)
    {

        $complain->update([
            'order_id' => $request->order_id,
            'description' => $request->description,
            'status' => $request->status
        ]);

        Session::flash("success",translate('Customer Complain Create Successfully'));
        return redirect()->route('complain.index');
    }

    public function destroy(CustomerComplain $complain)
    {

        $complain->delete();
        Session::flash("success",(translate('Customer Complain Deleted Successfully')));
        return redirect()->route('complain.index');
    }
}
