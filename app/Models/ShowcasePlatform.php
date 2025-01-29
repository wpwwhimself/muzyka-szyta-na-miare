<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;

class ShowcasePlatform extends Model
{
    use HasFactory;

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

    public static function reelCount()
    {
        $reels_count = Showcase::orderByDesc("created_at")
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

    public static function suggest()
    {
        return self::reelCount()
            ->sortBy("count")
            ->first();
    }

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
