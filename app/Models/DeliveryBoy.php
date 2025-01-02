<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryBoy extends Model
{
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function area(){
        return $this->belongsTo(City::class, 'city_id');
    }

    public function delivery_histories(){
        return $this->hasMany(DeliveryHistory::class, 'delivery_boy_id');
    }

    public function payment_requests(){

        return $this->hasMany(DeliveryBoyPayment::class, 'rider_id');
    }
}
