@props([
    "pop" => null,
])

<i {{ $pop ? Popper::pop($pop) : "" }} {{ $attributes }}></i>
