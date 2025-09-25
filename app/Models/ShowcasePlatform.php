<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class ShowcasePlatform extends Model
{
    use HasFactory;

    public const META = [
        "label" => "",
        "icon" => "",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'icon_class',
        'ordering',
    ];

    public function __toString()
    {
        return "{$this->icon} {$this->name}";
    }

    #region suggestions
    public static function reelCount(bool $organ = false)
    {
        $model = "App\\Models\\" . ($organ ? "Organ" : "") . "Showcase";
        $reels_count = $model::orderByDesc("created_at")
            ->select("platform")
            ->get()
            ->pluck("platform")
            ->countBy();
        $total_count = ShowcasePlatform::all()
            ->pluck("code")
            ->flip()
            ->map(fn ($x) => 0)
            ->merge($reels_count)
            ->map(fn ($count, $code) => compact("code", "count"));

        return $total_count;
    }

    public static function suggest(bool $organ = false)
    {
        return self::reelCount($organ)
            ->sortBy("count")
            ->first();
    }
    #endregion

    #region attributes
    public function getIconAttribute()
    {
        return view(
            "components.fa-icon",
            [
                "pop" => $this->name,
                "attributes" => new ComponentAttributeBag([
                    "class" => "fa-brands fa-{$this->icon_class}" ?? "fas fa-hashtag",
                ]),
            ]
        )->render();
    }
    #endregion

    #region relations
    public function showcases()
    {
        return $this->hasMany(Showcase::class, "platform");
    }
    #endregion
}
