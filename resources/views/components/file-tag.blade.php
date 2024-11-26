@props([
    "tag" => null,
    "transpose" => null,
])

<div class="file-tag flex-right center middle"
@if ($tag)

    style="background-color: {{ $tag->color }};"
    {{ Popper::pop($tag->name) }}
>
    @svg("mdi-".$tag->icon)

@elseif ($transpose)

    style="background-color: cyan;"
    {{ Popper::pop("Transpozycja") }}
>
    {{ $transpose }}

@endif
</div>