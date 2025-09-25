<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Top10 extends Model
{
    use HasFactory;
    
    public const META = [
        "label" => "",
        "icon" => "",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

    protected $table = 'top10';
    public $timestamps = false;

    protected $fillable = [
        'entity_id',
        'entity_type',
        'type',
    ];

    public function entity()
    {
        return $this->morphTo();
    }
}
