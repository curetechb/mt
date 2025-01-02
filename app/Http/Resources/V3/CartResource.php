<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $product = $this->product;

        return [
            "id" => $this->id,
            "product_id" => $this->product_id,
            "product_name" => $this->product->name,
            "product_name_bn" => $this->product->getTranslation("name", "bd"),
            "image" => env("AWS_URL")."/".api_asset($this->product->thumbnail_img),
            "unit_price" => $this->product->unit_price,
            "out_of_stock" => $product->current_stock <= $product->low_stock_quantity,
            "unit" => $this->product->unit_value." ".$this->product->unit,
            "quantity" => $this->quantity,
            // 'session' => request()->session()->get('temp_user_id'),
        ];
    }
}
