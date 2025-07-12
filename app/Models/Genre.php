<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
    ];

    #region scopes
    public function scopeOrdered($query) {
        return $query->orderBy("name");
    }
    #endregion

    public function songs() {
        return $this->hasMany(Song::class);
    }
}
