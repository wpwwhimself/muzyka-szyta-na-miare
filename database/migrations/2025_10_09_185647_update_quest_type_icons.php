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
        Schema::table("quest_types", function (Blueprint $table) {
            $table->string("icon")->nullable();
        });

        $icons = [
            "P" => "volume-high",
            "N" => "file-music",
            "O" => "scissors-cutting",
        ];
        foreach ($icons as $code => $icon_name) {
            QuestType::where("code", $code)->update(["icon" => $icon_name]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("quest_types", function (Blueprint $table) {
            $table->dropColumn("icon");
        });
    }
};
