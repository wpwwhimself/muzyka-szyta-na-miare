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
        Modal::updateOrCreate([
            "name" => "select-user-to-request",
        ], [
            "name" => "select-user-to-request",
            "visible" => 1,
            "heading" => "Przypisz klienta",
            "fields" => [
                [
                    "query",
                    "lookup",
                    "Wyszukaj",
                    "account-search",
                    false,
                    [
                        "selectData" => [
                            "dataRoute" => "lookup.users",
                            "fieldName" => "client_id",
                        ]
                    ]
                ]
            ],
            "target_route" => "requests.select-user",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Modal::where("name", "select-user-to-request")->delete();
    }
};
