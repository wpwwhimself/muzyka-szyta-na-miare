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
    ];

    public function song(){
        return $this->belongsTo(Song::class);
    }
}
