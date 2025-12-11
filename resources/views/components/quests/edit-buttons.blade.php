@props([
    "quest",
])

<div class="quest-status">
    <x-phase-indicator :status-id="$quest->status_id" :small="true" />
</div>

<div class="flex down but-mobile-right spread and-cover">
    <x-shipyard.ui.button
        :action="route('quest', ['id' => $quest->id])"
        icon="arrow-right"
        pop="Szczegóły"
    />
    <x-shipyard.ui.button
        :action="route('admin.model.edit', ['model' => 'quests', 'id' => $quest->id])"
        icon="pencil"
        pop="Edytuj"
        show-for="technical"
    />
</div>