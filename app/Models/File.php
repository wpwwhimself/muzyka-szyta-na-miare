<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardAttributes;
use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\ComponentAttributeBag;

class File extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Pliki",
        "icon" => "file-multiple",
        "description" => "",
        "role" => "archmage",
        "ordering" => 65,
    ];

    protected $fillable = [
        "song_id",
        "variant_name", "version_name",
        "transposition",
        "description",
        "file_paths",
    ];

    #region presentation
    public function __toString(): string
    {
        return implode(" | ", [$this->variant_name, $this->version_name, $this->transposition]);
    }

    public function optionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(" | ", [$this->variant_name, $this->version_name, $this->transposition]),
        );
    }

    public function displayTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.shipyard.app.h", [
                "lvl" => 3,
                "icon" => $this->icon ?? self::META["icon"],
                "attributes" => new ComponentAttributeBag([
                    "role" => "card-title",
                ]),
                "slot" => implode(" | ", [$this->variant_name, $this->version_name, $this->transposition]),
            ])->render(),
        );
    }

    public function displaySubtitle(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.shipyard.app.model.badges", [
                "badges" => $this->badges,
            ])->render(),
        );
    }

    public function displayMiddlePart(): Attribute
    {
        return Attribute::make(
            get: fn () => view("components.shipyard.app.model.connections-preview", [
                "connections" => self::getConnections(),
                "model" => $this,
            ])->render(),
        );
    }
    #endregion

    #region fields
    use HasStandardFields;

    public const FIELDS = [
        "variant_name" => [
            "label" => "Nazwa wariantu",
            "type" => "text",
            "icon" => "label",
            "placeholder" => "podstawowy",
        ],
        "version_name" => [
            "label" => "Nazwa wersji",
            "type" => "text",
            "icon" => "label-variant",
            "placeholder" => "wersja główna",
        ],
        "transposition" => [
            "label" => "Transpozycja",
            "type" => "number",
            "icon" => "music-note-plus",
            "placeholder" => 0,
        ],
        "description" => [
            "label" => "Opis",
            "type" => "TEXT",
            "icon" => "text",
        ],
        "file_paths" => [
            "label" => "Pliki",
            "type" => "JSON",
            "icon" => "file-multiple",
            "columnTypes" => [
                "Format" => "text",
                "Ścieżka" => "text",
            ],
        ],
    ];

    public const CONNECTIONS = [
        "song" => [
            "model" => Song::class,
            "mode" => "one",
            // "field_name" => "",
            // "field_label" => "",
        ],
    ];

    public const ACTIONS = [
        // [
        //     "icon" => "",
        //     "label" => "",
        //     "show-on" => "<list|edit>",
        //     "route" => "",
        //     "role" => "",
        //     "dangerous" => true,
        // ],
    ];
    #endregion

    // use CanBeSorted;
    public const SORTS = [
        // "<name>" => [
        //     "label" => "",
        //     "compare-using" => "function|field",
        //     "discr" => "<function_name|field_name>",
        // ],
    ];

    public const FILTERS = [
        // "<name>" => [
        //     "label" => "",
        //     "icon" => "",
        //     "compare-using" => "function|field",
        //     "discr" => "<function_name|field_name>",
        //     "mode" => "<one|many>",
        //     "operator" => "",
        //     "options" => [
        //         "<label>" => <value>,
        //     ],
        // ],
    ];

    #region scopes
    use HasStandardScopes;
    #endregion

    #region attributes
    protected $casts = ["file_paths" => "array"];

    protected $appends = [

    ];

    use HasStandardAttributes;

    // public function badges(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn () => [
    //             [
    //                 "label" => "",
    //                 "icon" => "",
    //                 "class" => "",
    //                 "style" => "",
    //                 "condition" => "",
    //             ],
    //             [
    //                 "html" => "",
    //             ],
    //         ],
    //     );
    // }

    public function getMissingFilesAttribute()
    {
        $missing = [];

        foreach ($this->file_paths as $extension => $path) {
            if (!Storage::exists($path)) {
                $missing[$extension] = $path;
            }
        }

        return $missing;
    }
    #endregion

    #region relations
    public function tags()
    {
        return $this->belongsToMany(FileTag::class);
    }
    public function song()
    {
        return $this->belongsTo(Song::class);
    }
    public function exclusiveClients()
    {
        return $this->belongsToMany(User::class, "file_user", "file_id", "user_id");
    }
    #endregion

    #region helpers
    #endregion
}
