<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        "password",
        "client_name", "email", "phone", "other_medium", "contact_preference",
        "trust", "helped_showcasing",
        "budget", "extra_exp",
        "default_wishes", "special_prices",
        "external_drive",
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
    ];

    protected $appends = [
        "pickiness",
    ];

    #region scopes
    public function scopeClients($query)
    {
        return $query->whereNotIn("id", [0, 1]);
    }
    #endregion

    #region relations
    public function quests(){
        return $this->hasMany(Quest::class, "client_id");
    }

    public function questsDone(){
        return $this->hasMany(Quest::class, "client_id")
            ->where("status_id", 19);
    }

    public function questsUnpaid(){
        return $this->hasMany(Quest::class, "client_id")
            ->where("paid", 0)
            ->whereNotIn("status_id", [17, 18]);
    }

    public function questsRecent() {
        return $this->hasMany(Quest::class, "client_id")
            ->whereDate("updated_at", ">=", Carbon::today()->subMonths(3));
    }
    #endregion

    #region attributes
    public function getExpAttribute(){
        return $this->quests->where("status_id", 19)->count() + $this->extra_exp;
    }

    public function getIsWomanAttribute(){
        return (substr(explode(" ", $this->name)[0], -1) == "a");
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
        return $this->quests->whereNotIn("status_id", [17, 18, 19])->count();
    }

    public function getIsForgottenAttribute(){
        return $this->trust >= 2;
    }
    #endregion
}
