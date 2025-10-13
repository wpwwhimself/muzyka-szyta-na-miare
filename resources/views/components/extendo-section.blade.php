@props([
  "title" => null,
])

@unless($slot == "" || empty($slot))
<div @class([
    "extendo-section",
    "flex",
    "down",
    "center",
])>
    @if ($title) <span class="title grayed-out">{{ $title }}</span> @endif
    {{ $slot }}
</div>
@endunless
