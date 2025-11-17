@props([
    "status",
    "pop" => null,
    "withName" => false,
])

@php
if ($pop !== false) $pop = $status->status_name;
@endphp

<span style="color: {{ $status->color }};"
    {{ $pop ? Popper::pop($pop) : null }}
>
    <x-shipyard.app.icon :name="$status->icon" />

    @if ($withName) {{ $status->status_name }} @endif
</span>
