<?php

use App\Models\Cost;
use App\Models\CostType;
use App\Models\IncomeType;
use App\Models\MoneyTransaction;
use App\Models\Quest;
use App\Models\StatusChange;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $income_type_quest = IncomeType::updateOrCreate(
            ["name" => "opłata zlecenia📦"],
            [
                "name" => "opłata zlecenia📦",
                "description" => "wpłaty kwoty zlecenia przez klienta",
            ],
        );
        $income_type_budget = IncomeType::updateOrCreate(
            ["name" => "budżet💰"],
            [
                "name" => "budżet💰",
                "description" => "wpłaty na poczet przyszłych zleceń",
            ],
        );

        Cost::all()->each(fn (Cost $cost) =>
            MoneyTransaction::create([
                "typable_type" => CostType::class,
                "typable_id" => $cost->cost_type_id,
                "date" => $cost->created_at,
                "amount" => $cost->amount,
                "description" => $cost->desc,
            ])
        );

        StatusChange::whereIn("new_status_id", [32, 34])
            ->get()
            ->each(fn (StatusChange $payment) =>
                MoneyTransaction::create([
                    "typable_type" => IncomeType::class,
                    "typable_id" => $payment->re_quest_id ? $income_type_quest->id : $income_type_budget->id,
                    "relatable_type" => $payment->re_quest_id ? Quest::class : User::class,
                    "relatable_id" => $payment->re_quest_id ?? $payment->changed_by,
                    "date" => $payment->date,
                    "amount" => $payment->comment,
                ])
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table("money_transactions")->truncate();
    }
};
