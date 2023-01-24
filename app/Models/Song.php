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
        "title", "artist",
        "link",
        "notes",
    ];

    public function genre(){
        return $this->belongsTo(Genre::class);
    }
    public function showcase(){
        return $this->hasOne(Showcase::class);
    }
    public function quest(){
        return $this->hasMany(Quest::class);
    }
}
