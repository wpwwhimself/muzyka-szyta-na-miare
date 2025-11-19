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
            "name" => "add-gig-transaction",
            "visible" => 1,
            "heading" => "Dodaj wpłatę za granie",
            "target_route" => "gig-transaction.add",
            "fields" => [
                [
                    "typable_id",
                    "select",
                    "Rodzaj",
                    model_icon("quest-types"),
                    true,
                    [
                        "selectData" => [
                            "optionsFromScope" => [
                                "\App\Models\IncomeType",
                                "forGigs",
                                "name",
                                "id",
                            ],
                        ],
                    ],
                ],
                [
                    "date",
                    "date",
                    "Data",
                    model_field_icon("money-transactions", "date"),
                    true,
                ],
                [
                    "amount",
                    "number",
                    "Kwota",
                    model_field_icon("money-transactions", "amount"),
                    true,
                    [
                        "step" => 0.01,
                    ],
                ],
                [
                    "description",
                    "text",
                    "Opis",
                    model_field_icon("money-transactions", "description"),
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
        Modal::where("name", "add-gig-transaction")->delete();
    }
};
