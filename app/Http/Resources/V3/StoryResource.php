<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
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
            "story_group_id" => $this->story_group_id,
            "image" => env("AWS_URL")."/".api_asset($this->upload_id),
            "navigation_url" => $this->navigation_url,
            "type" => $this->type,
            "duration" => $this->duration,
            "created_at" => $this->created_at,
        ];
    }
}
