<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PriceHistory;

class PriceHistoryController extends Controller
{

    public function index(Request $request)
    {

        $sort_search = null;
        // $delivery_status = null;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $search = $request->search;

        $histories = PriceHistory::orderBy('id','desc');
        // $orders = Order::orderBy('id', 'desc');

        if ($request->has('search')) {

            $histories = $histories->whereHas("product", function($q) use($request) {
                $q->where("name",  "like", "%$request->search%");
            });

        }

        if ($start_date != null) {
            $histories = $histories->whereDate('created_at', '>=', date('Y-m-d', strtotime($start_date)));
        }
        if ($end_date != null) {
            $histories = $histories->whereDate('created_at', '<=', date('Y-m-d', strtotime($end_date)));
        }

        $histories = $histories->paginate(20);
        return view('backend.price_history.index', compact('histories','start_date','end_date', 'search'));
    }


    public function create()
    {
        return view('backend.price_history.create');
    }


    public function store(Request $request)
    {
        $histories = PriceHistory::create([
            "user_id" => $request->user_id,
            "product_id"  => $request->product_id,
            "price" => $request->price,
            "notes" => $request->notes
        ]);

        return redirect()->route("customers.index")->with("success", "Customer Added Successfully");

    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $history = PriceHistory::all();
        $history->update([
            "user_id" => $request->user_id,
            "product_id"  => $request->product_id,
            "price" => $request->price,
            "notes" => $request->notes
        ]);

        return redirect()->route("customers.index")->with("success", "Customer Added Successfully");
    }


    public function destroy($id)
    {
        //
    }
}
