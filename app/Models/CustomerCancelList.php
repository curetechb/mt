<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCancelList extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'notes',
        'total_order',
        'cancel',
        'delivery',
        'nextday',
        'processing'
    ];
}
