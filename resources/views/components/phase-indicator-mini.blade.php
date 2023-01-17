@props([
    "status"
])

<i  class="fa-solid {{ $status->status_symbol }} p-{{ $status->id }}"
    style="color: rgb(var(--q-clr));"
    {{ Popper::pop($status->status_name) }}>
</i>
