@extends('layouts.app', compact("title"))

@section('content')
<section id="songs-list" class="flex-down spaced">
    <div class="section-header">
        <h1><i class="fa-solid fa-list"></i> Lista utworów</h1>
        <form method="get" id="search" class="flex-right" action="{{ route('songs') }}">
            <input type="text" name="search" class="small" value="{{ $search }}" />
            <x-button action="submit" icon="magnifying-glass" label="" :small="true" />
        </form>
    </div>
    {{-- <div class="quests-table"> --}}
        @forelse ($songs as $song)
        <x-extendo-block :key="$song->id"
            :header-icon="preg_replace('/fa-/', '', $song->type->fa_symbol)"
            :title="$song->title ?? 'bez tytułu'"
            :subtitle="$song->artist"
        >
            <x-extendo-section title="ID">{{ $song->id }}</x-extendo-section>
            <x-extendo-section title="Typ">{{ $song->type->type }}</x-extendo-section>
            <x-extendo-section title="Gatunek">{{ $song->genre->name }}</x-extendo-section>
            <x-extendo-section title="Kod wyceny">{!! $price_codes[$song->id] !!}</x-extendo-section>
            <x-extendo-section title="Linki"><x-link-interpreter :raw="$song->link" :editable="$song->id" /></x-extendo-section>
            <x-extendo-section title="Notatki">{{ $song->notes ? Illuminate\Mail\Markdown::parse($song->notes) : "" }}</x-extendo-section>
            <x-extendo-section title="Czas wykonania">
                <span {{ Popper::pop($song_work_times[$song->id]['parts']) }}>
                    {{ $song->work_time }}
                </span>
            </x-extendo-section>
            <x-extendo-section title="Zlecenia">
                @foreach ($song->quests as $quest)
                    <a href="{{ $quest->linkTo }}">{{ $quest->id }}</a>
                @endforeach
            </x-extendo-section>
        </x-extendo-block>
        @empty
        <p class="grayed-out">Nie ma żadnych utworów</p>
        @endforelse
    {{-- </div> --}}
    {{ $songs->links() }}
</section>

<script>
// editable songs //
$(document).ready(() => {
    $(".link-edit-trigger").click((e) => {
        const box = e.target.closest(".link-edit-trigger").nextElementSibling;
        box.classList.toggle("gone");
        box.querySelector("input[name=link]").focus();
    });

    $("input[name=link]").change((e) => {
        $.ajax({
            type: "post",
            url: "/song_link_change",
            data: {
                id: e.target.getAttribute("data-editable"),
                link: e.target.value,
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                window.location.reload();
            }
        });
    });
});
</script>

@endsection
