@extends('layouts.app', compact("title"))

@section('content')
<section id="songs-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-list"></i> Lista utworów</h1>
    </div>
    <style>
    .table-row{ grid-template-columns: 2fr 1fr 2fr 1fr 1fr 1fr; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Tytuł<br>Wykonawca</span>
            <span>Gatunek</span>
            <span>Uwagi</span>
            <span>Wycena</span>
            <span @popper(Czas wykonania)>Czas wyk.</span>
            <span>Zlecenia</span>
        </div>
        <hr />
        @forelse ($songs as $song)
        <div id="song{{ $song->id }}" class="table-row">
            <span class="quest-main-data">
                <x-quest-type
                    :id="song_quest_type($song->id)->id ?? 0"
                    :label="song_quest_type($song->id)->type ?? 'nie zdefiniowano'"
                    :fa-symbol="song_quest_type($song->id)->fa_symbol ?? 'fa-circle-question'"
                    />
                <span>
                    <h3 class="song-title">{{ $song->title ?? "bez tytułu" }}</h3>
                    <span class="song-artist">{{ $song->artist }}</span>
                    <span class="ghost">{{ $song->id }}</span>
                </span>
            </span>
            <span>
                {{ $song->genre->name }}
            </span>
            <span>
                {{ Illuminate\Mail\Markdown::parse($song->notes ?? "") }}
            </span>
            <span>
                {!! $price_codes[$song->id] !!}
            </span>
            <span {{ Popper::pop($song_work_times[$song->id]["parts"]) }}>
                {{ $song_work_times[$song->id]["total"] }}
            </span>
            <span>
            @foreach ($song->quests as $quest)
                <a href="{{ route('quest', ['id' => $quest->id]) }}">
                    {{ $quest->id }}
                </a>
            @endforeach
            </span>
        </div>
        @empty
        <p class="grayed-out">Nie ma żadnych utworów</p>
        @endforelse
    </div>
</section>

@endsection
