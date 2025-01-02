<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;

class RiderPaymentHistoryResource extends JsonResource
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
            "amount" => $this->amount,
            "status" => $this->status,
            "reject_reason" => $this->reject_reason,
            "created_at" => $this->created_at->format("d-m-Y h:iA"),
        ];
    }
}
