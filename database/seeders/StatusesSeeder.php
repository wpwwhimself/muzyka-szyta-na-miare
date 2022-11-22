<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("statuses")->insert([
            /* statusy requestów */
            ["id" => 1, "status_name" => "nowe", "status_symbol" => "fa-star"],
            ["id" => 5, "status_name" => "wycena do akceptacji", "status_symbol" => "fa-clipboard-question"],
            ["id" => 6, "status_name" => "wycena zakwestionowana", "status_symbol" => "fa-delete-left"],
            ["id" => 7, "status_name" => "nie podejmę się", "status_symbol" => "fa-trash"],
            ["id" => 8, "status_name" => "wycena odrzucona", "status_symbol" => "fa-fire"],
            ["id" => 9, "status_name" => "przyjęte", "status_symbol" => "fa-clipboard-check"],
            /* statusy questów */
            ["id" => 11, "status_name" => "nowe", "status_symbol" => "fa-cart-flatbed"],
            ["id" => 12, "status_name" => "prace w toku", "status_symbol" => "fa-person-digging"],
            ["id" => 13, "status_name" => "prace przerwane", "status_symbol" => "fa-pause"],
            ["id" => 15, "status_name" => "czeka na recenzję", "status_symbol" => "fa-truck-ramp-box"],
            ["id" => 16, "status_name" => "oddane do poprawki", "status_symbol" => "fa-people-pulling"],
            ["id" => 18, "status_name" => "odrzucone", "status_symbol" => "fa-dumpster-fire"],
            ["id" => 19, "status_name" => "zaakceptowane", "status_symbol" => "fa-check"],
            ["id" => 26, "status_name" => "oddane do poprawki po akceptacji", "status_symbol" => "fa-recycle"],
            /* statusy techniczne */
            ["id" => 31, "status_name" => "zmieniono wycenę", "status_symbol" => "fa-comment-dollar"],
            ["id" => 32, "status_name" => "dokonano wpłaty", "status_symbol" => "fa-cash-register"],
            /* statusy pracy nad questem */
            ["id" => 100, "status_name" => "analiza i wstępna obróbka", "status_symbol" => "🔍"],
            ["id" => 101, "status_name" => "nagrania: perkusja", "status_symbol" => "🥁"],
            ["id" => 102, "status_name" => "nagrania: gitary", "status_symbol" => "🎸"],
            ["id" => 103, "status_name" => "nagrania: fortepiany", "status_symbol" => "🎹"],
            ["id" => 104, "status_name" => "nagrania: syntezatory", "status_symbol" => "⚡"],
            ["id" => 105, "status_name" => "nagrania: dęte", "status_symbol" => "🎺"],
            ["id" => 106, "status_name" => "nagrania: smyczki", "status_symbol" => "🎻"],
            ["id" => 107, "status_name" => "nagrania: wokale", "status_symbol" => "🎙"],
            ["id" => 108, "status_name" => "nagrania: inne", "status_symbol" => "🌊"],
            ["id" => 109, "status_name" => "mix i mastering", "status_symbol" => "🎛"],
            ["id" => 110, "status_name" => "pisanie nut", "status_symbol" => "🎵"],
            ["id" => 111, "status_name" => "przygotowanie filmu", "status_symbol" => "🎬"]
        ]);
    }
}
