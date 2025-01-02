<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $shipping_address = json_decode($this->shipping_address);
        $address = $shipping_address->address;
        $address .= $this->area ? ", ".$this->area->name : "";

        return [
            "id" => $this->id,
            "code" => $this->code,
            "delivery_status" => $this->delivery_status,
            "payment_status" => $this->payment_status,
            "grand_total" => currency_symbol(). $this->grand_total,
            "address" => $address,
            "area_id" => $this->city_id,
            "dropoff_latitude" => (double) $this->area?->latitude ?? null,
            "dropoff_longitude" => (double) $this->area?->longitude ?? null,
            "created_at" => $this->created_at->format("d-M-Y h:iA")
        ];
    }
}
