<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    use HasFactory, Uuids;

    protected $fillable = [
        "made_by_me",
        "client_id", "client_name", "email", "phone", "other_medium", "contact_preference",
        "song_id", "quest_type_id", "title", "artist", "link", "genre_id", "wishes", "wishes_quest",
        "price_code", "price", "deadline", "hard_deadline",
        "status_id", "quest_id"
    ];
    protected $dates = ["deadline", "hard_deadline"];

    public function client(){
        return $this->belongsTo(Client::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function quest_type(){
        return $this->belongsTo(QuestType::class);
    }
    public function song(){
        return $this->belongsTo(Song::class);
    }
}
