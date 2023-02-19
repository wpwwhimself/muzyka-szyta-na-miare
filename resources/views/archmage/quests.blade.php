@extends('layouts.app', compact("title"))

@section('content')
<section id="quests-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-boxes-stacked"></i> Lista zleceń</h1>
        <div>
            <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe zapytanie</x-a>
        </div>
    </div>
    <style>
    .table-row{ grid-template-columns: 3fr 1fr 5em 11em; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Piosenka</span>
            <span>Klient</span>
            <span>Wycena</span>
            <span><i class="fa-solid fa-traffic-light"></i> Status</span>
        </div>
        <hr />
        @forelse ($quests as $quest)
        <a href="{{ route('quest', $quest->id) }}" class="table-row p-{{ $quest->status_id }} {{ is_priority($quest->id) ? "priority" : "" }}">
            <span class="quest-main-data">
                <x-quest-type
                    :id="song_quest_type($quest->song_id)->id ?? 0"
                    :label="song_quest_type($quest->song_id)->type ?? 'nie zdefiniowano'"
                    :fa-symbol="song_quest_type($quest->song_id)->fa_symbol ?? 'fa-circle-question'"
                    />
                <span>
                    <h3 class="song-title">{{ $quest->song->title ?? "bez tytułu" }}</h3>
                    <span class="song-artist">{{ $quest->song->artist }}</span>
                    @if (is_priority($quest->id))
                    • <b>Priorytet</b>
                    @endif
                </span>
            </span>
            <span>
            @if ($quest->client?->client_name)
                @if (is_veteran($quest->client->id))
                <i class="fa-solid fa-user-shield" @popper(stały klient)></i> {{ $quest->client->client_name }}
                @else
                <i class="fa-solid fa-user" @popper(zwykły klient)></i> {{ $quest->client->client_name }}
                @endif
            @else
                <i class="fa-regular fa-user" @popper(nowy klient)></i> {{ $quest->client_name }}
            @endif
            </span>
            <span @unless($quest->paid) class='error' @endif>
                <span {{ Popper::pop($quest->price_code_override) }}>
                    {{ $quest->price }} zł
                </span>
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
