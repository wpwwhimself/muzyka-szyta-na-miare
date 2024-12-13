@extends('layouts.app', compact("title"))

@section('content')

<x-section id="quests-list"
    title="Lista zleceń"
    icon="boxes-stacked"
>
    <x-slot name="buttons">
        <x-tutorial>
            <p>
                To jest lista wykonywanych dla Ciebie zleceń.
                Zleceniem jest każda usługa, jaką dla Ciebie wykonuję – podkład muzyczny, nuty itp.
                Nowe zlecenia powstają w wyniku akceptacji warunków przedstawionych w zapytaniu.
            </p>
            <p>
                Na liście poniżej znajdziesz nie tylko aktualne zlecenia, ale też wcześniejsze.
            </p>
        </x-tutorial>

        @unless (Auth::user()->client->trust == -1)
        <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe zapytanie</x-a>
        @endunless
    </x-slot>

    @if (Auth::user()->client->is_old)
    <p class="yellowed-out">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Bardzo prawdopodobnym jest, że poniższa lista jest niepełna.
        Część przeszłych zleceń została zarchiwizowana.
        Jeśli chcesz przywrócić któreś z nich, proszę o kontakt mailowy.
    </p>
    @endif

    @forelse ($quests as $quest)
    <x-quest-mini :quest="$quest" />
    @empty
    <p class="grayed-out">brak zapytań</p>
    @endforelse

    {{ $quests->links() }}
</x-section>

@endsection
