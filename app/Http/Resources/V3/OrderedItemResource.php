<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderedItemResource extends JsonResource
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
            "name_en" => $this->product->name,
            "name_bn" => $this->product->getTranslation("name", "bd"),
            "unit_price" => currency_symbol(). $this->price / $this->quantity,
            "unit" => $this->product->unit_value." ".$this->product->unit,
            "quantity" => $this->quantity,
            "image" => env("AWS_URL")."/".api_asset($this->product->thumbnail_img),
        ];
    }
}
