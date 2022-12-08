@props(['href', 'icon' => 'angles-right'])

<x-button
  action="{{ $href }}" {{ $attributes }} :small="true" label="{{ $slot }}" icon="{{ $icon }}"
  />
