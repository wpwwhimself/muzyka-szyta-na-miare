<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DjSet extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Zestawy DJa",
        "icon" => "tray-full",
        "description" => "",
        "role" => "archmage",
        "ordering" => 72,
    ];

    protected $fillable = [
        "name",
        "description",
    ];

    public function songs()
    {
        return $this->belongsToMany(DjSong::class);
    }
}
