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
    protected $dates = ["date"];
    // const CREATED_AT = "date";
    // const UPDATED_AT = "date";
    public $timestamps = false;

    public function invoice(){
        return $this->hasManyThrough(Invoice::class, InvoiceQuest::class, "quest_id", "id", "re_quest_id", "invoice_id");
    }
}
