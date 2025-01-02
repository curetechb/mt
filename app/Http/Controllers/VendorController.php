<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use DB;
use Redirect;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendors = Vendor::paginate(10);
        return view("backend.vendors.index", compact("vendors"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("backend.vendors.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vendor = Vendor::create([
            "contact_name" => $request->contact_name,
            "contact_number" => $request->contact_number,
            "company_name" => $request->company_name,
            "address" => $request->address
        ]);

        return redirect()->route("vendors.index")->with("success", "Vendor Created Successfully");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route("vendors.index")->with("success", "Vendor Deleted Successfully");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        return view("backend.vendors.edit", compact("vendor"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {

        $vendor->update([
            "contact_name" => $request->contact_name,
            "contact_number" => $request->contact_number,
            "company_name" => $request->company_name,
            "address" => $request->address
        ]);

        return redirect()->route("vendors.index")->with("success", "Vendor Updated Successfully");

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route("vendors.index")->with("success", "Vendor Deleted Successfully");
    }

    public function products($vendor_id){

        $vendor = Vendor::findOrFail($vendor_id);

        $products = $vendor->products()->paginate(20);

        return view("backend.vendors.products", compact("products", "vendor"));

    }

    public function addVendorProduct(Request $request){


        $vendor = Vendor::findOrFail($request->vendor_id);
        $product = Product::findOrFail($request->product);

        $vendor->products()->attach([$product->id]);

        return redirect()->route("vendor.products", $vendor->id)->with("success", "Product Added to Vendor");

    }

    public function deleteVendorProduct($product_id){

        $vendor_id = request()->vendor_id;

        DB::table('product_vendor')
            ->where("product_id", $product_id)
            ->where("vendor_id", $vendor_id)->delete();

        return redirect()->route("vendor.products", $vendor_id)->with("success", "Product Removed from Vendor");
    }


    public function stockInHistory(){

        $stockins = StockIn::paginate(10);
        return view("backend.vendors.stockin", compact("stockins"));

    }

    public function createStockIn(){

        return view("backend.vendors.add-stockin");
    }

    public function storeStockIn(Request $request){


        $product = Product::findOrFail($request->product);

        $stockin = StockIn::create([
            "vendor_id" => $request->vendor,
            "product_id" => $request->product,
            "quantity" => $request->quantity,
            "note" => $request->note
        ]);

        $stock = $product->stocks->where('variant', null)->first();
        $stock->qty = $stock->qty + $request->quantity;
        $stock->save();

        return redirect()->route("stockin.history")->with("success", "Added to Stock");
    }

    public function deleteStockIn($id){

        $stockin = StockIn::findOrFail($id);
        $product = Product::findOrFail($stockin->product_id);
        $stock = $product->stocks->where('variant', null)->first();
        $stock->qty = $stock->qty - $stockin->quantity;
        $stock->save();

        $stockin->delete();



        return redirect()->route("stockin.history")->with("success", "Removed From Stock");
    }

    public function stockOutHistory(){

        $stockouts = StockOut::paginate(10);
        return view('backend.vendors.stock_out.index', compact('stockouts'));
    }

    public function createStockOut(){

        return view("backend.vendors.stock_out.create");
    }

    public function storeStockOut(Request $request){

        $product = Product::findOrFail($request->product);

        $stockout = StockOut::create([

            "vendor_id" => $request->vendor,
            "product_id" => $request->product,
            "quantity" => $request->quantity,
            'note' => $request->note
        ]);

        $stock = $product->stocks->where('variant', null)->first();
        $stock->qty = $stock->qty - $request->quantity;
        $stock->save();

        return redirect()->route('stockout.history')->with('success', 'Added Stock Out');
    }

    public function deleteStockOut($id){

        $stockout = StockOut::findOrfail($id);
        $product = Product::findOrFail($stockout->product_id);
        $stock = $product->stocks->where('variant', null)->first();
        $stock->qty = $stock->qty - $stockout->quantity;
        $stock->save();

        $stockout->delete();

        return redirect()->route("stockout.history")->with("success", "Removed Successfully");

    }

}
