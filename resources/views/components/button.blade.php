@props([
    'label',
    'icon',
    'danger' => false,
    'action',
    "id" => null,
    'small' => false,
    "pop" => null,
])

<x-shipyard.ui.button
    :label="$label"
    :icon="$icon"
    :action="$action"
    :id="$id"
    :pop="$pop"
    class="{{ $danger ? 'danger' : '' }}"
/>
