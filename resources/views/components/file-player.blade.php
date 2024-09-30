@props([
    "songId",
    "file",
    "type",
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

    <audio oncanplaythrough="enableFilePlayer('{{ basename($file) }}')">
        <source src="{{ route('safe-show', ["id" => $songId, "filename" => basename($file)]) }}"
            type="audio/{{ $type == "mp3" ? "mpeg" : $type }}"
        />
    </audio>
</div>
