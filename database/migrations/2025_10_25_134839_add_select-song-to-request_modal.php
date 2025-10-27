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
            "name" => "select-song-to-request",
        ], [
            "name" => "select-song-to-request",
            "visible" => 1,
            "heading" => "Przypisz utwór",
            "fields" => [
                [
                    "query",
                    "lookup",
                    "Wyszukaj",
                    "song-search",
                    false,
                    [
                        "selectData" => [
                            "dataRoute" => "lookup.songs",
                            "fieldName" => "song_id",
                        ]
                    ]
                ]
            ],
            "target_route" => "requests.select-song",
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Modal::where("name", "select-song-to-request")->delete();
    }
};
