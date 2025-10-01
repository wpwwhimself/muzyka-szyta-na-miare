@props([
    'type', 'name', 'label',
    'autofocus' => false,
    'required' => false,
    "disabled" => false,
    "hint" => null,
    "value" => null,
    "small" => false,
    "links" => false,
])

<x-shipyard.ui.input
    :type="$type"
    :name="$name"
    :label="$label"
    :autofocus="$autofocus"
    :required="$required"
    :disabled="$disabled"
    :hint="$hint"
    :value="$value"
    :small="$small"
    :links="$links"
/>
