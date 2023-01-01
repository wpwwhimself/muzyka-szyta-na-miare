<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showcase extends Model
{
    use HasFactory;

    protected $fillable = [
        "song_id",
        "link_fb",
        "link_ig",
        "created_at",
        "updated_at",
    ];

    public function song(){
        return $this->belongsTo(Song::class);
    }
}
