<?php

namespace App\Models;

use Attribute;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Song extends Model
{
    use HasFactory;

    public const META = [
        "label" => "",
        "icon" => "",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "id",
        "title", "artist",
        "genre_id",
        "link",
        "price_code", "notes",
        "has_recorded_reel", "has_original_mv",
    ];
    protected $appends = [
        "full_title",
        "has_showcase_file",
        "work_time_total",
        "now_working",
    ];

    public function genre(){
        return $this->belongsTo(Genre::class);
    }
    public function showcase(){
        return $this->hasOne(Showcase::class);
    }
    public function clientShowcase() {
        return $this->hasMany(ClientShowcase::class);
    }
    public function quests(){
        return $this->hasMany(Quest::class);
    }
    public function workTime() {
        return $this->hasMany(SongWorkTime::class)->orderByDesc("time_spent");
    }
    public function tags() {
        return $this->belongsToMany(SongTag::class);
    }
    public function files()
    {
        return $this->hasMany(File::class)->orderByDesc("updated_at");
    }
    public function type()
    {
        return $this->belongsTo(QuestType::class, "type_letter", "code");
    }

    public function getCostsAttribute() {
        return Cost::where("desc", "like", "%".$this->id."%")
            ->orderByDesc("created_at")
            ->get();
    }
    public function getWorkTimeTotalAttribute(){
        return CarbonInterval::seconds($this->hasMany(SongWorkTime::class)->sum(DB::raw("TIME_TO_SEC(time_spent)")))
            ->cascade()
            ->format("%h:%I:%S")
        ;
    }
    public function getNowWorkingAttribute() {
        return $this->workTime
            ->filter(fn($log) => $log->now_working)
            ->count()
            > 0
        ;
    }
    public function getHasShowcaseFileAttribute(){
        return Storage::exists("showcases/$this->id.ogg");
    }
    public function getHasSafeFilesAttribute() {
        return Storage::exists("safe/$this->id");
    }
    public function getLinkToAttribute(){
        return route("songs", ["search" => $this->id]);
    }
    public function getFullTitleAttribute(){
        return implode(' – ', array_filter([
            $this->artist,
            $this->title ?? 'utwór bez tytułu',
        ], fn($v) => !empty($v)));
    }
    public function getTypeLetterAttribute(){
        return substr($this->id, 0, 1);
    }
}
