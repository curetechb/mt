<?php

namespace App\Http\Resources\UYVMS;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemsResource extends JsonResource
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
            "product_id" => $this->product->id,
            "product_name" => $this->product->name,
            "unit_price" => $this->product->unit_price,
            "quantity" => $this->quantity,
            "price" => $this->quantity*$this->product->unit_price,

        ];
    }
}
