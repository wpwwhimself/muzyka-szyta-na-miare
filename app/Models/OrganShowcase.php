<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganShowcase extends Model
{
    use HasFactory;

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
