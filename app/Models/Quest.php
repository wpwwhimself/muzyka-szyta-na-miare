<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";
    protected $dates = ["deadline", "hard_deadline", "delayed_payment"];

    protected $fillable = ["price_code_override", "price", "paid", "status_id", "deadline", "delayed_payment", "wishes"];

    public function getQuestTypeLetterAttribute(){
        return substr($this->id, 0, 1);
    }
    public function getDelayedPaymentInEffectAttribute(){
        return $this->delayed_payment > Carbon::today();
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function song(){
        return $this->belongsTo(Song::class);
    }
    public function quest_type(){
        return $this->belongsTo(QuestType::class, "quest_type_letter", "code");
    }
    public function changes(){
        return $this->hasMany(StatusChange::class, "re_quest_id");
    }
    public function payments(){
        return $this->hasMany(StatusChange::class, "re_quest_id")->where("new_status_id", 32);
    }
    public function visibleInvoices(){
        return $this->belongsToMany(Invoice::class, InvoiceQuest::class, "quest_id", "invoice_id")->where("visible", true);
    }
    public function allInvoices(){
        return $this->belongsToMany(Invoice::class, InvoiceQuest::class, "quest_id", "invoice_id");
    }
}
