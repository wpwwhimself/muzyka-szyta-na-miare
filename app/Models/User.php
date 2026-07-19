<?php

namespace App\Models;

use Wpwwhimself\Shipyard\Models\Role;
use Wpwwhimself\Shipyard\Models\User as ShipyardUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends ShipyardUser
{
    public const FROM_SHIPYARD = true;

    #region presentation
    public function __toString()
    {
        return $this->notes ?? $this->name;
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => "[$this->id] $this",
        );
    }

    public function nameAndBadges(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->notes->name_and_badges,
        );
    }
    #endregion

    #region fields
    public const CONNECTIONS = [
        "notes" => [
            "model" => UserNote::class,
            "mode" => "one",
        ],
    ];
    #endregion

    #region scopes
    public function scopeForConnection($query)
    {
        return $this->orderBy("id");
    }
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
        return $this->quests()
            ->where("status_id", 19);
    }

    public function questsUnpaid(){
        return $this->quests()
            ->where("paid", 0)
            ->whereNotIn("status_id", [18]);
    }

    public function questsRecent() {
        return $this->quests()
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
