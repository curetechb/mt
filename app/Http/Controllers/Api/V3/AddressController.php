<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\Controller;
use App\Http\Requests\V3\StoreAddressRequest;
use App\Http\Resources\V3\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use DB;
use Auth;

class AddressController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth:sanctum");
    }

    public function index()
    {
        $default_address = Address::where('user_id', auth()->user()->id)->where("set_default", true)->first();
        if(!$default_address){
            $d = Address::where('user_id', auth()->user()->id)->first();
            $d->set_default = true;
            $d->save();
        }

        $addresses = Address::where('user_id', auth()->user()->id)->get();
        return AddressResource::collection($addresses);
    }

    public function store(StoreAddressRequest $request){

        DB::beginTransaction();
        try{

            Address::where("user_id", Auth::user()->id)->update([
                "set_default" => 0
            ]);

            $address = new Address;
            $address->user_id = auth()->user()->id;
            $address->address = $request->address;
            $address->name = $request->name;
            $address->country_id    = $request->country_id ?? 1;
            $address->state_id      = $request->area;
            $address->city_id       = $request->area;
            $address->postal_code = $request->postal_code;
            $address->phone = $request->alternative_phone;
            $address->floor_no = $request->floor_no;
            $address->apartment = $request->apartment;
            $address->set_default = 1;
            $address->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Created Successfully"
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            throw $e;
        }
    }

    public function update(Request $request, $id){

        $address = Address::where("id", $id)->where("user_id", Auth::user()->id)->first();

        $address->address = $request->address;
        $address->name = $request->name;
        $address->country_id    = $request->country_id ?? 1;
        $address->state_id      = $request->area;
        $address->city_id       = $request->area;
        $address->postal_code = $request->postal_code;
        $address->phone = $request->alternative_phone;
        $address->floor_no = $request->floor_no;
        $address->apartment = $request->apartment;
        $address->save();

        return response()->json([
            'success' => true,
            'message' => translate('Updated successfully')
        ]);

    }

    public function destroy($id){

        $address = Address::where("id", $id)->where("user_id", Auth::user()->id)->first();

        // if($address->set_default) return response(["message" => "Can not Delete Default Address"], 422);

        if($address->set_default == true){
            $a = Address::where("user_id", Auth::user()->id)->where("set_default", 0)->first();
            if($a) {
                $a->update([
                    "set_default" => 1
                ]);
            }
        }

        $address->delete();



        return response()->json([
            'success' => true,
            'message' => translate('Deleted successfully')
        ]);

    }

    public function defaultAddress($id){

        DB::beginTransaction();
        try{
            Address::where('user_id', auth()->user()->id)->update([
                "set_default" => 0
            ]);

            Address::find($id)->update([
                'set_default' => 1
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Updated Successfully"
            ]);

        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

    }
}
