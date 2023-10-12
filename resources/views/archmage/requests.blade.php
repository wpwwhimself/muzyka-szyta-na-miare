@extends('layouts.app', compact("title"))

@section('content')
<section id="requests-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-envelope-open-text"></i> Lista zapytań</h1>
        <div>
            <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe</x-a>
        </div>
    </div>
    <style>
    .table-row{ grid-template-columns: 3fr 1fr 11em; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Piosenka</span>
            <span>Klient</span>
            <span><i class="fa-solid fa-traffic-light"></i> Status</span>
        </div>
        <hr />
        @forelse ($requests as $request)
        <a href="{{ route('request', $request->id) }}" class="table-row p-{{ $request->status_id }}">
            <span class="quest-main-data">
                <x-quest-type
                    :id="$request->quest_type_id"
                    :label="$request->quest_type->type"
                    :fa-symbol="$request->quest_type->fa_symbol"
                    />
                <span>
                    <h3 class="song-title">{{ $request->title ?? "bez tytułu" }}</h3>
                    <span class="song-artist">{{ $request->artist }}</span>
                </span>
            </span>
            <span>
            @if ($request->client?->client_name)
                @if ($request->client->is_veteran)
                <i class="fa-solid fa-user-shield" @popper(stały klient)></i> {{ _ct_($request->client->client_name) }}
                @else
                <i class="fa-solid fa-user" @popper(zwykły klient)></i> {{ _ct_($request->client->client_name) }}
                @endif
            @else
                <i class="fa-regular fa-user" @popper(nowy klient)></i> {{ _ct_($request->client_name) }}
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
    {{ $requests->links() }}
</section>

@endsection
