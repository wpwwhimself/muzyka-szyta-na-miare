@extends('layouts.app', compact("title"))

@section('content')

<x-section id="quests-list"
    title="Lista zleceń"
    icon="boxes-stacked"
>
    <x-slot name="buttons">
        <x-a :href="route('quests-calendar')" icon="calendar">Grafik</x-a>
        <x-a :href="route('add-request')" icon="plus">Dodaj nowe zapytanie</x-a>
    </x-slot>

    @forelse ($quests as $quest)
    <x-quest-mini :quest="$quest" />
    @empty
    <p class="grayed-out">brak zapytań</p>
    @endforelse

    {{ $quests->links() }}
</x-section>

@endsection
