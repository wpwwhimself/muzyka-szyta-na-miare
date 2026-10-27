@props([
    "tag" => null,
    "transpose" => null,
])

@php
if ($tag === "needs_replacing") {
    $tag = new stdClass();
    $tag->name = "Wymaga poprawy";
    $tag->icon = "glass-fragile";
    $tag->color = "#990808";
}
@endphp

<div class="file-tag flex right center middle" {{ $attributes }}
@if ($tag)
    style="background-color: {{ $tag->color }};"
    {{ Popper::pop($tag->name) }}
>
    <x-shipyard::app.icon :name="$tag->icon" />

@elseif ($transpose !== null)
    style="background-color: cyan;"
    {{ Popper::pop("Transpozycja") }}
>
    {{ $transpose > 0 ? "+" : "" }}{{ $transpose }}

@endif
</div>
