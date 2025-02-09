<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SongTag extends Model
{
    use HasFactory;

    protected $fillable = ["name", "description"];
    public $timestamps = false;

    public function songs()
    {
        return $this->belongsToMany(Song::class);
    }
}
