<?php

namespace App\Http\Resources\V3;

use App\Models\City;
use Illuminate\Http\Resources\Json\JsonResource;

class RiderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $rider = $this->delivery_boy;

        $riderarea = City::findOrFail($rider->city_id);

        $warehouse = $riderarea->warehouses->first();

        $warehouse_areas = \DB::table('city_warehouse')->where('warehouse_id', $warehouse->id)->pluck("city_id")->toArray();

        return [
            "user_id" => $this->id,
            "rider_id" => $rider->id,
            "name" => $this->name,
            "phone" => $this->phone,
            "address" => $this->address,
            "area_id" => $rider->city_id,
            "warehouse_areas" => $warehouse_areas,
            "area_name" => $rider->area->name ?? "",
            "earning" => $rider->total_earning,
            "collection" => $rider->total_collection,

            "vehicle_type" => $rider->vehicle_type,
            "model" => $rider->vehicle_model,
            "year" => $rider->vehicle_year,
            "license_number" => $rider->license,
            "registration_number" => $rider->registration_number,

            "nid_front" => $rider->nid_image ? env("AWS_URL")."/".api_asset($rider->nid_image) : "",
            "nid_back" => $rider->nid_image_backpart ? env("AWS_URL")."/".api_asset($rider->nid_image_backpart) : "",

            "license_image" => $rider->license_image ? env("AWS_URL")."/".api_asset($rider->license_image) : "",
            "registration_paper" => $rider->registration_paper ? env("AWS_URL")."/".api_asset($rider->registration_paper) : "",
            "avatar" => $this->avatar ? env("AWS_URL")."/".api_asset($this->avatar) : "",

            "is_registration_complete" => (boolean) $rider->is_registration_complete,
            "payment_request_sent" => (boolean) $rider->payment_requests()->where("status", "pending")->first() != null
        ];
    }
}
