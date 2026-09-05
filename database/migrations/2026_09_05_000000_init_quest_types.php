<?php

use App\Models\QuestType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ([
            ["id" => 1, "type" => "podkład muzyczny", "code" => "P", "fa_symbol" => "fa-file-audio", "icon" => "volume-high"],
            ["id" => 2, "type" => "nuty", "code" => "N", "fa_symbol" => "fa-music", "icon" => "file-music"],
            ["id" => 3, "type" => "obróbka", "code" => "O", "fa_symbol" => "fa-screwdriver-wrench", "icon" => "scissors-cutting"],
        ] as $qt) {
            if (QuestType::find($qt["id"])) continue;
            QuestType::insert($qt);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
