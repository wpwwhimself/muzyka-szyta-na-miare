<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    use HasFactory;

    public const META = [
        "label" => "",
        "icon" => "",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

    protected $fillable = ["cost_type_id", "desc", "amount", "created_at"];

    public function type(){
        return $this->belongsTo(CostType::class, "cost_type_id");
    }
}
