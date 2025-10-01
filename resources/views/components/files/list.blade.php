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
    @unless (!$version->exclusiveClients->contains(Auth::user()) && !is_archmage())
    <div class="{{ implode(" ", array_filter([
        'file-container-b',
        is_archmage() && !$version->exclusiveClients?->contains(App\Models\User::find($highlightForClientId)) ? 'ghost' : null,
    ])) }}">
        <h5>
            @foreach ($version->tags as $tag)
            <x-file-tag :tag="$tag" />
            @endforeach
            @if ($version->transposition)
            <x-file-tag :transpose="$version->transposition" />
            @endif
            @if ($version->exclusiveClients && is_archmage())
            <div class="file-tag flex-right center middle"
                style="background-color: white;"
                {{ Popper::pop("Widoczny dla: ".$version->exclusiveClients->pluck("client_name")->join(", ")) }}
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

        @if (in_array("jpg", array_keys($version->file_paths)))
        <div class="flex down center">
            <x-button
                :action="route('safe-show', ['id' => $version->song_id, 'filename' => basename($version->file_paths['jpg'])])"
                target="_blank"
                label="Mapa utworu"
                icon="map"
                small
            />
        </div>
        @endif

        <div class="file-container-c">
        @foreach ($version->file_paths as $extension => $file)
            @switch ($extension)
                @case ("mp4")
                <video controls><source src="{{ $file }}" /></video>
                @break

                @case ("mp3")
                @case ("ogg")
                <x-file-player
                    :song-id="$version->song_id"
                    :file="$file"
                    :type="$extension"
                />
                @break

                @case ("pdf")
                    <span class="ghost">Nie jestem w stanie<br>pokazać podglądu</span>
                @break
            @endswitch
        @endforeach
        @foreach ($version->file_paths as $extension => $file)
            @continue (!$canDownloadFiles && $extension != "pdf")
            @continue ($extension == "jpg")
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
