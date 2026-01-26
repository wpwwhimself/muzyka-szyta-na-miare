@props([
    "song"
])

@php
$links = array_filter(
    explode(",", $song->link ?? ""),
    fn ($l) => $l !== null && filter_var($l, FILTER_VALIDATE_URL) && Str::contains($l, "youtu")
);
@endphp

@foreach ($links as $link)
<a class="interactive accent primary" target="_blank" href="{{ $link }}">
    <x-shipyard.app.icon name="open-in-new" />
</a>
@endforeach
