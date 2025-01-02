<?php

namespace App\Http\Resources\UYVMS;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderListIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $address = $this->order->shipping_address;

        $itime = new DateTime($this->order->created_at);
        $idate = $itime->format('n.j.Y');
        $itime = $itime->format('H:i');

        $time = new DateTime($this->order->delivery_time);
        $date = $time->format('n.j.Y');
        $time = $time->format('H:i');

        return [
            "companyCode" => "1000",
            "branchCode" => "1001",
            "invoiceNo" => $this->order->code,
            "customerCode" => $this->order->id,
            "customerName" => $this->order->user->name  ?? "none",
            "customerAddress" => json_decode($address)->address,
            "customerVATRegNo" => "",
            "issueDate" => $idate,
            "issueTime" => $itime,
            "deliveryDate" => $date,
            "deliveryTime" => $time,
            "place" => json_decode($address)->state,
            "car" => "N/A",
            "remarks" => "",
            "challanType" => "1009",
            "distChannel" => "",
            "productCode" => $this->product_id,
            "issueQty" => $this->quantity ?? 0,
            "unitTP" => $this->price ?? 0,
            "totalWithoutSD" => 0,
            "totalSD" => 0,
            "totalWithoutVAT" => 0,
            "totalVAT" => 0,
            "totalWithVAT" => 0,
            "netAmount" => 0,
            "discount" => 0,
            "ait" => 0,
            "errorMessage" => 0,
            "transferTo" => null,
            "cpc" => 0,
            "itemNo" => 0,
            "officeCode" => 0,
            "delivery_status" => $this->order->delivery_status,
            "payment_status" => $this->order->payment_status,
        ];
    }
}
