<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['helped_showcasing', 'budget', 'trust'];
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
    public function isMailable(){
        return (
            $this->email
            // &&
            // $this->contact_preference == "email"*/
        );
    }
    public function getPickinessAttribute(){
        $correction_requests = StatusChange::where("changed_by", $this->id)->whereIn("new_status_id", [16, 26])->count();
        $quests_total = $this->quests->count();
        return $correction_requests / $quests_total;
    }
}
