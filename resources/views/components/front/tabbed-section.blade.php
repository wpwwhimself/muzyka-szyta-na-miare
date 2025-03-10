@props([
    "title",
    "icon",
])

<x-section :title="$title" :icon="$icon" {{ $attributes }}>
    <x-slot name="buttons">
        {{ $buttons }}
    </x-slot>

    {{ $slot }}
</x-section>
