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

    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = [
        "id",
        "title", "artist",
        "link",
        "price_code", "notes",
    ];
    protected $appends = [
        "full_title", "has_showcase_file",
    ];

    public function genre(){
        return $this->belongsTo(Genre::class);
    }
    public function showcase(){
        return $this->hasOne(Showcase::class);
    }
    public function quests(){
        return $this->hasMany(Quest::class);
    }
    
    public function getCostsAttribute() {
        return Cost::where("desc", "like", "%".$this->id."%")
            ->orderByDesc("created_at")
            ->get();
    }
    public function getWorkTimeAttribute(){
        return CarbonInterval::seconds($this->hasMany(SongWorkTime::class)->sum(DB::raw("TIME_TO_SEC(time_spent)")))
            ->cascade()
            ->format("%h:%I:%S")
        ;
    }
    public function getHasShowcaseFileAttribute(){
        return Storage::exists("showcases/$this->id.ogg");
    }
    public function getHasSafeFilesAttribute() {
        return Storage::exists("safe/$this->id");
    }
    public function getTypeAttribute(){
        $type_letter = substr($this->id, 0, 1);
        if($type_letter == "A") return collect(["id" => 0, "type" => "nie ustalono (archiwalne)", "code" => "A", "fa_symbol" => "fa-circle-question"]);
        return QuestType::where("code", $type_letter)->first();
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
}
