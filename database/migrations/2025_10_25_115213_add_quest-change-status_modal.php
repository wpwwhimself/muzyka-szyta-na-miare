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
        Modal::create([
            "name" => "quest-change-status",
            "visible" => 1,
            "heading" => "Zmień status zapytania",
            "target_route" => "mod-quest-back",
            "fields" => [
                [
                    "comment",
                    "TEXT",
                    "Komentarz (opcjonalnie)",
                    "text",
                    false,
                ],
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Modal::where("name", "quest-change-status")->delete();
    }
};
