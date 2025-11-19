<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DjShowcase extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Rolki DJowe",
        "icon" => "guitar-electric",
        "description" => "",
        "role" => "archmage",
        "ordering" => 33,
    ];

    protected $fillable = [
        "platform",
        "link",
    ];

    #region relations
    public function platformData() {
        return $this->belongsTo(ShowcasePlatform::class, "platform", "code");
    }
    #endregion
}
