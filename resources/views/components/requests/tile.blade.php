@props([
    "request"
])

<x-shipyard.app.model.tile :model="$request">
    <x-slot:actions>
        <x-requests.edit-buttons :request="$request" />
    </x-slot:actions>
</x-shipyard.app.model.tile>
