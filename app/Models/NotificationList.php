<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationList extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "description",
        "product_slug"
    ];

}
