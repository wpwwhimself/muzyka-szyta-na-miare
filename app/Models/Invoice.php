<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ["quest_id", "primary", "amount", "paid"];

    public function fullCode(){
        return $this->id . "/" . ($this->primary ? "F" : "FD") . "/" . $this->quest_id;
    }
    public function quest(){
        return $this->belongsTo(Quest::class);
    }
}
