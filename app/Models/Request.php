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
        "price_code", "price", "deadline", "hard_deadline", "delayed_payment",
        "status_id", "quest_id"
    ];
    protected $dates = ["deadline", "hard_deadline", "delayed_payment"];

    // rounded prices
    public function getPriceAttribute($val) {
        return round($val, 2);
    }
    public function setPriceAttribute($val) {
        $this->attributes["price"] = round($val, 2);
    }

    public function getIsPriorityAttribute(){
        return preg_match("/z/", $this->price_code);
    }
    public function getLinkToAttribute(){
        return route("request", ["id" => $this->id]);
    }
    public function getFullTitleAttribute(){
        return implode(' – ', array_filter([
            $this->artist,
            $this->title ?? 'utwór bez tytułu',
        ], fn($v) => !empty($v)));
    }

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
    public function history(){
        return $this->hasMany(StatusChange::class, "re_quest_id")->orderByDesc("date")->orderByDesc("new_status_id");
    }
    public function quest(){
        return $this->belongsTo(Quest::class);
    }
}
