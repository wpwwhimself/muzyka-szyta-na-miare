<?php

namespace App\Models;

use App\Models\Shipyard\Role;
use App\Models\Shipyard\User as ShipyardUser;
use Carbon\Carbon;

class User extends ShipyardUser
{
    public const FROM_SHIPYARD = true;

    public function __toString()
    {
        return $this->notes ?? $this->name;
    }

    #region fields
    public const CONNECTIONS = [
        "roles" => [
            "model" => Role::class,
            "mode" => "many",
            "role" => "technical",
        ],
        "notes" => [
            "model" => UserNote::class,
            "mode" => "one",
        ],
    ];
    #endregion

    #region relations
    public function notes()
    {
        return $this->hasOne(UserNote::class);
    }

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

    public function comments() {
        return $this->hasMany(StatusChange::class, "changed_by")
            ->whereIn("new_status_id", [14, 19])
            ->whereNotNull("comment")
            ->orderByDesc("date");
    }

    public function top10() {
        return $this->morphMany(Top10::class, "entity");
    }
    #endregion
}
