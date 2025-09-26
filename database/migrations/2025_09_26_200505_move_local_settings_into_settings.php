<?php

use App\Models\Shipyard\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $types = [
            "available_day_until" => "text",
            "available_days_needed" => "number",
            "work_on_weekends" => "checkbox",

            "current_pricing" => "select",
            "pricing_B_since" => "date",
            "min_account_balance" => "number",
            "quest_minimal_price" => "text",

            "quest_expired_after" => "number",
            "quest_reminder_time" => "number",
            "request_expired_after" => "number",
            "safe_old_enough" => "number",

            "veteran_from" => "number",
        ];

        DB::table("local_settings")->get()->each(fn ($setting) =>
            Setting::updateOrCreate([
                "name" => "msznm_" . $setting->setting_name,
            ], [
                "name" => "msznm_" . $setting->setting_name,
                "type" => $types[$setting->setting_name],
                "value" => $setting->value_str,
            ])
        );

        Schema::dropIfExists("local_settings");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
