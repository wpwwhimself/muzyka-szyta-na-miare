<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GigPriceRate extends Model
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
        "label",
        "value",
    ];
}
