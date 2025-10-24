@props([
    "tag" => null,
    "transpose" => null,
])

<div class="file-tag flex right center middle" {{ $attributes }}
@if ($tag)

    style="background-color: {{ $tag->color }};"
    {{ Popper::pop($tag->name) }}
>
    <x-shipyard.app.icon :name="$tag->icon" />

@elseif ($transpose)

    style="background-color: cyan;"
    {{ Popper::pop("Transpozycja") }}
>
    {{ $transpose > 0 ? "+" : "" }}{{ $transpose }}

@endif
</div>
