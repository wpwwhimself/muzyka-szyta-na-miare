<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceQuest extends Model
{
    use HasFactory;

    public const META = [
        "label" => "",
        "icon" => "",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

    protected $fillable = [
        "invoice_id", "quest_id",
        "primary",
        "amount", "paid",
    ];

    public function getIsPaidAttribute(){
        return $this->amount == $this->paid;
    }

    public function mainInvoice(){
        return $this->belongsTo(Invoice::class, "invoice_id");
    }
}
