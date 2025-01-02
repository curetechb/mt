<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        "vendor_id",
        "product_id",
        "quantity",
        "note"
    ];

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
