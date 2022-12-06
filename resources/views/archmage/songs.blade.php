@extends('layouts.app', compact("title"))

@section('content')
<section id="clients-stats" class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-chart-pie"></i> Statystyki utworów
        </h1>
    </div>
    {{-- TODO --}}
</section>

<section id="clients-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-list"></i> Lista utworów</h1>
    </div>
    <style>
    .table-row{ grid-template-columns: 4fr 4fr 8em 1fr 1fr; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Tytuł<br>Wykonawca</span>
            <span>Gatunek</span>
        </div>
        <hr />
        @forelse ($songs as $song)
        <div class="table-row">
            <span>
                <h3 class="song-title">{{ $song->title }}</h3>
                <p class="song-artist">{{ $song->artist }}</p>
            </span>
            <span>
                {{ $song->genre_id }}
            </span>
        </div>
        @empty
        <p class="grayed-out">Nie ma żadnych utworów</p>
        @endforelse
    </div>
</section>

@endsection
