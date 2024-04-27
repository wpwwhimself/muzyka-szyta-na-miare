<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongWorkTime extends Model
{
    use HasFactory;

    protected $fillable = ["song_id", "status_id", "time_spent", "now_working", "since"];
    protected $dates = ["since"];
    public $timestamps = false;

    public function status() {
        return $this->belongsTo(Status::class);
    }
}
