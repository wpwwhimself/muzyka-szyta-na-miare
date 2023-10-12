@extends('layouts.app', compact("title"))

@section('content')
<section id="quests-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-boxes-stacked"></i> Lista zleceń</h1>
        <div>
            <x-a href="{{ route('quests-calendar') }}" icon="calendar">Grafik</x-a>
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
        <a href="{{ route('quest', $quest->id) }}" class="table-row p-{{ $quest->status_id }} {{ ($quest->is_priority) ? "priority" : "" }}">
            <span class="quest-main-data">
                <x-quest-type
                    :id="$quest->song->type->id ?? 0"
                    :label="$quest->song->type->type ?? 'nie zdefiniowano'"
                    :fa-symbol="$quest->song->type->fa_symbol ?? 'fa-circle-question'"
                    />
                <span>
                    <h3 class="song-title">{{ $quest->song->title ?? "bez tytułu" }}</h3>
                    <span class="song-artist">{{ $quest->song->artist }}</span>
                    @if ($quest->is_priority)
                    • <b>Priorytet</b>
                    @endif
                </span>
            </span>
            <span>
            @if ($quest->client?->client_name)
                @if ($quest->client->is_veteran)
                <i class="fa-solid fa-user-shield" @popper(stały klient)></i> {{ _ct_($quest->client->client_name) }}
                @else
                <i class="fa-solid fa-user" @popper(zwykły klient)></i> {{ _ct_($quest->client->client_name) }}
                @endif
            @else
                <i class="fa-regular fa-user" @popper(nowy klient)></i> {{ _ct_($quest->client_name) }}
            @endif
            </span>
            <span @unless($quest->paid) class='error' @endif>
                <span {{ Popper::pop($quest->price_code_override) }}>
                    {{ _c_(as_pln($quest->price)) }}
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
