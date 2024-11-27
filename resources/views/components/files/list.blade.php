@props([
    "groupedFiles",
    "editable" => false,
])

@forelse ($groupedFiles as $variant_name => $versions)
<div class="file-container-a">
    <h4>
        <small>wariant:</small>
        {{ $variant_name }}
    </h4>
    
    @foreach ($versions as $version)
    <div class="file-container-b">
        <h5>
            @foreach ($version->tags as $tag)
            <x-file-tag :tag="$tag" />
            @endforeach
            @if ($version->transposition)
            <x-file-tag :transpose="$version->transposition" />
            @endif
            {{ $version->version_name }}
            <small class="ghost" {{ Popper::pop($version->updated_at) }}>
                {{ $version->updated_at->diffForHumans() }}
            </small>
        </h5>
        
        <div class="ver_desc">
            {{ Illuminate\Mail\Markdown::parse($version->description ?? "") }}
        </div>

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
            <x-file-tile :id="$version->song_id" :file="$file" />
        @endforeach
        </div>
    </div>
    @endforeach
</div>
@empty
<p class="grayed-out">Brak plików</p>
@endforelse
