@props([
    "request"
])

<div class="quest-status">
    <x-phase-indicator :status-id="$request->status_id" :small="true" />
</div>

<div class="flex down but-mobile-right spread and-cover">
    <x-shipyard.ui.button
        :action="route('request', ['id' => $request->id])"
        icon="arrow-right"
        :label="is_archmage() ? null : 'Szczegóły'"
        :pop="is_archmage() ? 'Szczegóły' : null"
    />
    <x-shipyard.ui.button
        :action="route('admin.model.edit', ['model' => 'requests', 'id' => $request->id])"
        icon="pencil"
        pop="Edytuj"
        show-for="technical"
    />
</div>
