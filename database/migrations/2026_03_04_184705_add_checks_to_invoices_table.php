<?php

use App\Models\Shipyard\Modal;
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
            $table->boolean("is_check")->after("visible")->default(false);
        });

        Modal::where("name", "edit-invoice")->update(["fields" => [
            ["is_check", "checkbox", "Rachunek zamiast faktury", "invoice-list-outline", false],
            ["payer_name", "text", "Nazwa płatnika", "badge-account", true],
            ["payer_title", "text", "Tytuł płatnika", "badge-account-horizontal", false, ["hint" => "Szary tekst pod nazwą płatnika."]],
            ["payer_address", "TEXT", "Adres", "map-marker", true],
            ["payer_nip", "text", "NIP", "domain", false],
            ["payer_regon", "text", "REGON", "domain", false],
            ["payer_email", "email", "E-mail", "at", false],
            ["payer_phone", "tel", "Telefon", "phone", false],
            ["quests", "text", "Zlecenia", "package-variant-closed", true, ["hint" => "Oddzielone spacjami."]],
        ]]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn("is_check");
        });

        Modal::where("name", "edit-invoice")->update(["fields" => [
            ["payer_name", "text", "Nazwa płatnika", "badge-account", true],
            ["payer_title", "text", "Tytuł płatnika", "badge-account-horizontal", false, ["hint" => "Szary tekst pod nazwą płatnika."]],
            ["payer_address", "TEXT", "Adres", "map-marker", true],
            ["payer_nip", "text", "NIP", "domain", false],
            ["payer_regon", "text", "REGON", "domain", false],
            ["payer_email", "email", "E-mail", "at", false],
            ["payer_phone", "tel", "Telefon", "phone", false],
            ["quests", "text", "Zlecenia", "package-variant-closed", true, ["hint" => "Oddzielone spacjami."]],
        ]]);
    }
};
