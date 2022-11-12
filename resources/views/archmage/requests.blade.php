@extends('layouts.app', compact("title"))

@section('content')
    @foreach (["success", "error"] as $status)
    @if (session($status))
        <x-alert :status="$status" />
    @endif
    @endforeach
        <section id="requests-list">
            <div class="section-header">
                <h1><i class="fa-solid fa-envelope-open-text"></i> Lista zapytań</h1>
                <div>
                    <a href="{{ route("add-request") }}">Dodaj nowe <i class="fa-solid fa-plus"></i></a>
                </div>
            </div>
            <style>
            .table-row{ grid-template-columns: 3em 3fr 1fr 11em; }
            </style>
            <div class="quests-table">
                <div class="table-header table-row">
                    <span>Typ</span>
                    <span>Tytuł<br>Wykonawca</span>
                    <span>Klient</span>
                    <span><i class="fa-solid fa-traffic-light"></i> Status</span>
                </div>
                <hr />
                @forelse ($requests as $request)
                <a href="{{ route("request", $request->id) }}" class="table-row p-{{ $request->status_id }}">
                    <span>
                        <x-quest-type :id="$request->quest_type_id" :label="$request->quest_type->type" />
                    </span>
                    <span>
                        <h3 class="song-title">{{ $request->title }}</h3>
                        <span class="song-artist">{{ $request->artist }}</span>
                    </span>
                    <span>
                    @if ($request->client?->client_name)
                        @if (is_veteran($request->client->id))
                        <i class="fa-solid fa-user-shield" @popper(stały klient)></i> {{ $request->client->client_name }}
                        @else
                        <i class="fa-solid fa-user" @popper(zwykły klient)></i> {{ $request->client->client_name }}
                        @endif
                    @else
                        <i class="fa-regular fa-user" @popper(nowy klient)></i> {{ $request->client_name }}
                    @endif
                    </span>
                    <span class="quest-status">
                        <x-phase-indicator :status-id="$request->status_id" :small="true" />
                    </span>
                </a>
                @empty
                <p class="grayed-out">brak zapytań</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
