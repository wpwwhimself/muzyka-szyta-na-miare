@extends('layouts.app', compact("title"))

@section('content')
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
<section id="quests-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-boxes-stacked"></i> Lista zleceń</h1>
        <div>
            @unless (Auth::user()->client->trust == -1)
            <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe zapytanie</x-a>
            @endunless
        </div>
    </div>

    @if (Auth::user()->client->is_old)
    <p class="yellowed-out">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Bardzo prawdopodobnym jest, że poniższa lista jest niepełna.
        Część przeszłych zleceń została zarchiwizowana.
        Jeśli chcesz przywrócić któreś z nich, proszę o kontakt mailowy.
    </p>
    @endif

    <style>
    .table-row{ grid-template-columns: 3fr 2em 11em; }
    .table-row span:nth-child(5){ text-align: center; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Piosenka</span>
            <span @popper(Czy opłacony)><i class="fa-solid fa-sack-dollar"></i></span>
            <span><i class="fa-solid fa-traffic-light"></i> Status</span>
        </div>
        <hr />
        @forelse ($quests as $quest)
        <a href="{{ route('quest', $quest->id) }}" class="table-row p-{{ $quest->status_id }}">
            <span class="quest-main-data">
                <x-quest-type
                    :id="$quest->song->type->id ?? 0"
                    :label="$quest->song->type->type ?? 'nie zdefiniowano'"
                    :fa-symbol="$quest->song->type->fa_symbol ?? 'fa-circle-question'"
                    />
                <span>
                    <h3 class="song-title">{{ $quest->song->title ?? "bez tytułu" }}</h3>
                    <span class="song-artist">{{ $quest->song->artist }}</span>
                </span>
            </span>
            <span>
            @if ($quest->paid)
            <i class="success fa-solid fa-circle-dollar-to-slot"></i>
            @endif
            </span>
            <span class="quest-status">
                <x-phase-indicator :status-id="$quest->status_id" :small="true" />
            </span>
        </a>
        @empty
        <p class="grayed-out">brak zapytań</p>
        @endforelse

    </div>
    {{ $quests->links() }}
</section>

@endsection
