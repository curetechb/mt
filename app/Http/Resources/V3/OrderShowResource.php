<?php

namespace App\Http\Resources\V3;

use Illuminate\Http\Resources\Json\JsonResource;
use Auth;
use App\Models\Order;

class OrderShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $shipping_address = json_decode($this->shipping_address);

        $address = $shipping_address->address.", ".$shipping_address->state;


        if ($shipping_address->floor_no)
            $address .= ", Floor No - $shipping_address->floor_no";

        if ($shipping_address->apartment)
            $address .= ", Apartment - $shipping_address->apartment";

        $total_with_discount = $this->grand_total - $this->reward_discount - $this->coupon_discount;

        $user = Auth::user();
        $emergency_order = Order::where("user_id", $user->id)->where("id", "!=", $this->id)->where("is_emergency_order", true)->where("payment_status", "!=", "paid")->first();
        $previous_due = 0;
        $total_with_emergency_due = 0;

        if($emergency_order){
            $previous_due = $emergency_order->grand_total - $emergency_order->reward_discount - $emergency_order->coupon_discount;
            $total_with_emergency_due = $total_with_discount + $previous_due;
        }

        $subtotal = $this->grand_total - $this->shipping_cost;

        return [
            "id" => $this->id,
            "address" => $address,
            "code" => $this->code,
            "subtotal" => currency_symbol(). $subtotal,
            "grand_total" => currency_symbol(). $this->grand_total,
            "reward_discount" => currency_symbol().$this->reward_discount,
            "coupon_discount" => currency_symbol().$this->coupon_discount,
            "total_with_discount" => currency_symbol(). $total_with_discount,
            "previous_due" => currency_symbol(). $previous_due,
            "total_with_due" => currency_symbol(). $total_with_emergency_due,
            "created_at" => $this->created_at->format("d-m-Y"),
            "delivery_status" => $this->delivery_status,
            "payment_status" => $this->payment_status,
            "pickup_latitude" => (double) $this->latitude,
            "pickup_longitude" => (double) $this->longitude,
            "dropoff_latitude" => (double) $this->area->latitude ?? null,
            "dropoff_longitude" => (double) $this->area->longitude ?? null,
            "shipping_cost" => $this->shipping_cost,
            "items" => OrderedItemResource::collection($this->orderDetails),
        ];
    }
}
