@extends('layouts.app', compact("title"))

@section('content')
    <section id="quests-list">
        <div class="section-header">
            <h1><i class="fa-solid fa-boxes-stacked"></i> Lista zleceń</h1>
            <div>
                <a href="{{ route("add-quest") }}">Dodaj nowe <i class="fa-solid fa-plus"></i></a>
            </div>
        </div>
        @if (count($quests))
            @foreach ($quests as $quest)
                <x-quest :quest="$quest" />
            @endforeach
        @else
            <p class="grayed-out">brak zleceń</p>
        @endif
    </section>
    <section id="requests-list">
        <div class="section-header">
            <h1><i class="fa-solid fa-envelope-open-text"></i> Lista zapytań</h1>
            <div>
                <a href="{{ route("add-request") }}">Dodaj nowe <i class="fa-solid fa-plus"></i></a>
            </div>
        </div>
        @if (count($requests))
            @foreach ($requests as $request)
                <x-request :quest="$request" />
            @endforeach
        @else
            <p class="grayed-out">brak zapytań</p>
        @endif
    </section>
@endsection
