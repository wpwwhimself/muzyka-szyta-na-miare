<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientShowcase extends Model
{
    use HasFactory;

    public const META = [
        "label" => "",
        "icon" => "",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

    protected $fillable = [
        "song_id",
        "embed",
    ];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
