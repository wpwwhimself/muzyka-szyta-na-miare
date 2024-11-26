<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileTag extends Model
{
    use HasFactory;

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
