<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $front_story = $this->stories()->where("show_on_front", true)->first();

        return [
            "id" => $this->id,
            "image" => env("AWS_URL")."/".api_asset($front_story->upload_id ?? ""),
            "stories" => StoryResource::collection($this->stories()->where("is_active", true)->get())
        ];
    }
}
