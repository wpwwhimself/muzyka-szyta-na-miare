@props([
    "request"
])

<x-shipyard.app.model.tile :model="$request">
    <x-slot:actions>
        <div class="quest-status">
            <x-phase-indicator :status-id="$request->status_id" :small="true" />
        </div>

        <x-shipyard.ui.button
            :action="route('quest', ['id' => $request->id])"
            icon="arrow-right"
            pop="Szczegóły"
        />
    </x-slot:actions>
</x-shipyard.app.model.tile>
