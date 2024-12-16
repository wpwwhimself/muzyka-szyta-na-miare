<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MergeClientAndUserTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // add columns for users
        Schema::table('users', function (Blueprint $table) {
            $table->string("client_name");
            $table->string("email")->nullable();
            $table->string("phone", 20)->nullable();
            $table->string("other_medium")->nullable();
            $table->string("contact_preference")->default("email");
            $table->integer("trust")->default(0);
            $table->float("budget")->default(0);
            $table->text("default_wishes")->nullable();
            $table->text("special_prices")->nullable();
            $table->integer("helped_showcasing")->default(0)->comment("0: no, 1: pending, 2: yes");
            $table->integer("extra_exp")->default(0);
            $table->string("external_drive")->nullable();
        });

        // move data from clients to users
        DB::unprepared(<<<SQL
UPDATE users u
INNER JOIN clients c ON u.id = c.id
SET
    u.client_name = c.client_name,
    u.email = c.email,
    u.phone = c.phone,
    u.other_medium = c.other_medium,
    u.contact_preference = c.contact_preference,
    u.trust = c.trust,
    u.budget = c.budget,
    u.default_wishes = c.default_wishes,
    u.special_prices = c.special_prices,
    u.helped_showcasing = c.helped_showcasing,
    u.extra_exp = c.extra_exp,
    u.external_drive = c.external_drive;
SQL );

        // update constraints
        Schema::table("quests", function (Blueprint $table) {
            $table->dropForeign(["client_id"]);
            $table->foreign("client_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
        });
        Schema::table("requests", function (Blueprint $table) {
            $table->dropForeign(["client_id"]);
            $table->foreign("client_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
        });
        Schema::table("files", function (Blueprint $table) {
            $table->dropForeign(["only_for_client_id"]);
            $table->foreign("only_for_client_id")->references("id")->on("users")->onDelete("cascade")->onUpdate("cascade");
        });

        // remove clients table
        Schema::dropIfExists('clients');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // restore columns for clients
        Schema::create('clients', function (Blueprint $table) {
            $table->foreignId("id")->primary()->constrained("users");
            $table->string("client_name");
            $table->string("email")->nullable();
            $table->string("phone", 20)->nullable();
            $table->string("other_medium")->nullable();
            $table->string("contact_preference")->default("email");
            $table->integer("trust")->default(0);
            $table->float("budget")->default(0);
            $table->text("default_wishes")->nullable();
            $table->text("special_prices")->nullable();
            $table->integer("helped_showcasing")->default(0)->comment("0: no, 1: pending, 2: yes");
            $table->integer("extra_exp")->default(0);
            $table->string("external_drive")->nullable();
        });

        // move data from users to clients
        DB::unprepared(<<<SQL
INSERT INTO clients (
    id,
    client_name,
    email,
    phone,
    other_medium,
    contact_preference,
    trust,
    budget,
    default_wishes,
    special_prices,
    helped_showcasing,
    extra_exp,
    external_drive
)
SELECT
    u.id,
    u.client_name,
    u.email,
    u.phone,
    u.other_medium,
    u.contact_preference,
    u.trust,
    u.budget,
    u.default_wishes,
    u.special_prices,
    u.helped_showcasing,
    u.extra_exp,
    u.external_drive
FROM users u
WHERE u.id NOT IN (1, 0);
SQL );

        // update constraints
        Schema::table("quests", function (Blueprint $table) {
            $table->dropForeign(["client_id"]);
            $table->foreign("client_id")->references("id")->on("clients")->onDelete("cascade")->onUpdate("cascade");
        });
        Schema::table("requests", function (Blueprint $table) {
            $table->dropForeign(["client_id"]);
            $table->foreign("client_id")->references("id")->on("clients")->onDelete("cascade")->onUpdate("cascade");
        });
        Schema::table("files", function (Blueprint $table) {
            $table->dropForeign(["only_for_client_id"]);
            $table->foreign("only_for_client_id")->references("id")->on("clients")->onDelete("cascade")->onUpdate("cascade");
        });

        // remove columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                "client_name",
                "email",
                "phone",
                "other_medium",
                "contact_preference",
                "trust",
                "budget",
                "default_wishes",
                "special_prices",
                "helped_showcasing",
                "extra_exp",
                "external_drive",
            ]);
        });
    }
}
