@props([
    "quest",
])

<div class="quest-status">
    <x-phase-indicator :status-id="$quest->status_id" :small="true" />
</div>

<div class="flex down but-mobile-right spread and-cover">
    <x-shipyard::ui.button
        :action="route('quest', ['id' => $quest->id])"
        icon="arrow-right"
        :label="is_archmage() ? null : 'Szczegóły'"
        :pop="is_archmage() ? 'Szczegóły' : null"
    />
</div>
