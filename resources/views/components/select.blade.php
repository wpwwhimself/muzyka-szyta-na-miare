@props([
    'type', 'name', 'label',
    'autofocus' => false,
    'required' => false,
    "disabled" => false,
    'options',
    'emptyOption' => false,
    'value' => null,
    'small' => false
])

<x-shipyard.ui.input
    type="select"
    :name="$name"
    :label="$label"
    :autofocus="$autofocus"
    :required="$required"
    :disabled="$disabled"
    :options="collect($options)->map(fn ($v, $k) => ['label' => $k, 'value' => $v])"
    :select-data="['emptyOption' => $emptyOption]"
    :value="$value"
    :small="$small"
/>
