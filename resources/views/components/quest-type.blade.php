@props([
  'type' => null,
  'id' => null, 'label' => null, 'faSymbol' => null,
  'small' => false,
])

@php
if ($type) [$id, $label, $faSymbol] = [$type->id, $type->type, $type->fa_symbol];
@endphp

<i class="quest-type {{ $small ? 'small' : '' }} fa-solid {{ $faSymbol }}" {{ Popper::pop($label) }}></i>
