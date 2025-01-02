<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        "contact_name",
        "contact_number",
        "company_name",
        "address"
    ];


    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

}
