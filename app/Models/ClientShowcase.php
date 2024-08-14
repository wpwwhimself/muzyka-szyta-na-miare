<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientShowcase extends Model
{
    use HasFactory;

    protected $fillable = [
        "song_id",
        "embed",
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
