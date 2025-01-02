<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class LevelThreeCategoryResource extends JsonResource
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
            "id"=> $this->id,
            "name" => $this->name,
            "name_bn" => $this->getTranslation("name", "bd") ?: $this->name,
            "banner" => env("APP_URL")."/".api_asset($this->banner),
            "icon" => env("APP_URL")."/".api_asset($this->icon),
            "image" => env("APP_URL")."/".api_asset($this->image),
            "slug" => $this->slug,
            "meta_title"=> $this->meta_title,
            "meta_description"=> $this->meta_description,
            // "children" => CategoryResource::collection($this->categories)
        ];
    }
}
