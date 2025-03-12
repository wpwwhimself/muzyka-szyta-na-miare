<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DjSong extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = "string";

    public const TEMPOS = [
        [
            "label" => "ślimacze",
            "icon" => "🐌",
            "code" => 1,
        ],
        [
            "label" => "wolne",
            "icon" => "🐢",
            "code" => 2,
        ],
        [
            "label" => "średnie",
            "icon" => "🙂",
            "code" => 3,
        ],
        [
            "label" => "szybkie",
            "icon" => "🐶",
            "code" => 4,
        ],
        [
            "label" => "padaka",
            "icon" => "🐗",
            "code" => 5,
        ],
    ];

    protected $fillable = [
        "id",
        "title", "artist",
        "key", "tempo",
        "songmap", "has_project_file",
        "lyrics", "chords", "notes",
    ];

    public const PROCESSABLEJSONS = ["lyrics", "chords", "notes"];

    protected $appends = [
        "full_title",
        "tempo_pretty",
        "parts",
    ];


    #region attributes
    protected $casts = [
        "has_project_file" => "boolean",
        "lyrics" => "json",
        "chords" => "json",
        "notes" => "json",
    ];

    public function getFullTitleAttribute()
    {
        return $this->artist ? "$this->artist – $this->title" : $this->title ?? "bez tytułu";
    }

    public function getTempoPrettyAttribute()
    {
        $tempo = collect(self::TEMPOS)->firstWhere("code", $this->tempo);
        if (!$tempo) return null;
        return "$tempo[icon] $tempo[label]";
    }

    public function getPartsAttribute()
    {
        if (empty($this->songmap)) return null;
        $parts = [];
        preg_match_all("/\w+/", $this->songmap, $parts);
        return $parts[0];
    }

    public function jsonForEdit($field)
    {
        if (empty($this->{$field})) return null;
        return collect($this->{$field})
            ->map(fn ($value, $part) => "//$part\n$value")
            ->join("\n\n");
    }
    #endregion

    #region helpers
    public static function nextId()
    {
        $newest_id = DjSong::orderBy("id", "desc")->value("id") ?? "D000";
        $newest_id_last = substr($newest_id, 1);
        if(in_array($newest_id_last, ["ZZZ"])){
            return "D000";
        }
        return "D" . to_base36(from_base36($newest_id_last) + 1, 3);
    }

    public static function processJsonForEdit($value = null)
    {
        if (empty($value)) return null;
        return collect(preg_split("/(\r?\n){2}(?=\/\/)/", $value))
            ->mapWithKeys(function ($value) {
                $parts = [];
                preg_match("/^\/\/(\w+)\r?\n(.*)$/s", $value, $parts);
                return [$parts[1] => $parts[2]];
            });
    }
    #endregion
}
