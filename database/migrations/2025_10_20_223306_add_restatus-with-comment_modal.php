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
            "name" => "restatus-with-comment",
            "visible" => 2,
            "heading" => "Dodaj komentarz do zmiany statusu",
            "target_route" => "re_quests.restatus-with-comment",
            "fields" => [
                [
                    "comment",
                    "TEXT",
                    "Komentarz",
                    "note",
                    false,
                    null,
                ],
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Modal::where("name", "restatus-with-comment")->delete();
    }
};
