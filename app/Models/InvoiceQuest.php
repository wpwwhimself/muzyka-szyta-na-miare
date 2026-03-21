<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceQuest extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Pozycje faktury",
        "icon" => "invoice-list-outline",
        "description" => "",
        "role" => "archmage",
        "ordering" => 44,
    ];

    protected $fillable = [
        "invoice_id", "quest_id",
        "primary",
        "amount", "paid",
    ];

    #region attributes
    public function getIsPaidAttribute(){
        return $this->amount == $this->paid;
    }
    #endregion

    #region relations
    public function mainInvoice(){
        return $this->belongsTo(Invoice::class, "invoice_id");
    }

    public function quest() {
        return $this->belongsTo(Quest::class);
    }
    #endregion
}
