<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showcase extends Model
{
    use HasFactory;

    protected $fillable = [
        "song_id",
        "platform",
        "link",
    ];

    #region relations
    public function song(){
        return $this->belongsTo(Song::class);
    }

    public function platform() {
        return $this->belongsTo(ShowcasePlatform::class, "platform", "code");
    }
    #endregion
}
