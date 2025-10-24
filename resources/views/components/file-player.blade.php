@props([
    "songId" => null,
    "file",
    "type",
    "isShowcase" => false,
])

<div class="file-tile file-player" data-file-name="{{ basename($file) }}">
    <div class="container">
        <span role="btn">
            <x-shipyard.app.icon name="loading" />
        </span>
        <span role="btn" onclick="startFilePlayer('{{ basename($file) }}')" class="hidden interactive">
            <x-shipyard.app.icon name="play" />
        </span>
        <span role="btn" onclick="pauseFilePlayer('{{ basename($file) }}')" class="hidden interactive">
            <x-shipyard.app.icon name="pause" />
        </span>
    </div>

    <div class="seeker interactive hidden" style="--progress: 0%;"
        onclick="seekFilePlayer('{{ basename($file) }}', event)"
    >
    </div>

    <audio
        onloadstart="disableFilePlayer('{{ basename($file) }}')"
        onloadeddata="enableFilePlayer('{{ basename($file) }}')"
        ontimeupdate="updateSeeker('{{ basename($file) }}')"
        onended="pauseFilePlayer('{{ basename($file) }}')"

    >
        <source src="{{ $isShowcase
            ? basename($file)
            : route('safe-show', ["id" => $songId, "filename" => basename($file)]) }}"
            type="audio/{{ $type == "mp3" ? "mpeg" : $type }}"
        />
    </audio>
</div>
