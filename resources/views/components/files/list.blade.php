@props([
    "songId",
    "editable" => false,
    "highlightForClientId" => null,
    "canDownloadFiles" => false,
])

@php
$uuid = Str::uuid();
@endphp

<div class="files-container" data-uuid="{{ $uuid }}">
    <x-shipyard.app.loader />
    <div class="meta hidden"
        data-song-id="{{ $songId }}"
        data-who-am-i="{{ Auth::id() }}"
        data-can-download-files="{{ var_export($canDownloadFiles) }}"
        data-editable="{{ var_export($editable) }}"
        data-highlight-for-client-id="{{ $highlightForClientId ?? "null" }}"
    ></div>
    <div class="contents"></div>
</div>

<script>
loadFileList("{{ $uuid }}");
</script>
