<?php

namespace App\Models;

use App\Traits\Shipyard\HasStandardFields;
use App\Traits\Shipyard\HasStandardScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Top10 extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Najlepsi",
        "icon" => "chart-line",
        "description" => "Dane przedstawiane w statystykach najlepszych - najlepsi klienci, najpopularniejsze zlecenia itp.",
        "role" => "archmage",
        "ordering" => 99,
    ];

    protected $table = 'top10';
    public $timestamps = false;

    protected $fillable = [
        'entity_id',
        'entity_type',
        'type',
    ];

    use HasStandardScopes, HasStandardFields;


    public function entity()
    {
        return $this->morphTo();
    }
}
