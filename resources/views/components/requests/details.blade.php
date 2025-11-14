@props([
    "request",
])

<div>
    @foreach ([
        ["Klient", $request->client_id ? "account" : "account-plus", $request->user?->name_and_badges ?? $request->client_name, null],
        ["Cena", model_icon("prices"), $request->price ? as_pln($request->price) : null, null],
        ["Termin", model_field_icon("requests", "deadline"), $request->deadline?->addDay()->diffForHumans(),
            $request->deadline?->addDay()->isPast() ? "accent error" : (
            $request->deadline?->subDays(2)->isPast() ? "accent danger" :
            null
        )],
        ["Termin klienta", model_field_icon("requests", "hard_deadline"), $request->hard_deadline?->addDay()->diffForHumans(),
            $request->hard_deadline?->addDay()->isPast() ? "accent error" : (
            $request->hard_deadline?->subDays(2)->isPast() ? "accent danger" :
            null
        )],
    ] as [$label, $icon, $value])
    @continue (!$value)

    <x-shipyard.app.icon-label-value
        :icon="$icon"
        :label="$label"
    >
        {!! $value !!}
    </x-shipyard.app.icon-label-value>
    @endforeach
</div>
