<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showcase extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Showcase'y",
        "icon" => "bullhorn",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

    protected $fillable = [
        "song_id",
        "platform",
        "link",
    ];

    #region relations
    public function song(){
        return $this->belongsTo(Song::class);
    }

    public function platformData() {
        return $this->belongsTo(ShowcasePlatform::class, "platform", "code");
    }
    #endregion
}
