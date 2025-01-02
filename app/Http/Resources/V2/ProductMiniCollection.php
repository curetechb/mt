<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Cart;

class ProductMiniCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->getTranslation('name'),
                    'thumbnail_image' => api_asset($data->thumbnail_img),
                    'banner_image' => $data->banner_img ? api_asset($data->banner_img) : null,
                    'has_discount' => home_base_price($data, false) != home_discounted_base_price($data, false) ,
                    'stroked_price' => home_base_price($data),
                    'min_qty' => $data->min_qty,
                    'max_qty' => $data->max_qty,
                    'main_price' => home_discounted_base_price($data),
                    'stroked_price_wu' => home_discounted_base_price($data, false),
                    'main_price_wu' => home_discounted_base_price($data, false),
                    'rating' => (double) $data->rating,
                    'sales' => (integer) $data->num_of_sale,
                    'in_cart' => Cart::where('temp_user_id', request()->temp_user_id)->where('product_id', $data->id)->first()->quantity ?? 0,
                    'unit' => $data->unit_value > 0 ? $data->unit_value." ".$data->unit : $data->unit,
                    'unit_value' => (integer) $data->unit_value,
                    'low_stock_quantity' => $data->low_stock_quantity,
                    'quantity' => 1,
                    'stock' => $data->current_stock ?? 0,
                    'description' => $data->description,
                    'links' => [
                        'details' => "",
                    ]
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
