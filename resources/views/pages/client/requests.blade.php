@extends('layouts.app')
@section("title", "Zapytania")

@section('content')

<x-section id="requests-list"
    title="Lista zapytań"
    :icon="model_icon('requests')"
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
        <x-shipyard.ui.button
            label="Złóż zapytanie o podkład/nuty"
            icon="send"
            action="none"
            onclick="openModal('send-podklady-request', {
                client_id: {{ Auth::user()?->id ?? 'null' }},
                client_name: '{{ Auth::user()?->notes?->client_name }}' || null,
                email: '{{ Auth::user()?->notes?->email }}' || null,
                phone: '{{ Auth::user()?->notes?->phone }}' || null,
                other_medium: '{{ Auth::user()?->notes?->other_medium }}' || null,
                contact_preference: '{{ Auth::user()?->notes?->contact_preference }}' || 'email',
            })"
            class="primary"
        />
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

    <div class="flex down">
        @forelse ($requests as $request)
        <x-requests.tile :request="$request" />
        @empty
        <p class="grayed-out">brak zapytań</p>
        @endforelse
    </div>

    {{ $requests->links("components.shipyard.pagination.default") }}
</x-section>

@endsection
