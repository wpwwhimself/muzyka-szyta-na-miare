@extends('layouts.app')
@section("title", "Zlecenia")

@section('content')

<x-section id="quests-list"
    title="Lista zleceń"
    :icon="model_icon('quests')"
>
    <x-slot name="buttons">
        <x-a :href="route('quests-calendar')" icon="calendar">Grafik</x-a>
        <x-a :href="route('add-request')" icon="plus">Dodaj nowe zapytanie</x-a>
    </x-slot>

    @forelse ($quests as $quest)
    <x-quests.tile :quest="$quest" />
    @empty
    <p class="grayed-out">brak zapytań</p>
    @endforelse

    {{ $quests->links("components.shipyard.pagination.default") }}
</x-section>

@endsection
