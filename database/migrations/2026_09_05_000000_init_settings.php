<?php

use App\Models\Setting;
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
            ["name" => "msznm_available_day_until", "type" => "text", "value" => "0,0,2,2,2,2,3"],
            ["name" => "msznm_available_days_needed", "type" => "number", "value" => "2"],
            ["name" => "msznm_current_pricing", "type" => "select", "value" => "C"],
            ["name" => "msznm_min_account_balance", "type" => "number", "value" => "6000"],
            ["name" => "msznm_pricing_B_since", "type" => "date", "value" => "2022-09-09"],
            ["name" => "msznm_pricing_C_since", "type" => "date", "value" => "2026-03-31"],
            ["name" => "msznm_quest_expired_after", "type" => "number", "value" => "30"],
            ["name" => "msznm_quest_minimal_price", "type" => "text", "value" => "45,100,10"],
            ["name" => "msznm_quest_reminder_time", "type" => "number", "value" => "4"],
            ["name" => "msznm_request_expired_after", "type" => "number", "value" => "7"],
            ["name" => "msznm_safe_old_enough", "type" => "number", "value" => "9999999"],
            ["name" => "msznm_veteran_from", "type" => "number", "value" => "6"],
            ["name" => "msznm_work_on_weekends", "type" => "checkbox", "value" => "1"],
        ] as $s) {
            if (Setting::where("name", $s["name"])->exists()) continue;
            Setting::insert($s);
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
