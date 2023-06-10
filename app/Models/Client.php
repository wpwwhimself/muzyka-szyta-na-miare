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
    public function isWoman(){
        return (
            substr(explode(" ", $this->client_name)[0], -1) == "a"
        );
    }
    public function isOld(){
        return $this->created_at < BEGINNING();
    }
    public function getPickinessAttribute(){
        $correction_requests = StatusChange::where("changed_by", $this->id)->whereIn("new_status_id", [16, 26])->count();
        $quests_total = $this->quests->count();
        if($quests_total == 0) return 0;
        return round($correction_requests / $quests_total, 2);
    }
}
