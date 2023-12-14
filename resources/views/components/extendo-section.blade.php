@props([
  "title",
])

@unless($slot == "" || empty($slot))
<div class="extendo-section flex-down center">
    <span class="title grayed-out">{{ $title }}</span>
    <span>{{ $slot }}</span>
</div>
@endunless