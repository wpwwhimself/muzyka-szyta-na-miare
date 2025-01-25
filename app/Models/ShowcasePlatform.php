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
