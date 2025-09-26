<?php

use App\Models\Shipyard\Setting;
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
            "app_accent_color_1_dark" => "#60cc89",
            "app_accent_color_1_light" => "#60cc89",
            "app_accent_color_2_dark" => "#457c3f",
            "app_accent_color_2_light" => "#457c3f",
            "app_accent_color_3_dark" => "#ffb400",
            "app_accent_color_3_light" => "#ffb400",
            "app_logo_path" => "/msznm.svg",
            "app_name" => "Muzyka Szyta Na Miarę",
            "app_theme" => "austerity",
            "metadata_author" => "Wojciech Przybyła",
            "metadata_description" => "Poszukujesz kogoś, kto pomoże Ci w sprawach muzycznych? Potrzebujesz podkładu lub nut? Szukasz DJa na imprezę, który nie tylko puszcza piosenki? Napisz do mnie.",
            "metadata_image" => "/media/thumbnail.jpg",
            "metadata_keywords" => "Wojciech Przybyła, Wesoły Wojownik, fajna strona, WPWW, podkłady, nuty, transkrypcja, patrytury, studio, muzyka",
            "metadata_title" => "Muzyka Szyta Na Miarę | Podkłady/nuty/koncerty dopasowane do Twoich potrzeb",
            "users_login_is" => "none",
        ] as $name => $value) {
            Setting::find($name)->update(["value" => $value]);
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
