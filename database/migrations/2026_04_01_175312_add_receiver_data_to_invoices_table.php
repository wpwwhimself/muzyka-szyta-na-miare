<?php

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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string("receiver_name")->nullable()->before("ksef_number");
            $table->string("receiver_title")->nullable()->before("ksef_number");
            $table->text("receiver_address")->nullable()->before("ksef_number");
            $table->string("receiver_nip")->nullable()->before("ksef_number");
            $table->string("receiver_regon")->nullable()->before("ksef_number");
            $table->string("receiver_email")->nullable()->before("ksef_number");
            $table->string("receiver_phone")->nullable()->before("ksef_number");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                "receiver_name",
                "receiver_title",
                "receiver_address",
                "receiver_nip",
                "receiver_regon",
                "receiver_email",
                "receiver_phone",
            ]);
        });
    }
};
