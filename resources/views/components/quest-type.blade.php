@props(['id', 'label', 'fa-symbol', 'small' => false])

<i class="quest-type {{ $small ? 'small' : '' }} fa-solid {{ $faSymbol }}" {{ Popper::pop($label) }}></i>
