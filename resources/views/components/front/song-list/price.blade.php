@props([
    "price",
])

<span class="ghost">
    <x-shipyard::app.icon name="cash" />
    ~{{ as_pln($price) }}
</span>
