<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\View\ComponentAttributeBag;
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

    public function __toString()
    {
        return _ct_($this->client_name) . " " . $this->badges;
    }

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

    #region icons
    public function getExpIconAttribute()
    {
        $dict = [
            "normal" => ["klient", "fas fa-user"],
            "veteran" => ["stały klient", "fas fa-user-shield"],
        ];
        $ddict = $dict[$this->is_veteran ? "veteran" : "normal"];
        return view(
            "components.fa-icon",
            [
                "pop" => $ddict[0],
                "attributes" => new ComponentAttributeBag([
                    "class" => $ddict[1],
                ]),
            ]
        )->render();
    }
    public function getTrustIconAttribute()
    {
        $dict = [
            1 => ["ponadprzeciętne zaufanie", "success fas fa-hand-holding-heart"],
            2 => ["zapomniany", "success fas fa-ghost"],
            -1 => ["krętacz i oszust", "error fas fa-user-ninja"],
        ];
        if (!in_array($this->trust, array_keys($dict))) return "";
        return view(
            "components.fa-icon",
            [
                "pop" => $dict[$this->trust][0],
                "attributes" => new ComponentAttributeBag([
                    "class" => $dict[$this->trust][1],
                ]),
            ]
        )->render();
    }

    public function getBadgesAttribute()
    {
        $icons = [
            "veteran" => [
                $this->is_veteran,
                "fas fa-user-shield",
                "Stały klient"
            ],
            "patron" => [
                $this->is_patron && is_archmage(),
                "fas fa-award showcase-highlight",
                "Patron"
            ],
            "trusted" => [
                $this->trust > 0,
                "fas fa-hand-holding-heart success",
                "Zaufany"
            ],
            "active" => [
                $this->top10->where("type", "active")->count() > 0,
                "fas fa-chart-line success",
                "Zleceń w ostatnich 3 mc: ".$this->questsRecent()->count()
            ],
            "picky" => [
                $this->pickiness >= 1.5 && is_archmage(),
                "fas fa-people-pulling error",
                "Wybredny"
            ],
            "forgotten" => [
                $this->is_forgotten && is_archmage(),
                "fas fa-ghost success",
                "Zapomniany"
            ],
            "kio" => [
                $this->trust < 0 && is_archmage(),
                "fas fa-user-ninja error",
                "Na czarnej liście"
            ],
            "special_prices" => [
                $this->special_prices && is_archmage(),
                "fas fa-address-card",
                "Niestandardowe ceny:<br>"._ct_($this->special_prices)
            ],
            "default_wishes" => [
                $this->default_wishes && is_archmage(),
                "fas fa-cloud",
                "Domyślne życzenia:<br>"._ct_($this->default_wishes)
            ],
            "budget" => [
                $this->budget && is_archmage(),
                "fas fa-sack-dollar success",
                "Budżet:<br>"._c_(as_pln($this->budget))
            ],
        ];
        return collect($icons)
            ->filter(fn ($data) => $data[0])
            ->map(fn ($data) => view(
                "components.fa-icon",
                [
                    "pop" => $data[2],
                    "attributes" => new ComponentAttributeBag([
                        "class" => $data[1],
                    ]),
                ]
            )->render())
            ->join("");
    }
    #endregion
}
