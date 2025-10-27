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
            "name" => "mod-cost",
            "visible" => 1,
            "heading" => "Edycja kosztu",
            "target_route" => "mod-cost",
            "fields" => [
                [
                    "created_at",
                    "date",
                    "Data",
                    "calendar",
                    true,
                ],
                [
                    "cost_type_id",
                    "select",
                    "Typ kosztu",
                    "cash-marker",
                    true,
                    [
                        "selectData" => [
                            "optionsFromScope" => [
                                "\App\Models\CostType",
                                "forAdminList",
                                "name",
                                "id",
                            ],
                        ],
                    ],
                ],
                [
                    "desc",
                    "TEXT",
                    "Opis",
                    "text",
                    true,
                ],
                [
                    "amount",
                    "number",
                    "Wartość",
                    "cash",
                    true,
                    [
                        "step" => "0.01",
                    ],
                ],
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Modal::where("name", "mod-cost")->delete();
    }
};
