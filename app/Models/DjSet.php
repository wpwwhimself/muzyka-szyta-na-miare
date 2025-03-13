<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DjSet extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
    ];

    public function songs()
    {
        return $this->belongsToMany(DjSong::class);
    }
}
