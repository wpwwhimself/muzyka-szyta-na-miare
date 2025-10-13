@extends('layouts.app', compact("title"))

@section('content')

<x-section id="requests-list"
    title="Lista zapytań"
    icon="envelope-open-text"
>
    <x-slot name="buttons">
        <x-tutorial>
            <p>
                To jest lista złożonych przez Ciebie zapytań.
                Każde zlecenie zaczyna się od złożenia zapytania, w którym trzeba zawrzeć, co będzie przedmiotem prac (podkład muzyczny, nuty itp.).
                Następnie ja przygotowuję wycenę, tj. wyznaczam cenę zlecenia oraz termin jego wykonania.
                Zaakceptowana wycena automatycznie sprawia, że zapytanie staje się nowym zleceniem.
            </p>
            <p>
                Zapytania mają na celu zebrać potrzebne informacje przed rozpoczęciem prac nad faktycznym zleceniem.
                Na liście poniżej znajdziesz nie tylko aktualne zapytania, ale też wcześniejsze.
            </p>
        </x-tutorial>

        @unless (Auth::user()->notes->trust == -1)
        <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe</x-a>
        @endunless
    </x-slot>

    @if (Auth::user()->notes->is_old)
    <p class="yellowed-out">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Bardzo prawdopodobnym jest, że poniższa lista jest niepełna.
        Część przeszłych zleceń została zarchiwizowana.
        Jeśli chcesz przywrócić któreś z nich, proszę o kontakt mailowy.
    </p>
    @endif

    @forelse ($requests as $request)
    <x-quest-mini :quest="$request" />
    @empty
    <p class="grayed-out">brak zapytań</p>
    @endforelse

    {{ $requests->links() }}
</x-section>

@endsection
