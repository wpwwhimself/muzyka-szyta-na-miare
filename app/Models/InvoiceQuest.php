<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceQuest extends Model
{
    use HasFactory;

    protected $fillable = [
        "invoice_id", "quest_id",
        "primary",
        "amount", "paid",
    ];
}
