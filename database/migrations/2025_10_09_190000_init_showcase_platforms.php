<?php

use App\Models\QuestType;
use App\Models\ShowcasePlatform;
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
            ["code" => "ig", "name" => "Instagram", "icon_class" => "instagram", "ordering" => 2],
            ["code" => "tt", "name" => "Tiktok", "icon_class" => "tiktok", "ordering" => 1],
            ["code" => "yt", "name" => "Youtube", "icon_class" => "youtube", "ordering" => 3],
        ] as $s) {
            if (ShowcasePlatform::find($s["code"])) continue;
            ShowcasePlatform::create($s);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
