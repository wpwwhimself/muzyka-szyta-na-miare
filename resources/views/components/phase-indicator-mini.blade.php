@props([
    "status"
])

<span class="p-{{ $status->id }}"
    style="color: rgb(var(--q-clr));"
    {{ Popper::pop($status->status_name) }}
>
    <x-shipyard.app.icon :name="$status->icon" />
</span>
