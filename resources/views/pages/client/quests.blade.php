@extends('layouts.app')
@section("title", "Zlecenia")

@section('content')

<x-section id="quests-list"
    title="Lista zleceń"
    :icon="model_icon('quests')"
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
        @forelse ($quests as $quest)
        <x-quests.tile :quest="$quest" />
        @empty
        <p class="grayed-out">brak zapytań</p>
        @endforelse
    </div>

    {{ $quests->links("components.shipyard.pagination.default") }}
</x-section>

@endsection
