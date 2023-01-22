<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";
    protected $dates = ["deadline", "hard_deadline"];

    protected $fillable = ["price_code_override", "price", "paid", "status_id", "deadline", "wishes"];

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
        return $this->belongsTo(QuestType::class);
    }
    public function payments(){
        return $this->hasMany(StatusChange::class, "re_quest_id")->where("new_status_id", 32);
    }
}
