<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryHistory extends Model
{
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function rider(){
        return $this->belongsTo(DeliveryBoy::class, 'delivery_boy_id');
    }
}
