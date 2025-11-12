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
            "name" => "pay-for-quest",
            "visible" => 1,
            "heading" => "Dodaj wpłatę za zlecenie",
            "fields" => [
                [
                    "comment",
                    "number",
                    "Kwota",
                    "cash",
                    true,
                    [
                        "step" => 0.01,
                    ],
                ],
            ],
            "target_route" => "mod-quest-back",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Modal::where("name", "pay-for-quest")->delete();
    }
};
