@props([
  "title" => null,
  "noShrinking" => false,
])

@unless($slot == "" || empty($slot))
<div @class([
    "extendo-section",
    "flex-down",
    "center",
    "no-shrinking" => $noShrinking,
])>
    @if ($title) <span class="title grayed-out">{{ $title }}</span> @endif
    <span>{{ $slot }}</span>
</div>
@endunless
