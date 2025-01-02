<?php

namespace App\Http\Controllers\Api\V3;

use App\Http\Controllers\AizUploadController;
use App\Http\Controllers\Controller;
use App\Http\Requests\V3\StoreRiderRequest;
use App\Http\Resources\V3\OrderIndexResource;
use App\Http\Resources\V3\RiderDeliveryHistoryResource;
use App\Http\Resources\V3\WarehouseResource;
use App\Http\Resources\V3\RiderListResource;
use App\Http\Resources\V3\RiderPaymentHistoryResource;
use App\Http\Resources\V3\RiderResource;
use App\Models\City;
use App\Models\Country;
use App\Models\DeliveryBoy;
use App\Models\DeliveryBoyPayment;
use App\Models\DeliveryHistory;
use App\Models\Order;
use App\Models\State;
use App\Models\Upload;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Auth;
use DB;
use Hash;
use Illuminate\Support\Str;
use Session;
use Illuminate\Support\Facades\Storage;
use Image;

class RiderController extends Controller
{

    public function __construct()
    {
        $this->middleware("auth:sanctum")->except(["riderRegistration"]);
    }

    public function riders(){
        $users = User::where("user_type", "delivery_boy")->get();
        return RiderListResource::collection($users);
    }

    public function rider(){

        $user = Auth::user();
        return  new RiderResource($user);

    }

    public function undeliveredOrders(){

        $rider = Auth::user()->delivery_boy;

        $maximum_delivery_limit = get_setting("maximum_delivery_limit");

        $riderarea = City::findOrFail($rider->city_id);

        $warehouse = $riderarea->warehouses->first();

        $warehouse_areas = \DB::table('city_warehouse')->where('warehouse_id', $warehouse->id)->pluck("city_id")->toArray();

        $orders = Order::where("delivery_status", "processing")
                    ->where("assign_delivery_boy", null)
                    ->whereIn("city_id", $warehouse_areas)
                    ->where("grand_total", "<=", $maximum_delivery_limit)
                    ->orderBy("created_at", "desc")->get();

        return OrderIndexResource::collection($orders);
    }

    public function acceptOrder(Request $request, $id){

        // $request->validate([
        //     "latitude" => ["required"],
        //     "longitude" => ["required", "numeric"]
        // ]);

        $order = Order::findOrFail($id);
        $rider = Auth::user()->delivery_boy;

        $maximum_delivery_limit = get_setting("maximum_delivery_limit");

        if( ($order->grand_total + $rider->invisible_earning) > $maximum_delivery_limit ){
            return response()->json([
                "errors" => [
                    "message" => ["Maximum Delivery Limit Reached. Please Pay first."]
                ]
            ], 422);
        }

        $order->assign_delivery_boy = Auth::user()->id;

        if($request->latitude && $request->longitude){
            $order->latitude = $request->latitude;
            $order->longitude = $request->longitude;
        }else{

            $area = City::findOrFail($order->city_id);

            $warehouse = $area->warehouses->first();
            if($warehouse){
                $order->latitude = $warehouse->latitude;
                $order->longitude = $warehouse->longitude;
            }
        }

        $order->save();

        return response()->json([
            "message" => "Accepted Successfully"
        ], 200);
    }

    public function acceptedOrders(){

        $orders = Order::where("assign_delivery_boy", Auth::user()->id)
                    ->whereIn("delivery_status", ["processing", "on_the_way"])
                    ->orderBy("created_at", "desc")->get();
        return OrderIndexResource::collection($orders);
    }

