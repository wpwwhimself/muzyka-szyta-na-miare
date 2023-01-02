<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "artist", "notes",
    ];

    public function genre(){
        return $this->belongsTo(Genre::class);
    }
}
