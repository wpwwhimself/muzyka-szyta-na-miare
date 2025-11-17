<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Str;

class StatusChange extends Model
{
    use HasFactory;

    public const META = [
        "label" => "Historia zmian",
        "icon" => "timeline",
        "description" => "",
        "role" => "technical",
        "ordering" => 99,
    ];

    protected $fillable = [
        "re_quest_id",
        "new_status_id",
        "changed_by",
        "comment",
        "values",
        "mail_sent",
        "date",
        "pinned",
    ];
    protected $casts = [
        "values" => "collection",
        "date" => "datetime",
    ];
    // const CREATED_AT = "date";
    // const UPDATED_AT = "date";
    public $timestamps = false;

    public function __toString()
    {
        $output = "";
        $details = $this->values?->map(fn ($v, $k) => "**$k**: $v") ?? [];
        $content = [
            view("components.phase-indicator-mini", [
                "status" => $this->status,
                "pop" => false,
                "withName" => true,
            ])->render(),
            "<br>",
            count($details) ? $details->implode("<br>") : null,
            in_array($this->new_status_id, [32, 34]) ? _c_(as_pln($this->comment)) : $this->comment,
            "<span class='grayed-out'>"
                .$this->changed_by_name
                .", ".$this->date."</span>",
        ];
        foreach($content as $value){
            if(empty($value)) continue;
            $output .= Str::startsWith($value, "<") ? $value : Markdown::parse($value);
        }
        return $output;
    }

    public function canBeCorrected(): Attribute
    {
        return Attribute::make(
            get: fn () => false, // todo fix pls
        );
    }
    public function changedByName(): Attribute
    {
        return Attribute::make(
            get: fn () => (
                $this->changed_by == 1 ? "Wojciech Przybyła" : (
                $this->changed_by === null ? Request::find($this->re_quest_id)->client_name : (
                $this->changer->notes->client_name
            ))),
        );
    }
    public function getChangesListAttribute(){
        return json_decode($this->values);
    }
    public function getReQuestAttribute(){
        return (strlen($this->re_quest_id) == 36)
            ? Request::find($this->re_quest_id)
            : Quest::find($this->re_quest_id);
    }

    public function changer(){
        if($this->changed_by > 1) return $this->hasOne(User::class, "id", "changed_by");
    }
    public function status(){
        return $this->hasOne(Status::class, "id", "new_status_id");
    }
}
