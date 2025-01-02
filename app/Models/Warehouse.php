<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "address",
        "state_id",
        "city_id",
        "latitude",
        "longitude",
    ];

    public function areas(){
        return $this->belongsToMany(City::class);
    }
}
