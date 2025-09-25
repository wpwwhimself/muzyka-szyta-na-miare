<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class DjSampleSet extends Model
{
    public $incrementing = false;
    protected $keyType = "string";

    public const META = [
        "label" => "",
        "icon" => "",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];
    
    protected $fillable = [
        "id",
        "name",
        "description",
    ];

    #region relations
    public function songs() {
        return $this->hasMany(DjSong::class);
    }
    #endregion

    #region attributes
    public function fullName(): Attribute
    {
        return Attribute::make(
            fn () => "$this->id $this->name",
        );
    }
    #endregion
}
