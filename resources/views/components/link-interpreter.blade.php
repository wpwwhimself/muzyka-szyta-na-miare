@props(['raw'])

<div class="quest-links">
    @foreach (explode(",", $raw) as $link)
    @if (filter_var($link, FILTER_VALIDATE_URL))
    <x-button action="{{ $link }}" target="_blank" icon="up-right-from-square" label="Link" :small="true" />
    @endif
    @endforeach
</div>
