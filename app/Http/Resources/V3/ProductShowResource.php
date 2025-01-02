<?php

namespace App\Http\Resources\V3;

use App\Models\Cart;
use Illuminate\Http\Resources\Json\JsonResource;
use Auth;

class ProductShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if(Auth::check()){
            $cart = Cart::where("product_id", $this->id)->where("user_id", Auth::user()->id)->first();
        }else{
            $cart = Cart::where("product_id", $this->id)->where("temp_user_id", request("temp_id"))->first();
        }

        return [
            "id" => $this->id,
            "name" => $this->name,
            "name_bn" => $this->getTranslation("name", "bd") ?: $this->name,
            "has_discount" => false,
            "out_of_stock" => $this->current_stock <= $this->low_stock_quantity,
            "unit_price" => currency_symbol().$this->unit_price,
            "unit" => $this->unit_value." ".$this->unit,
            "quantity" => $cart->quantity ?? 0,
            "cart_id" => $cart->id ?? null,
            "image" => $this->thumbnail_img ? env("APP_URL")."/".api_asset($this->thumbnail_img) : "/assets/img/placeholder.jpg",
            "description" => $this->description,
            "slug" => $this->slug
        ];
    }
}
