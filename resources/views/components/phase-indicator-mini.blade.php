@props([
    "status",
    "pop" => null,
    "withName" => false,
])

@php
if ($pop !== false) $pop = $status->status_name;
@endphp

<span class="p-{{ $status->id }}"
    style="color: rgb(var(--q-clr));"
    {{ $pop ? Popper::pop($pop) : null }}
>
    <x-shipyard.app.icon :name="$status->icon" />

    @if ($withName) {{ $status->status_name }} @endif
</span>
