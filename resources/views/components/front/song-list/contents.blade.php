@props([
    "songs",
    "for",
])

<ul id="{{ $for }}-song-list">
    @foreach ($songs as $song)
    @if ($song instanceof \App\Models\Composition)
    <li
        data-song-genre="{{ $song->songs->pluck("genre_id")->join(",") }}"
        data-song-tags="{{ $song->tags?->pluck("id")->join(",") }}"
    >
        <span>{{ $song->title ?? 'utwór bez tytułu' }}</span>
        <span class="ghost">{{ $song->composer ?? '' }}</span>

        <span {{ Popper::pop("Zobacz, jakie aranże tego utworu wykonałem") }}
            class="interactive accent primary"
            onclick="openCompositionDemos({{ $song->id }})"
        >
            <x-shipyard.app.icon :name="model_icon('compositions')" />
            {{ $song->songs->count() }}
        </span>
    </li>

    @elseif ($song instanceof \App\Models\DjSong)
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
                `{{ Str::of($song->notes ?? '')->replace('\n', '<br>') }}`
            )"
        >
            <x-shipyard.app.icon :name="model_icon('songs')" />
        </span>
        @endif
    </li>

    @endif
    @endforeach
</ul>
