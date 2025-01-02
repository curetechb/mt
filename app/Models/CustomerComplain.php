<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerComplain extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'description',
        'status'
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
