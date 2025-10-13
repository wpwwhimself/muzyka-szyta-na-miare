@extends('layouts.app', compact("title"))

@section('content')

<div>
    <x-button action="{{ route('cost-types') }}" label="Typy" icon="cog" />
    <x-button action="{{ route('finance') }}" label="Wróć" icon="angles-right" />
    <x-button action="{{ route('finance-summary') }}" label="Podsumowanie" icon="chart-column" />
</div>


<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-plus"></i>
            Dodaj koszt
        </h1>
    </div>
    <form action="{{ route('mod-cost') }}" method="post" class="flex right center">
        @csrf
        <x-input type="date" name="created_at" label="Data" small :value="date('Y-m-d')" />
        <x-select name="cost_type_id" label="Typ" :options="$types" />
        <x-input type="text" name="desc" label="Opis" :small="true" />
        <x-input type="number" name="amount" step="0.01" label="Wartość" />
        <input type="hidden" name="id" value="" />
        <x-button action="submit" icon="plus" label="Zatwierdź" />
    </form>
    <script defer>
    $(".table-row:not(.table-header)").click(function(){
        $("#created_at").val($(this).find(".cost-date").attr("data-date"));
        $("#cost_type_id").val($(this).find(".cost-type").attr("data-typ"));
        $("#desc").val($(this).find(".cost-desc").text());
        $("#amount").val($(this).find(".cost-amount").attr("data-amount"));
        $("input[name='id']").val($(this).attr("data-id"));
    });
    </script>
</section>

<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-money-bill-wave"></i>
            Zapisane koszty
        </h1>
    </div>

    <x-stats-highlight-h :data="$summary" :all-pln="true" />

    <div class="quests-table">
        <style>
        .table-row{ grid-template-columns: 1fr 1fr 2fr 1fr; }
        .table-row span:last-child{ text-align: right; }
        </style>
        <div class="table-header table-row">
            <span>Data</span>
            <span>Typ</span>
            <span>Opis</span>
            <span>Kwota</span>
        </div>
        @forelse ($costs as $cost)
        <div class="table-row clickable" data-id="{{ $cost->id }}">
            <span class="cost-date" data-date="{{ $cost->created_at->format('Y-m-d') }}" {{ Popper::pop($cost->created_at->format('d.m.Y')) }}>{{ $cost->created_at->diffForHumans() }}</span>
            <span class="cost-type" data-typ="{{ $cost->cost_type_id }}">{{ _ct_($cost->type->name) }}</span>
            <span class="cost-desc">{{ _ct_($cost->desc) }}</span>
            <span class="cost-amount" data-amount="{{ _c_($cost->amount) }}">{{ _c_(as_pln($cost->amount)) }}</span>
        </div>
        @empty
        <p class="grayed-out">Brak danych</p>
        @endforelse
    </div>
    {{ $costs->links() }}
</section>

@endsection
