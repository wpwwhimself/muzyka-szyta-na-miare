<x-shipyard.app.model.tile :model="$quest">
    <x-slot:actions>
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
