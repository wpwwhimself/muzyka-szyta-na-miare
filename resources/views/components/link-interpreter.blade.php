@props(['raw'])

<div class="quest-links">
    @foreach (explode(",", $raw) as $link)
    @if (filter_var($link, FILTER_VALIDATE_URL))
    <a href="{{ $link }}" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a>
    @endif
    @endforeach
</div>
