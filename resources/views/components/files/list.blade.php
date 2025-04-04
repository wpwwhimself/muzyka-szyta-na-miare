@props([
    "groupedFiles",
    "editable" => false,
    "highlightForClientId" => null,
    "canDownloadFiles",
])

@forelse ($groupedFiles as $variant_name => $versions)
<div class="file-container-a">
    <h4>
        <small>wariant:</small>
        {{ $variant_name }}
    </h4>

    @foreach ($versions as $version)
    @unless ($version->only_for_client_id && Auth::id() != $version->only_for_client_id && !is_archmage())
    <div class="{{ implode(" ", array_filter([
        'file-container-b',
        !in_array($version->only_for_client_id, [null, Auth::id(), $highlightForClientId]) ? 'ghost' : null,
    ])) }}">
        <h5>
            @foreach ($version->tags as $tag)
            <x-file-tag :tag="$tag" />
            @endforeach
            @if ($version->transposition)
            <x-file-tag :transpose="$version->transposition" />
            @endif
            @if ($version->exclusiveClient && is_archmage())
            <div class="file-tag flex-right center middle"
                style="background-color: white;"
                {{ Popper::pop("Tylko dla: ".$version->exclusiveClient->client_name) }}
            >
                @svg("mdi-eye")
            </div>
            @endif

            {{ $version->version_name }}

            <small class="ghost" {{ Popper::pop($version->updated_at) }}>
                {{ $version->updated_at->diffForHumans() }}
            </small>

            @if ($editable)
            <x-a :href="route('files-edit', ['id' => $version->id])" icon="pen" target="_blank" onclick="primeReload()"></x-a>
            @endif
        </h5>

        <div class="ver_desc">
            {{ Illuminate\Mail\Markdown::parse($version->description ?? "") }}
        </div>

        @if ($version->missing_files)
        <div class="yellowed-out">
            <i class="fas fa-triangle-exclamation fa-fade warning"></i>
            Sejf jest niekompletny. Napisz do mnie, żeby dodać pliki.
        </div>
        @endif

        <div class="file-container-c">
        @foreach ($version->file_paths as $extension => $file)
            @if ($extension == "mp4")
            <video controls><source src="{{ $file }}" /></video>
                @break
            @elseif (in_array($extension, ["mp3", "ogg"]))
            <x-file-player
                :song-id="$version->song_id"
                :file="$file"
                :type="$extension"
            />
                @break
            @elseif ($extension == "pdf")
            <span class="ghost">Nie jestem w stanie<br>pokazać podglądu</span>
            @endif
        @endforeach
        @foreach ($version->file_paths as $extension => $file)
            @continue (!$canDownloadFiles && $extension != "pdf")
            <x-file-tile :id="$version->song_id" :file="$file" />
        @endforeach
        </div>
    </div>
    @endunless
    @endforeach
</div>
@empty
<p class="grayed-out">Brak plików</p>
@endforelse
