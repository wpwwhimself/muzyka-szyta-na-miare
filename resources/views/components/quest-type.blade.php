@props(['id', 'label', 'fa-symbol'])

<i class="quest-type fa-solid {{ $faSymbol }}" {{ Popper::pop($label) }}></i>
