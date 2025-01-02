<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryGroup extends Model
{
    use HasFactory;

    public function stories(){
        return $this->hasMany(Story::class);
    }
}
