@extends('layouts.app', compact("title"))

@section('content')
<section id="songs-stats" class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-chart-pie"></i> Statystyki utworów
            <small class="ghost">Łącznie {{ $songs_count }}</small>
        </h1>
    </div>
    {{-- TODO --}}
</section>

<section id="songs-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-list"></i> Lista utworów</h1>
    </div>
    <style>
    .table-row{ grid-template-columns: 2fr 1fr 2fr 1fr 1fr; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Tytuł<br>Wykonawca</span>
            <span>Gatunek</span>
            <span>Uwagi</span>
            <span>Wycena</span>
            <span @popper(Czas wykonania)>Czas wyk.</span>
        </div>
        <hr />
        @forelse ($songs as $song)
        <div id="song{{ $song->id }}" class="table-row">
            <span>
                <h3 class="song-title">{{ $song->title ?? "bez tytułu" }}</h3>
                <p class="song-artist">{{ $song->artist }}</p>
            </span>
            <span>
                {{ $song->genre->name }}
            </span>
            <span>
                {{ Illuminate\Mail\Markdown::parse($song->notes ?? "") }}
            </span>
            <span>
                {{ $song->price_code }}
            </span>
            <span @popper($song_work_times[$song->id]["parts"])>
                {{ $song_work_times[$song->id]["total"] }}
            </span>
        </div>
        @empty
        <p class="grayed-out">Nie ma żadnych utworów</p>
        @endforelse
    </div>
</section>

@endsection
