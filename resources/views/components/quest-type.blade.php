@props([
  'type' => null,
  'id' => null, 'label' => null, 'icon' => null,
  'small' => false,
])

@php
if ($type) [$id, $label, $icon] = [$type->id, $type->type, $type->icon];
@endphp

<span @class([
    "quest-type",
    "small" => $small,
])
    @if ($label) {{ Popper::pop($label) }} @endif
>
    <x-shipyard.app.icon :name="$icon" />
</span>
