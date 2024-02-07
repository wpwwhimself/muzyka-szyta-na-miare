<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        "client_name", "email", "phone", "other_medium", "contact_preference",
        'trust', 'helped_showcasing',
        'budget', "extra_exp",
        "default_wishes", "special_prices",
    ];
    protected $appends = ["pickiness"];

    public function user(){
        return $this->belongsTo(User::class, "id", "id");
    }
    public function quests(){
        return $this->hasMany(Quest::class);
    }
    public function questsDone(){
        return $this->hasMany(Quest::class)->where("status_id", 19);
    }
    public function questsUnpaid(){
        return $this->hasMany(Quest::class)->where("paid", 0);
    }

    public function getExpAttribute(){
        return $this->quests->where("status_id", 19)->count() + $this->extra_exp;
    }
    public function getIsWomanAttribute(){
        return (
            substr(explode(" ", $this->client_name)[0], -1) == "a"
        );
    }
    public function getIsOldAttribute(){
        return $this->created_at < BEGINNING();
    }
    public function getIsVeteranAttribute(){
        return $this->exp >= VETERAN_FROM();
    }
    public function getIsPatronAttribute(){
        return $this->helped_showcasing == 2;
    }
    public function getPickinessAttribute(){
        $correction_requests = StatusChange::where("changed_by", $this->id)->whereIn("new_status_id", [16, 26])->count();
        $quests_total = $this->quests->count();
        if($quests_total == 0) return 0;
        return round($correction_requests / $quests_total, 2);
    }
    public function getCanSeeFilesAttribute(){
        return $this->trust > -1;
    }
    public function getUpcomingQuestsCountAttribute(){
        return $this->quests->whereIn("status_id", [11, 12, 15, 16, 26])->count();
    }
}
