@props([
    "songId" => null,
    "file",
    "type",
    "isShowcase" => false,
])

<div class="file-tile file-player" data-file-name="{{ basename($file) }}">
    <div class="container">
        <i class="fa-solid fa-circle-notch fa-spin"></i>
        <i class="hidden fa-solid fa-play"
            onclick="startFilePlayer('{{ basename($file) }}')">
        </i>
        <i class="hidden fa-solid fa-pause fa-fade"
            onclick="pauseFilePlayer('{{ basename($file) }}')">
        </i>
    </div>

    <div class="seeker hidden" style="--progress: 0%;"
        onclick="seekFilePlayer('{{ basename($file) }}', event)"
    >
    </div>

    <audio
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
