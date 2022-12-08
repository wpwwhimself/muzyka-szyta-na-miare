@props(['href', 'icon' => 'angles-right'])

<x-button
  action="{{ $href }}" {{ $attributes }} label="{{ $slot }}" icon="{{ $icon }}"
  />
