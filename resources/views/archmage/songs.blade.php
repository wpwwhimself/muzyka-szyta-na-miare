@extends('layouts.app', compact("title"))

@section('content')
<section id="songs-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-list"></i> Lista utworów</h1>
        <form method="get" id="search" class="flex-right" action="{{ route('songs') }}">
            <input type="text" name="search" class="small" value="{{ $search }}" />
            <x-button action="submit" icon="magnifying-glass" label="" :small="true" />
        </form>
    </div>
    <div class="quests-table">
        @forelse ($songs as $song)
        <x-extendo-block :key="$song->id"
            type="song"
            :object="$song"
            extended="perma"
        />
        @empty
        <p class="grayed-out">Nie ma żadnych utworów</p>
        @endforelse
    </div>
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
