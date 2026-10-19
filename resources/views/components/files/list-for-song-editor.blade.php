@props([
    "data",
])

<div class="flex right center middl">
    <x-shipyard::ui.button
        icon="refresh"
        pop="Odśwież"
        action="none"
        onclick="loadFileList(document.querySelector(`.files-container`).closest(`.files-container`).dataset.uuid);"
        class="tertiary"
    />
    <x-shipyard::ui.button
        icon="plus"
        pop="Wgraj"
        :action="route('files-upload-by-entity', ['entity_name' => 'song', 'id' => $data->id])"
        target="_blank"
    />
    <x-shipyard::ui.button
        icon="recycle"
        pop="Dodaj istniejące"
        :action="route('files-add-from-existing-safe', ['song_id' => $data->id])"
        target="_blank"
    />
</div>

<x-files.list :song-id="$data->id" :editable="true" />
