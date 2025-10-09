<?php

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
        Schema::table("statuses", function (Blueprint $table) {
            $table->string("icon")->nullable();
        });

        $icons = [
            "1" => "star",
            "4" => "delete",
            "5" => "clipboard-arrow-up",
            "6" => "clipboard-alert",
            "7" => "timer-sand-complete",
            "8" => "clipboard-remove",
            "9" => "clipboard-check",
            "11" => "package-variant-plus",
            "12" => "package-variant",
            "13" => "pause-box-outline",
            "14" => "check",
            "15" => "chat-question",
            "16" => "chat-alert",
            "17" => "timer-sand-complete",
            "18" => "package-variant-closed-remove",
            "19" => "check-all",
            "21" => "account-alert",
            "26" => "recycle",
            "31" => "cash-edit",
            "32" => "account-cash",
            "33" => "reminder",
            "34" => "cash-refund",
            "95" => "reply",
            "96" => "reply-all",
        ];
        foreach ($icons as $id => $icon_name) {
            Status::find($id)->update(["icon" => $icon_name]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("statuses", function (Blueprint $table) {
            $table->dropColumn("icon");
        });
    }
};
