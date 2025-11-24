<?php

use App\Models\Shipyard\Modal;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Modal::updateOrCreate([
            "name" => "quest-quote-update",
        ], [
            "name" => "quest-quote-update",
            "visible" => 1,
            "heading" => "Zmień wycenę zlecenia",
            "target_route" => "quest-quote-update",
            "fields" => [
                [
                    "reason", // name
                    "text", // type,
                    "Zmiana z uwagi na:", // label,
                    "chat-question", // icon,
                    true, // required
                ],
                [
                    "price_code_override", // name
                    "text", // type,
                    "Kod nowej ceny", // label,
                    "barcode", // icon,
                    true, // required
                ],
                [
                    "deadline", // name
                    "date", // type,
                    "Nowy termin", // label,
                    "calendar", // icon,
                    false, // required
                ],
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Modal::where("name", "quest-quote-update")->delete();
    }
};
