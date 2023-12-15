@props([
  "title",
  "noShrinking" => false,
])

@unless($slot == "" || empty($slot))
<div @class([
    "extendo-section",
    "flex-down",
    "center",
    "no-shrinking" => $noShrinking,
])>
    <span class="title grayed-out">{{ $title }}</span>
    <span>{{ $slot }}</span>
</div>
@endunless
