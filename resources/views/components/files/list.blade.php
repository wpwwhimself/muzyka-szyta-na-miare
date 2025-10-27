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
    <div @class([
        'file-container-b',
        'ghost' => is_archmage() && !$version->exclusiveClients?->contains(App\Models\User::find($highlightForClientId)),
    ])
    >
        <div class="flex right middle spread nowrap">
            <span class="heading-wrapper flex right middle nowrap">
                @foreach ($version->tags as $tag)
                <x-file-tag :tag="$tag" />
                @endforeach

                @if ($version->transposition)
                <x-file-tag :transpose="$version->transposition" />
                @endif

                @if ($version->exclusiveClients && is_archmage())
                <div class="file-tag flex right center middle"
                    style="background-color: white;"
                    {{ Popper::pop("Widoczny dla: ".$version->exclusiveClients->pluck("notes.client_name")->join(", ")) }}
                >
                    <x-shipyard.app.icon name="eye" />
                </div>
                @endif

                <h5>
                    {{ $version->version_name }}
                    <small class="ghost" {{ Popper::pop($version->updated_at) }}>
                        {{ $version->updated_at->diffForHumans() }}
                    </small>
                </h5>
            </span>

            @if ($editable)
            <x-shipyard.ui.button
                icon="pencil"
                pop="Edytuj"
                :action="route('files-edit', ['id' => $version->id])"
                onclick="primeReload()"
                target="_blank"
            />
            @endif
        </div>

        <div class="ver_desc">
            {{ Illuminate\Mail\Markdown::parse($version->description ?? "") }}
        </div>

        @if ($version->missing_files)
        <div class="yellowed-out">
            <span class="accent warning">
                <x-shipyard.app.icon name="alert" />
            </span>
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
