<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerCancelList;
use App\Http\Requests\UpdateCustomerCancelList;
use App\Models\CancelListsExport;
use App\Models\CustomerCancelList;
use Excel;
use Illuminate\Http\Request;

class CustomerCancelListController extends Controller
{

    public function index()
    {
        $cancels = CustomerCancelList::orderBy('created_at','desc')->get();
        return view('backend.customer_cancel_list.index', compact('cancels'));
    }


    public function create()
    {
        return view('backend.customer_cancel_list.create');
    }


    public function export(){
        return Excel::download(new CancelListsExport(), 'cancel_list.xlsx');
    }

    public function store(StoreCustomerCancelList $request)
    {
        $cancel = CustomerCancelList::create([
            "date" => $request->date,
            "notes" => $request->notes,
            "total_order" => $request->total_order,
            "cancel" => $request->cancel,
            "delivery" => $request->delivery,
            "nextday" => $request->nextday,
            "processing" => $request->processing
        ]);

        return redirect()->route('cancel_list.index')->with("success", "Customer Cancel List Successfully Created");
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $cancel = CustomerCancelList::find($id);
        return view('backend.customer_cancel_list.edit', compact('cancel'));
    }

    public function update(UpdateCustomerCancelList $request,  $id)
    {
        $cancel = CustomerCancelList::find($id);
        $cancel->update([
            "date" => $request->date,
            "notes" => $request->notes,
            "total_order" => $request->total_order,
            "cancel" => $request->cancel,
            "delivery" => $request->delivery,
            "nextday" => $request->nextday,
            "processing" => $request->processing
        ]);
        return redirect()->route('cancel_list.index')->with("success", "Customer Cancel List Successfully Updated");
    }


    public function destroy($id)
    {
        $cancel = CustomerCancelList::find($id);
        $cancel->delete();
        return redirect()->route("cancel_list.index")->with("success", "Customer Cancel List Successfully Delated");
    }
}
