<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        "quest_id", "full_code_override",
        "primary", "visible",
        "amount", "paid",
        "payer_name", "payer_title", "payer_address", "payer_nip", "payer_regon",
        "payer_email", "payer_phone",
    ];

    public function getFullCodeAttribute(){
        return $this->full_code_override ?? $this->id . "/" . ($this->primary ? "F" : "FD") . "/" . $this->quest_id;
    }
    public function getIsPaidAttribute(){
        return $this->amount == $this->paid;
    }

    public function quests(){
        return $this->belongsToMany(Quest::class, InvoiceQuest::class);
    }
}
