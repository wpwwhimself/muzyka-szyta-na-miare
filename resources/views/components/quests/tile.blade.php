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

        <div class="quest-status">
            <x-phase-indicator :status-id="$quest->status_id" :small="true" />
        </div>

        <x-shipyard.ui.button
            :action="route('quest', ['id' => $quest->id])"
            icon="arrow-right"
            pop="Szczegóły"
        />
    </x-slot:actions>
</x-shipyard.app.model.tile>
