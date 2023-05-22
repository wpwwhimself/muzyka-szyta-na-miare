@extends('layouts.app', compact("title"))

@section('content')
{{-- <section id="showcases-stats" class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-chart-pie"></i> Statystyki reklam
            <small class="ghost">Łącznie {{ $showcases_count }}</small>
        </h1>
    </div>
</section> --}}

<section id="add-showcase">
    <div class="section-header">
        <h1><i class="fa-solid fa-add"></i> Dodaj reklamę</h1>
    </div>

    <div id="quick-add-showcase" class="flex-right">
    @foreach ($potential_showcases as $song)
        <x-button action="#/" :small="true"
            label="{!! $song->title !!} ({{ $song->artist }})"
            icon="{{ substr(song_quest_type($song->id)->fa_symbol, 3) }}"
            value="{{ $song->id }}"
            />
    @endforeach
    </div>
    <script>
    $(document).ready(function(){
        $("#quick-add-showcase a").click(function(){
            $("select[name='song_id']").val($(this).attr("value"));
        });
    });
    </script>

    <form action="{{ route('add-showcase') }}" class="flex-right" method="post">
        @csrf
        <x-select name="song_id" label="Utwór" :options="$songs" :small="true" />
        <x-input type="text" name="link_fb" label="Embed Facebooka" :small="true" />
        <x-input type="text" name="link_ig" label="Embed Instagrama" :small="true" />
        <x-button action="submit" label="Dodaj" icon="plus" :small="true" />
    </form>
</section>

<section id="showcases-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-list"></i> Lista reklam</h1>
    </div>
    <style>
    .table-row{ grid-template-columns: 1fr 1fr 1fr; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Tytuł<br>Wykonawca</span>
            <span>Facebook</span>
            <span>Instagram</span>
        </div>
        <hr />
        @forelse ($showcases as $showcase)
        <div class="table-row">
            <a href="{{ route('songs', ['search' => $showcase->song_id]) }}">
                <h3 class="song-title">{{ $showcase->song->title ?? "bez tytułu" }}</h3>
                <p class="song-artist">{{ $showcase->song->artist }}</p>
            </a>
            {!! $showcase->link_fb ?? "<span></span>" !!}
            {!! $showcase->link_ig ?? "<span></span>" !!}
        </div>
        @empty
        <p class="grayed-out">Nie ma żadnych reklam</p>
        @endforelse
    </div>
    {{ $showcases->links() }}
</section>

@endsection
