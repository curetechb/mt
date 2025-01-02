<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        "upload_id",
        "story_group_id",
        "type",
        "duration",
        "show_on_front",
    ];

    public function story_group(){
        return $this->belongsTo(StoryGroup::class);
    }
}
