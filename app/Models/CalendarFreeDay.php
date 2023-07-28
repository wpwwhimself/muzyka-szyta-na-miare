<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarFreeDay extends Model
{
    use HasFactory;

    protected $fillable = [
        "date"
    ];

    protected $dates = ["date"];
    const UPDATED_AT = null;
}
