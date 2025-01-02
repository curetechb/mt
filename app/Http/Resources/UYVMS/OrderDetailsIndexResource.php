<?php

namespace App\Http\Resources\UYVMS;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailsIndexResource extends JsonResource
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
            "serial" => $this->id,
            "order_no" => $this->code,
            "grand_total" => $this->grand_total,
            "discount" => $this->coupon_discount + $this->reward_discount,
            "items" => OrderItemsResource::collection($this->orderDetails)

        ];
    }
}
