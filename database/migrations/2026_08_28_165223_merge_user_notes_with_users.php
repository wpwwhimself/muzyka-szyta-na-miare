<?php

use App\Models\User;
use App\Models\UserNote;
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
        Schema::table("users", function (Blueprint $table) {
            $table->string("password_actual");
            $table->string("phone")->nullable();
            $table->string("other_medium")->nullable();
            $table->string("contact_preference")->default("email");
            $table->integer("trust")->default(0);
            $table->double("budget")->default(0);
            $table->text("default_wishes")->nullable();
            $table->text("special_prices")->nullable();
            $table->integer("helped_showcasing")->comment("0: no, 1: pending, 2: yes")->default(0);
            $table->integer("extra_exp")->default(0);
            $table->string("external_drive")->nullable();
            $table->boolean("is_forgotten")->default(0);
            $table->json("invoice_data")->nullable();
        });

        UserNote::all()->each(fn ($un) => User::find($un->user_id)->update([
            "display_name" => $un->client_name,
            "password_actual" => $un->password,
            "phone" => $un->phone,
            "other_medium" => $un->other_medium,
            "contact_preference" => $un->contact_preference,
            "trust" => $un->trust,
            "budget" => $un->budget,
            "default_wishes" => $un->default_wishes,
            "special_prices" => $un->special_prices,
            "helped_showcasing" => $un->helped_showcasing,
            "extra_exp" => $un->extra_exp,
            "external_drive" => $un->external_drive,
            "is_forgotten" => $un->is_forgotten,
            "invoice_data" => $un->invoice_data,
        ]));

        Schema::table("requests", function (Blueprint $table) {
            $table->dropForeign("requests_client_id_foreign");
            $table->foreign("client_id")->references("id")->on("users");
        });
        Schema::table("quests", function (Blueprint $table) {
            $table->dropForeign("quests_client_id_foreign");
            $table->foreign("client_id")->references("id")->on("users");
        });
        Schema::table("file_user", function (Blueprint $table) {
            $table->dropForeign("file_user_user_id_foreign");
            $table->foreign("user_id")->references("id")->on("users");
        });
        Schema::table("status_changes", function (Blueprint $table) {
            $table->dropForeign("status_changes_changed_by_foreign");
            $table->foreign("changed_by")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
