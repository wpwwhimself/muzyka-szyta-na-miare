<?php

use App\Models\CostType;
use App\Models\IncomeType;
use App\Models\MoneyTransaction;
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
        MoneyTransaction::where([
            "typable_type" => IncomeType::class,
            "typable_id" => 1,
        ])
            ->where("amount", "<", 0)
            ->get()
            ->each(fn (MoneyTransaction $mt) => $mt->update([
                "typable_type" => CostType::class,
                "typable_id" => 6,
                "amount" => abs($mt->amount),
            ]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        MoneyTransaction::where([
            "typable_type" => CostType::class,
            "typable_id" => 6,
        ])->get()->each(fn (MoneyTransaction $mt) => $mt->update([
            "typable_type" => IncomeType::class,
            "typable_id" => 1,
            "amount" => -$mt->amount,
        ]));
    }
};
