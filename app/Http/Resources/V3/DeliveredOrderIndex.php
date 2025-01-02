<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class DeliveredOrderIndex extends JsonResource
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

       "id" => $this->id,
        "user_id" => $this->user_id,
        "grand_total" => $this->grand_total,
        "delivery_status" => $this->delivery_status,
        "status" => $this->payment_status,
        "paid_date" => $this->delivery_time,
        "created_at" => $this->created_at,
        "updated_at" => $this->updated_at

       ];
    }
}
