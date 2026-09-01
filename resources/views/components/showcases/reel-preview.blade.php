@props([
    "data",
])

@if ($data->links)
<x-front.showcase-reels :showcases="[$data]" :preview-mode="true" />

@else
<span class="accent error">
    <x-shipyard::app.icon name="alert" />
    Brak rolek do wyświetlenia.
</span>

@endif
