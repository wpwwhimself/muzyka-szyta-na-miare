<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Zlecenia",
        "icon" => "package-variant-closed",
        "description" => "",
        "role" => "",
        "ordering" => 99,
    ];

    public $incrementing = false;
    protected $keyType = "string";
    protected $casts = [
        "deadline" => "datetime",
        "hard_deadline" => "datetime",
        "delayed_payment" => "datetime",
    ];

    protected $fillable = ["id", "price_code_override", "price", "paid", "status_id", "deadline", "delayed_payment", "wishes", "files_ready", "has_files_on_external_drive"];

    // rounded prices
    public function getPriceAttribute($val) {
        return round($val, 2);
    }
    public function setPriceAttribute($val) {
        $this->attributes["price"] = round($val, 2);
    }

    public function getQuestTypeLetterAttribute(){
        return substr($this->id, 0, 1);
    }
    public function getDelayedPaymentInEffectAttribute(){
        return $this->delayed_payment > Carbon::today() && !$this->paid;
    }
    public function getIsPriorityAttribute(){
        return preg_match("/z/", $this->price_code_override);
    }
    public function getLinkToAttribute(){
        return route("quest", ["id" => $this->id]);
    }
    public function getPaymentsSumAttribute(){
        return $this->payments->sum("comment");
    }
    public function getPaymentRemainingAttribute() {
        return $this->price - $this->payments_sum;
    }
    public function getCompletedOnceAttribute() {
        return $this->history->whereIn("new_status_id", [14, 19])->count() > 0;
    }

    public function client(){
        return $this->belongsTo(User::class, "client_id");
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function song(){
        return $this->belongsTo(Song::class);
    }
    public function quest_type(){
        return $this->belongsTo(QuestType::class, "quest_type_letter", "code");
    }
    public function history(){
        return $this->hasMany(StatusChange::class, "re_quest_id")->orderByDesc("date")->orderByDesc("new_status_id");
    }
    public function payments(){
        return $this->hasMany(StatusChange::class, "re_quest_id")->whereIn("new_status_id", [32, 34]);
    }
    public function visibleInvoices(){
        return $this->belongsToMany(Invoice::class, InvoiceQuest::class, "quest_id", "invoice_id")->where("visible", true);
    }
    public function allInvoices(){
        return $this->belongsToMany(Invoice::class, InvoiceQuest::class, "quest_id", "invoice_id");
    }
}
