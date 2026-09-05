<?php

use App\Models\QuestType;
use App\Models\Status;
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
            ["id" => 1, "status_name" => "nowe", "status_symbol" => "fa-star"],
            ["id" => 4, "status_name" => "nie podejmę się", "status_symbol" => "fa-trash"],
            ["id" => 5, "status_name" => "wycena do akceptacji", "status_symbol" => "fa-clipboard-question"],
            ["id" => 6, "status_name" => "wycena zakwestionowana", "status_symbol" => "fa-delete-left"],
            ["id" => 7, "status_name" => "wygasłe", "status_symbol" => "fa-hourglass-end"],
            ["id" => 8, "status_name" => "wycena odrzucona", "status_symbol" => "fa-fire"],
            ["id" => 9, "status_name" => "przyjęte", "status_symbol" => "fa-clipboard-check"],
            /* statusy questów */
            ["id" => 11, "status_name" => "nowe", "status_symbol" => "fa-cart-flatbed"],
            ["id" => 12, "status_name" => "prace w toku", "status_symbol" => "fa-person-digging"],
            ["id" => 13, "status_name" => "prace przerwane", "status_symbol" => "fa-pause"],
            ["id" => 14, "status_name" => "póki co zaakceptowane", "status_symbol" => "fa-check"],
            ["id" => 15, "status_name" => "czeka na recenzję", "status_symbol" => "fa-truck-ramp-box"],
            ["id" => 16, "status_name" => "oddane do poprawki", "status_symbol" => "fa-people-pulling"],
            ["id" => 17, "status_name" => "wygasłe", "status_symbol" => "fa-wind"],
            ["id" => 18, "status_name" => "odrzucone", "status_symbol" => "fa-dumpster-fire"],
            ["id" => 19, "status_name" => "zaakceptowane", "status_symbol" => "fa-check-double"],
            ["id" => 21, "status_name" => "zaproponowano zmiany", "status_symbol" => "fa-hand-point-up"],
            ["id" => 26, "status_name" => "oddane po zamknięciu", "status_symbol" => "fa-recycle"],
            /* statusy techniczne */
            ["id" => 31, "status_name" => "zmieniono wycenę", "status_symbol" => "fa-magnifying-glass-dollar"],
            ["id" => 32, "status_name" => "dokonano wpłaty", "status_symbol" => "fa-cash-register"],
            ["id" => 33, "status_name" => "przypomnienie o wpłacie", "status_symbol" => "fa-comment-dollar"],
            ["id" => 34, "status_name" => "dokonano zwrotu wpłaty", "status_symbol" => "fa-hand-holding-dollar"],
            ["id" => 95, "status_name" => "czeka na doprecyzowanie", "status_symbol" => "fa-reply"],
            ["id" => 96, "status_name" => "doprecyzowane", "status_symbol" => "fa-reply-all"],
            /* statusy pracy nad questem */
            ["id" => 100, "status_name" => "wstępna obróbka", "status_symbol" => "🔍"],
            ["id" => 101, "status_name" => "nagr: perkusja", "status_symbol" => "🟦"],
            ["id" => 102, "status_name" => "nagr: gitary", "status_symbol" => "🟥"],
            ["id" => 103, "status_name" => "nagr: fortepiany", "status_symbol" => "🟧"],
            ["id" => 104, "status_name" => "nagr: syntezatory", "status_symbol" => "⬛"],
            ["id" => 105, "status_name" => "nagr: dęte", "status_symbol" => "🟩"],
            ["id" => 106, "status_name" => "nagr: smyczki", "status_symbol" => "🟫"],
            ["id" => 107, "status_name" => "nagr: wokale", "status_symbol" => "🟪"],
            ["id" => 108, "status_name" => "inne", "status_symbol" => "🌊"],
            ["id" => 109, "status_name" => "mix i mastering", "status_symbol" => "🎛"],
            ["id" => 110, "status_name" => "pisanie nut", "status_symbol" => "🎵"],
            ["id" => 111, "status_name" => "przygotowanie filmu", "status_symbol" => "🎬"],
            ["id" => 112, "status_name" => "nagr: basy", "status_symbol" => "🟨"],
        ] as $s) {
            if (Status::find($s["id"])) continue;
            Status::create($s);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
