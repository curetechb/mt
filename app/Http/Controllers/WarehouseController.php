<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use DB;

class WarehouseController extends Controller
{

    public function index()
    {
        $warehouses = Warehouse::paginate(10);
        return view('backend.warehouse.index', compact('warehouses'));
    }


    public function create()
    {
        $states = State::where("status", 1)->get();
        $cities = City::where("status", 1)->get();
        return view('backend.warehouse.create', compact("states", "cities"));
    }


    public function store(Request $request)
    {

        DB::beginTransaction();
        try{
            $warehouse = Warehouse::create([
                "name"=> $request->name,
                "address"=> $request->address,
                "state_id"=> 1,
                "latitude"=> $request->latitude,
                "longitude"=> $request->longitude
            ]);

            $warehouse->areas()->sync($request->areas);

            DB::commit();
            return redirect()->route('warehouse.index')->with("success", "Warehouse Created Successfully");
        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }


    public function show(Warehouse $warehouse)
    {
          $warehouse->delete();
        return redirect()->route('warehouse.index')->with("success", "Warehouse Deleted Successfully");
    }


    public function edit(Warehouse $warehouse)
    {
        $states = State::where("status", 1)->get();
        $cities = City::where("status", 1)->get();
        $warehouse_areas = $warehouse->areas()->pluck("city_id")->toArray();

        return view('backend.warehouse.edit', compact('warehouse', 'states', 'cities', 'warehouse_areas'));
    }


    public function update(Request $request, Warehouse $warehouse)
    {


        DB::beginTransaction();
        try{

            $warehouse->update([
                "name"=> $request->name,
                "address"=> $request->address,
                "state_id"=> 1,
                "latitude"=> $request->latitude,
                "longitude"=> $request->longitude
            ]);

            $warehouse->areas()->sync($request->areas);

            DB::commit();
            return redirect()->route('warehouse.index')->with("success", "Warehouse Updated Successfully");

        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

    }


    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('warehouse.index')->with("success", "Warehouse Deleted Successfully");
    }
}
