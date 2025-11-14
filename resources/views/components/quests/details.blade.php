@props([
    "quest",
])

<div>
    @foreach ([
        ["Klient", model_icon("users"), $quest->user->name_and_badges, null],
        ["Cena", model_icon("prices"), as_pln($quest->price), $quest->paid ? "accent success" : null],
        ["Termin", "calendar-blank", $quest->deadline?->addDay()->diffForHumans(),
            $quest->deadline?->addDay()->isPast() ? "accent error" : (
            $quest->deadline?->subDays(2)->isPast() ? "accent danger" :
            null
        )],
        ["Termin klienta", "calendar-account", $quest->hard_deadline?->addDay()->diffForHumans(),
            $quest->hard_deadline?->addDay()->isPast() ? "accent error" : (
            $quest->hard_deadline?->subDays(2)->isPast() ? "accent danger" :
            null
        )],
    ] as [$label, $icon, $value, $class])
    @continue (!$value)

    <x-shipyard.app.icon-label-value
        :icon="$icon"
        :label="$label"
        :class="$class"
    >
        {!! $value !!}
    </x-shipyard.app.icon-label-value>
    @endforeach
</div>
