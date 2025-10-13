@props([
    "request",
])

<div>
    @foreach ([
        ["Klient", $request->client_id ? "account" : "account-plus", $request->client_name, null],
        ["Termin klienta", "calendar-account", $request->hard_deadline?->diffForHumans(), null],
        ["Cena", model_icon("prices"), $request->price ? as_pln($request->price) : null, null],
        ["Termin", "calendar-blank", $request->deadline?->diffForHumans(), null],
    ] as [$label, $icon, $value])
    @continue (!$value)

    <x-shipyard.app.icon-label-value
        :icon="$icon"
        :label="$label"
    >
        {{ $value }}
    </x-shipyard.app.icon-label-value>
    @endforeach
</div>
