<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class RiderDeliveryHistoryResource extends JsonResource
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
            "order_id" => $this->order->code ?? "",
            "earning" => $this->earning,
            "collection" => $this->collection,
            "distance" => $this->distance,
            "delivery_time" => $this->created_at->format("d-m-Y h:iA"),
        ];
    }
}
