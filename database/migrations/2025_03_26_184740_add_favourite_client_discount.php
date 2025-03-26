<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddFavouriteClientDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // price level
        DB::table("prices")->insert([
            [
                "service" => "zniżka dla ulubionego klienta",
                "indicator" => "!",
                "operation" => "*",
                "price_a" => -0.25,
                "price_b" => -0.25,
            ],
        ]);
        DB::table("prices")->where("indicator", "#")->update(["price_a" => -0.5, "price_b" => -0.5]);

        // favourite client
        Schema::table("users", function (Blueprint $table) {
            $table->boolean("is_forgotten")->nullable()->default(false);
        });
        User::where("trust", 2)->update(["trust" => 0, "is_forgotten" => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("prices")->where("indicator", "!")->delete();
        DB::table("prices")->where("indicator", "#")->update(["price_a" => -0.75, "price_b" => -0.75]);

        User::where("is_forgotten", true)->update(["trust" => 2]);
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn("is_forgotten");
        });
    }
}
