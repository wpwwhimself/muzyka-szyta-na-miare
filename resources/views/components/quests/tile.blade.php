@props([
    "quest",
    "no" => null,
])

<x-shipyard.app.model.tile :model="$quest">
    <x-slot:actions>
        @if ($no)
        <x-shipyard.app.icon-label-value
            icon="sort-reverse-variant"
            label="Numer zlecenia w kolejce"
        >
            {{ $no }}
        </x-shipyard.app.icon-label-value>
        @endif

        <x-quests.edit-buttons :quest="$quest" />
    </x-slot:actions>
</x-shipyard.app.model.tile>
