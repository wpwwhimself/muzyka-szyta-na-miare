<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        "has_showcase_file",
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

    public function getHasShowcaseFileAttribute(){
        return Storage::exists("showcases/$this->id.ogg");
    }
    public function getTypeAttribute(){
        $type_letter = substr($this->id, 0, 1);
        if($type_letter == "A") return collect(["id" => 0, "type" => "nie ustalono (archiwalne)", "code" => "A", "fa_symbol" => "fa-circle-question"]);
        return QuestType::where("code", $type_letter)->first();
    }
}
