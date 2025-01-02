<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'      => $this->id,
            'name'   => $this->name,
            'apartment' => $this->apartment,
            'floor_no' => $this->floor_no,
            'user_id' => $this->user_id,
            'address' => $this->address,
            'country_id' => $this->country_id,
            'state_id' => $this->state_id,
            'city_id' =>  $this->city_id,
            'country_name' => $this->country->name ?? "",
            'state_name' => $this->state->name ?? "",
            'city_name' => $this->city->name ?? "",
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'set_default' => (int) $this->set_default,
        ];
    }
}
