@extends('layouts.app', compact("title"))

@section('content')
    @foreach (["success", "error"] as $status)
    @if (session($status))
        <x-alert :status="$status" />
    @endif
    @endforeach
        <section id="quests-list">
            <div class="section-header">
                <h1><i class="fa-solid fa-boxes-stacked"></i> Lista zleceń</h1>
                <div>
                    <a href="{{ route("add-quest") }}">Dodaj nowe <i class="fa-solid fa-plus"></i></a>
                </div>
            </div>
            <style>
            .table-row{ grid-template-columns: 3em 3fr 1fr 11em 2em; }
            .table-row span:nth-child(5){ text-align: center; }
            </style>
            <div class="quests-table">
                <div class="table-header table-row">
                    <span>Typ</span>
                    <span>Tytuł<br>Wykonawca</span>
                    <span>Klient</span>
                    <span><i class="fa-solid fa-traffic-light"></i> Status</span>
                    <span @popper(Czy opłacony)><i class="fa-solid fa-sack-dollar"></i></span>
                </div>
                <hr />
                @forelse ($quests as $quest)
                <a href="{{ route("quest", $quest->id) }}" class="table-row p-{{ $quest->status_id }}">
                    <span>
                        <x-quest-type
                            :id="song_quest_type($quest->song_id)->id ?? 0"
                            :label="song_quest_type($quest->song_id)->type ?? 'nie zdefiniowano'"
                            :fa-symbol="song_quest_type($quest->song_id)->fa_symbol ?? 'fa-circle-question'"
                            />
                    </span>
                    <span>
                        <h3 class="song-title">{{ $quest->song->title ?? "bez tytułu" }}</h3>
                        <span class="song-artist">{{ $quest->song->artist }}</span>
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
                    <span class="quest-status">
                        <x-phase-indicator :status-id="$quest->status_id" :small="true" />
                    </span>
                    <span>
                    @if (quest_paid($quest->id, $quest->price))
                    <i class="quest-paid fa-solid fa-check"></i>
                    @endif
                    </span>
                </a>
                @empty
                <p class="grayed-out">brak zapytań</p>
                @endforelse

            </div>
            {{ $quests->links() }}
        </section>
@endsection
