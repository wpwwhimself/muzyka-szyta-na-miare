@extends('layouts.app', compact("title"))

@section('content')

<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-money-bill-wave"></i>
            Typy katalogowanych kosztów
        </h1>
    </div>

    <div class="quests-table">
        <style>
        .table-row{ grid-template-columns: 2em 1fr 2fr }
        </style>
        <div class="table-header table-row">
            <span>ID</span>
            <span>Nazwa</span>
            <span>Opis</span>
        </div>
        @forelse ($types as $type)
        <div class="table-row">
            <span>{{ $type->id }}</span>
            <span>{{ $type->name }}</span>
            <span>{{ $type->desc }}</span>
        </div>
        @empty
        <p class="grayed-out">Brak typów</p>
        @endforelse
    </div>
</section>

<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-plus"></i>
            Dodaj typ
        </h1>
    </div>
    <form action="{{ route('mod-cost-type') }}" method="post">
        @csrf
        <x-input type="text" name="name" label="Nazwa" />
        <x-input type="text" name="desc" label="Opis" :small="true" />
        <input type="hidden" name="id" value="" />
        <x-button action="submit" icon="plus" label="Zatwierdź" />
    </form>
    <script>
    $(document).ready(function(){
        $(".table-row:not(.table-header)").click(function(){
            $("#name").val($(this).find("span:nth-child(2)").text());
            $("#desc").val($(this).find("span:nth-child(3)").text());
            $("input[name='id']").val($(this).find("span:nth-child(1)").text());
        });
    });
    </script>
</section>

<div>
    <x-button action="{{ route('costs') }}" label="Koszty" icon="money-bill-wave" />
    <x-button action="{{ route('finance') }}" label="Wróć" icon="angles-right" />
</div>

@endsection
