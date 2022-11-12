@props(['id', 'label'])

<i class="quest-type fa-solid
@switch($id)
    @case(1)
        fa-file-audio
        @break
    @case(2)
        fa-music
        @break
    @case(3)
        fa-guitar
        @break
    @default
        fa-circle-question
@endswitch
" {{ Popper::pop($label) }}></i>
