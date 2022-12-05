<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusChange extends Model
{
    use HasFactory;

    protected $fillable = [
        "re_quest_id",
        "new_status_id",
        "changed_by",
        "comment",
        "mail_sent",
        "date",
    ];
    // const CREATED_AT = "date";
    // const UPDATED_AT = "date";
    public $timestamps = false;
}
