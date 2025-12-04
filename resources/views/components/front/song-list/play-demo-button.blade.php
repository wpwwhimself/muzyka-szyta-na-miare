@props([
    "song",
])

<span class="interactive accent primary" onclick="startDemo('{{ $song->id }}')">
    <x-shipyard.app.icon :name="model_icon('songs')" />
</span>