    public function verifyOrderOtp(Request $request){

        $request->validate([
            "tracking_code" => ["required"],
            "distance" => ["required", "numeric"]
        ]);

        DB::beginTransaction();
        try{

            $order = Order::findOrFail($request->order_id);

            if($order->tracking_code == $request->tracking_code && $order->delivery_status != "delivered"){

                $order->delivery_status = "delivered";
                // $order->distance = $request->distance;
                $order->save();

                // ------------------------------ calculate commission ------------------------- //
                $commission_for_first_km = get_setting("rider_base_commission");
                $next_km_commission = get_setting("delivery_boy_commission");

                $distance = $request->distance;
                $rider_earning = $commission_for_first_km;

                if($distance < 1) $distance = 1;
                $distance_left = $distance - 1;

                $rider_earning += $next_km_commission * $distance_left;

                $user = Auth::user();
                $rider = $user->delivery_boy;

                // if cash on delivery add to invisile earning if not then give to total earning
                if($order->payment_type == "cash_on_delivery" || $order->payment_type == "cash_payment"){
                    $rider->invisible_earning = $rider->invisible_earning + $rider_earning;
                    $rider->total_collection = $rider->total_collection + $order->grand_total;
                }else{
                    $rider->total_earning = $rider->total_earning + $rider_earning;
                }

                $rider->save();

                $dhistory = new DeliveryHistory();
                $dhistory->delivery_boy_id = $rider->id;
                $dhistory->order_id = $order->id;
                $dhistory->earning = $rider_earning;
                $dhistory->collection = $order->grand_total;
                $dhistory->distance = $request->distance;
                $dhistory->save();

                DB::commit();

                return response()->json([
                    "message" => "Verified Successfully"
                ], 200);
            }

            return response()->json([
                "message" => "Code doesn't match"
            ], 422);

        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }
    }

    public function warehouses(){

        $warehouses = Warehouse::all();
        return WarehouseResource::collection($warehouses);
    }

    public function orderWarehouse($order_id){

        $order = Order::findOrFail($order_id);

        $area = City::findOrFail($order->city_id);

        $warehouse = $area->warehouses->first();

        return new WarehouseResource($warehouse);

    }

    public function riderDeliveryHistories(){

        $user = Auth::user();
        $rider = $user->delivery_boy;
        $delivery_histories = $rider->delivery_histories;
        return RiderDeliveryHistoryResource::collection($delivery_histories);

    }

    public function riderPaymentHistories(){

        $rider = Auth::user()->delivery_boy;
        $payments = DeliveryBoyPayment::where('rider_id', $rider->id)->get();
        return RiderPaymentHistoryResource::collection($payments);
    }

    public function requestPayment(Request $request){


        $minimum_payment_amount = get_setting("minimum_payment_request_amount");

        $request->validate([
            "amount" => ["required", "numeric", "min: $minimum_payment_amount"]
        ]);

        $rider = Auth::user()->delivery_boy;

        if($rider->total_earning < $request->amount){

            return response()->json([
                "errors" => [
                    "amount" => ["You dont have enough Earning"]
                ]
            ], 422);

        }

        $payment = new DeliveryBoyPayment();
        $payment->rider_id = $rider->id;
        $payment->amount = $request->amount;
        $payment->save();

        DeliveryHistory::where("delivery_boy_id", $rider->id)->where("delivery_boy_payment_id", null)
            ->update([
                "delivery_boy_payment_id" => $payment->id
            ]);

        return response()->json([
            "success" => true,
            "message" => "Payment Request Sent"
        ]);
    }

    public function riderFcm(Request $request){

        $request->validate([
            "rider_id" => ["required"],
            "token" => ["required"]
        ]);

        $rider = DeliveryBoy::findOrFail($request->rider_id);
        $rider->fcm_token = $request->token;
        $rider->save();

        return response([
            "success" => true,
            "message" => "Success Rider Push Notification"
        ]);

    }


