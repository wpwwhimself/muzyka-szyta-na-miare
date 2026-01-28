@props([
    "data",
])

@if (!$data->is_dj_ready)
<span class="accent error">
    <x-shipyard.app.icon name="alert" />
    Kompozycja nie posiada kompletu danych do loterii utworów.
</span>

@else
<div class="composition-preview">
    <x-shipyard.ui.abc-preview name="melody_preview" :value="$data->melody" />

    <div class="lyrics-table grid">
        @foreach (explode(" ", $data->songmap) as $part)
        @php
        $part_clean = preg_replace("/[^a-zA-Z0-9]/", "", $part);
        @endphp
        <strong class="part accent secondary">{{ $part }}</strong>
        <div class="lyrics">{!! $data->lyrics_pretty[$part_clean] ?? null !!}</div>
        @endforeach
    </div>
</div>

@endif
