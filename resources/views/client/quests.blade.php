@extends('layouts.app', compact("title"))

@section('content')
<div class="tutorial">
    <p>
        <i class="fa-solid fa-circle-question"></i>
        To jest lista wykonywanych dla Ciebie zleceń.
        Zleceniem jest każda usługa, jaką dla Ciebie wykonuję – podkład muzyczny, nuty itp.
        Nowe zlecenia powstają w wyniku akceptacji warunków przedstawionych w zapytaniu.
    </p>
    <p>
        Na liście poniżej znajdziesz nie tylko aktualne zlecenia, ale też wcześniejsze.
    </p>
</div>
<section id="quests-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-boxes-stacked"></i> Lista zleceń</h1>
        <div>
            @unless (Auth::user()->client->trust == -1)
            <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe zapytanie</x-a>
            @endunless
        </div>
    </div>
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
                    :id="song_quest_type($quest->song_id)->id ?? 0"
                    :label="song_quest_type($quest->song_id)->type ?? 'nie zdefiniowano'"
                    :fa-symbol="song_quest_type($quest->song_id)->fa_symbol ?? 'fa-circle-question'"
                    />
                <span>
                    <h3 class="song-title">{{ $quest->song->title ?? "bez tytułu" }}</h3>
                    <span class="song-artist">{{ $quest->song->artist }}</span>
                </span>
            </span>
            <span>
            @if ($quest->paid)
            <i class="quest-paid fa-solid fa-circle-dollar-to-slot"></i>
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
