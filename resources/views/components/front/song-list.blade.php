@props([
    "songs",
])

<ul>
    @foreach ($songs as $song)
    <li
        data-song-genre="{{ $song->genre_id }}"
        data-song-tags="{{ $song->tags?->pluck("id")->join(",") }}"
    >
        <span>{{ $song->title ?? 'utwór bez tytułu' }}</span>
        <span class="ghost">{{ $song->artist ?? '' }}</span>

        @if ($song->has_showcase_file)
        <span {{ Popper::pop("Posłuchaj próbki mojego wykonania") }}
            class="interactive accent primary"
            onclick="openSongDemo(
                `{{ $song->id }}`,
                `{{ $song->full_title }}`,
                `{{ Str::of($song->notes ?? '')->replace('\n', '<br>') || '' }}`
            )"
        >
            <x-shipyard.app.icon :name="model_icon('songs')" />
        </span>
        @endif
    </li>
    @endforeach
</ul>