    public function uploadRiderFile($file){


        $type = array(
            "jpg"=>"image",
            "jpeg"=>"image",
            "png"=>"image",
            "svg"=>"image",
            "webp"=>"image",
            "gif"=>"image",
            "mp4"=>"video",
            "mpg"=>"video",
            "mpeg"=>"video",
            "webm"=>"video",
            "ogg"=>"video",
            "avi"=>"video",
            "mov"=>"video",
            "flv"=>"video",
            "swf"=>"video",
            "mkv"=>"video",
            "wmv"=>"video",
            "wma"=>"audio",
            "aac"=>"audio",
            "wav"=>"audio",
            "mp3"=>"audio",
            "zip"=>"archive",
            "rar"=>"archive",
            "7z"=>"archive",
            "doc"=>"document",
            "txt"=>"document",
            "docx"=>"document",
            "pdf"=>"document",
            "csv"=>"document",
            "xml"=>"document",
            "ods"=>"document",
            "xlr"=>"document",
            "xls"=>"document",
            "xlsx"=>"document"
        );

        $upload = new Upload();
        $extension = strtolower($file->getClientOriginalExtension());

        if(isset($type[$extension])){
            $upload->file_original_name = null;
            $arr = explode('.', $file->getClientOriginalName());
            for($i=0; $i < count($arr)-1; $i++){
                if($i == 0){
                    $upload->file_original_name .= $arr[$i];
                }
                else{
                    $upload->file_original_name .= ".".$arr[$i];
                }
            }

            $path = $file->store('uploads/all', 'local');
            $size = $file->getSize();

            // Return MIME type ala mimetype extension
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            // Get the MIME type of the file
            $file_mime = finfo_file($finfo, base_path('public/').$path);

            if($type[$extension] == 'image' && get_setting('disable_image_optimization') != 1){
                try {
                    $img = Image::make($file->getRealPath())->encode();
                    $height = $img->height();
                    $width = $img->width();
                    if($width > $height && $width > 1500){
                        $img->resize(1500, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }elseif ($height > 1500) {
                        $img->resize(null, 800, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                    $img->save(base_path('public/').$path);
                    clearstatcache();
                    $size = $img->filesize();

                } catch (\Exception $e) {
                    //dd($e);
                }
            }

            if (env('FILESYSTEM_DRIVER') == 's3') {
                Storage::disk('s3')->put(
                    $path,
                    file_get_contents(base_path('public/').$path),
                    [
                        'visibility' => 'public',
                        'ContentType' =>  $extension == 'svg' ? 'image/svg+xml' : $file_mime
                    ]
                );
                if($arr[0] != 'updates') {
                    unlink(base_path('public/').$path);
                }
            }

            $upload->extension = $extension;
            $upload->file_name = $path;
            $upload->user_id = Auth::user()->id ?? 9;
            $upload->type = $type[$upload->extension];
            $upload->file_size = $size;
            $upload->save();
        }

        return $upload->id ?? null;

    }


    public function riderRegistration(StoreRiderRequest $request){

        $user = User::where("phone", "+88$request->phone")->first();

        if($user){
            return response()->json([
                "errors" => [
                    "phone" => ["This Phone Number is already Registered"]
                ]
            ], 422);
        }

		// $country = Country::where('id', $request->country_id)->first();
        $country = Country::first();
		$state = State::where('id', $request->area)->first();
		$city = City::where('id', $request->area)->first();


        DB::beginTransaction();
        try{
            $user                       = new User();
            $user->banned = false;
            $user->user_type            = 'delivery_boy';
            $user->name                 = $request->name;
            // $user->email                = $request->email;
            // $user->email                = Str::random(10)."@gmail.com";
            $user->phone                = "+88$request->phone";
            $user->country              = $country->name;
            $user->state              	= $state->name;
            $user->city                 = $city->name ?? "";
            $user->address              = $request->address;
            $user->email_verified_at    = date("Y-m-d H:i:s");
            $user->password             =  \Hash::make($request->password ?: "password");
            $user->save();


            $delivery_boy = new DeliveryBoy();
            $delivery_boy->city_id = $request->area;
            $delivery_boy->user_id = $user->id;
            $delivery_boy->emergency_contact_number = $request->emergency_contact_number;
            $delivery_boy->save();
            DB::commit();

            $token = $user->createToken('API Token')->plainTextToken;
            return response()->json([
                'result' => true,
                'message' => translate('Successfully logged in'),
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => null,
                'user' => [
                    'id' => $user->id,
                    'type' => $user->user_type,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_original' => api_asset($user->avatar_original),
                    'phone' => $user->phone
                ]
            ]);

        }catch(\Exception $e){
            DB::rollback();
            throw $e;
        }

    }


    public function updateVehicleInfo(Request $request){

        if($request->vehicle_type == "motor_bike"){
            $request->validate([
                "vehicle_type" => ["required"],
                "registration_number" => ["required"],
                "license_number" => ["required"],
                "model" => ["required"],
                "year" => ["required"],
            ]);
        }


        $user = Auth::user();
        $delivery_boy = $user->delivery_boy;

        $delivery_boy->vehicle_type = $request->vehicle_type;
        $delivery_boy->has_cycle      = $request->vehicle_type == "cycle" ? true : false;
        $delivery_boy->has_motorbike  = $request->motor_bike == "motor_bike" ? true : false;
        $delivery_boy->by_walk        = $request->by_walk == "by_walk" ? true : false;

        $delivery_boy->registration_number = $request->registration_number;
        $delivery_boy->license      = $request->license_number;
        $delivery_boy->vehicle_model = $request->model;
        $delivery_boy->vehicle_year = $request->year;

        $delivery_boy->save();

        return response()->json([
            "success" => true,
            "message" => "Updated Successfully"
        ]);


    }


    public function uploadNidFront(Request $request){

        $request->validate([
            'nid_frontpart' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
        ]);


        $user = Auth::user();
        $delivery_boy = $user->delivery_boy;

        $nid_upload_id = $this->uploadRiderFile($request->nid_frontpart);
        $delivery_boy->nid_image      = $nid_upload_id;

        $delivery_boy->save();

        return response()->json([
            "success" => true,
            "message" => "Uploaded Successfully"
        ]);

    }

    public function uploadNidBack(Request $request){

        $request->validate([
            'nid_backpart' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
        ]);


        $user = Auth::user();
        $delivery_boy = $user->delivery_boy;

        $nid_image_backpart_id = $this->uploadRiderFile($request->nid_backpart);
        $delivery_boy->nid_image_backpart      = $nid_image_backpart_id;

        $delivery_boy->save();

        return response()->json([
            "success" => true,
            "message" => "Updated Successfully"
        ]);

    }

    public function uploadUserAvatar(Request $request){

        $request->validate([
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
        ]);


        $user = Auth::user();
        $delivery_boy = $user->delivery_boy;

        $avatar_upload_id = $this->uploadRiderFile($request->avatar);
        $user->avatar = $avatar_upload_id;
        $user->save();

        if($delivery_boy->vehicle_type != "motor_bike"){
            $delivery_boy->is_registration_complete = true;
            $delivery_boy->save();
        }

        return response()->json([
            "success" => true,
            "message" => "Updated Successfully"
        ]);

    }

    public function uploadDrivingLicense(Request $request){

        $request->validate([
            'driving_license' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
        ]);


        $user = Auth::user();
        $delivery_boy = $user->delivery_boy;

        $license_upload_id = $this->uploadRiderFile($request->driving_license);
        $delivery_boy->license_image = $license_upload_id;

        $delivery_boy->save();

        return response()->json([
            "success" => true,
            "message" => "Updated Successfully"
        ]);

    }

    public function uploadVehicleRegistration(Request $request){

        $request->validate([
            'registration_paper' => 'image|mimes:jpeg,png,jpg,gif,svg,webp|max:512',
        ]);


        $user = Auth::user();
        $delivery_boy = $user->delivery_boy;

        $registration_paper = $this->uploadRiderFile($request->registration_paper);
        $delivery_boy->registration_paper      = $registration_paper;
        $delivery_boy->is_registration_complete = true;
        $delivery_boy->save();

        return response()->json([
            "success" => true,
            "message" => "Updated Successfully"
        ]);

    }
}
