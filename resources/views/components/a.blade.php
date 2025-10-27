@props([
    'href',
    'icon' => 'chevron-double-right',
])

<x-button
  :action="$href"
  :small="true"
  :label="$slot"
  :icon="$icon"
  {{ $attributes }}
/>
