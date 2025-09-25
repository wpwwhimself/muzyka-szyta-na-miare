<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileTag extends Model
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
        "name",
        "icon",
        "color",
    ];

    public function files()
    {
        return $this->belongsToMany(File::class);
    }
}
