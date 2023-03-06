@extends('layouts.app', compact("title"))

@section('content')

<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-plus"></i>
            Dodaj koszt
        </h1>
    </div>
    <form action="{{ route('mod-cost') }}" method="post">
        @csrf
        <x-select name="cost_type_id" label="Typ" :options="$types" />
        <x-input type="text" name="desc" label="Opis" :small="true" />
        <x-input type="number" name="amount" step="0.01" min="0" label="Wartość" />
        <input type="hidden" name="id" value="" />
        <x-button action="submit" icon="plus" label="Zatwierdź" />
    </form>
    <script>
        $(document).ready(function(){
            $(".table-row:not(.table-header)").click(function(){
                $("#cost_type_id").val($(this).find(".cost-type").attr("data-typ"));
                $("#desc").val($(this).find(".cost-desc").text());
                $("#amount").val($(this).find(".cost-amount").attr("data-amount"));
                $("input[name='id']").val($(this).attr("data-id"));
            });
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
            <span {{ Popper::pop($cost->created_at) }}>{{ $cost->created_at->diffForHumans() }}</span>
            <span class="cost-type" data-typ="{{ $cost->cost_type_id }}">{{ $cost->type->name }}</span>
            <span class="cost-desc">{{ $cost->desc }}</span>
            <span class="cost-amount" data-amount="{{ $cost->amount }}">{{ number_format($cost->amount, 2, ",", " ") }} zł</span>
        </div>
        @empty
        <p class="grayed-out">Brak danych</p>
        @endforelse
    </div>
    {{ $costs->links() }}
</section>

<div>
    <x-button action="{{ route('cost-types') }}" label="Typy" icon="cog" />
    <x-button action="{{ route('finance') }}" label="Wróć" icon="angles-right" />
</div>

@endsection
