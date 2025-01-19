<?php

use App\Models\Showcase;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ConsolidateShowcaseColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update(<<<SQL
UPDATE showcases
SET link_ig = coalesce(link_ig, link_fb, link_tt, link_yt)
WHERE true;
SQL);

        Schema::table("showcases", function (Blueprint $table) {
            $table->string("platform")->default("yt");
            $table->renameColumn("link_ig", "link");
            $table->dropColumn(["link_fb", "link_yt", "link_tt"]);
        });

        Showcase::all()->each(function (Showcase $showcase) {
            $showcase->update([
                "platform" =>
                    Str::contains($showcase->link, ["instagram"]) ? "ig" : (
                    Str::contains($showcase->link, ["facebook"]) ? "fb" : (
                    Str::contains($showcase->link, ["tiktok"]) ? "tt" : (
                    "yt")))
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
