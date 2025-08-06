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
        "values",
        "mail_sent",
        "date",
        "pinned",
    ];
    protected $casts = [
        "date" => "datetime",
    ];
    // const CREATED_AT = "date";
    // const UPDATED_AT = "date";
    public $timestamps = false;

    public function getChangesListAttribute(){
        return json_decode($this->values);
    }
    public function getReQuestAttribute(){
        return (strlen($this->re_quest_id) == 36)
            ? Request::find($this->re_quest_id)
            : Quest::find($this->re_quest_id);
    }

    public function invoice(){
        return $this->hasManyThrough(Invoice::class, InvoiceQuest::class, "quest_id", "id", "re_quest_id", "invoice_id");
    }
    public function changer(){
        if($this->changed_by > 1) return $this->hasOne(User::class, "id", "changed_by");
    }
    public function status(){
        return $this->hasOne(Status::class, "id", "new_status_id");
    }
}
