<?php

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
        Schema::table('showcase_platforms', function (Blueprint $table) {
            $table->string('icon_url')->nullable();
            $table->string("msznm_url")->nullable();
        });

        $icons = [
            "ig" => "/assets/socials/instagram.svg",
            "tt" => "/assets/socials/tiktok.svg",
            "yt" => "/assets/socials/youtube.svg",
        ];
        $links = [
            "yt" => "https://www.youtube.com/@muzykaszytanamiarepl",
            "tt" => "https://tiktok.com/@muzykaszytanamiarepl",
            "ig" => "https://www.instagram.com/muzykaszytanamiarepl/",
        ];
        foreach ($icons as $platform => $icon) {
            ShowcasePlatform::find($platform)->update([
                'icon_url' => $icon,
                "msznm_url" => $links[$platform],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('showcase_platforms', function (Blueprint $table) {
            $table->dropColumn('icon_url');
            $table->dropColumn("msznm_url");
        });
    }
};
