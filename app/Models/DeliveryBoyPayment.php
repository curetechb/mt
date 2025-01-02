<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryBoyPayment extends Model
{
    use HasFactory;

    public function rider(){
    	return $this->belongsTo(DeliveryBoy::class, 'rider_id');
    }
}
