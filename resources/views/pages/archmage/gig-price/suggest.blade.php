@extends("layouts.app")
@section("title", "Wycena grania")

@section("content")

<x-section :title="$title" :icon="model_icon('gig-price-rates')">
    <x-slot name="buttons">
        <x-a :href="route('admin.model.list', ['model' => 'gig-price-defaults'])" :icon="model_icon('gig-price-defaults')">Domyślne</x-a>
        <x-a :href="route('admin.model.list', ['model' => 'gig-price-rates'])" :icon="model_icon('gig-price-rates')">Stawki</x-a>
        <x-a :href="route('admin.model.list', ['model' => 'gig-price-places'])" :icon="model_icon('gig-price-places')">Miejsca</x-a>
    </x-slot>

    <div id="settings" class="flex right center">
        @foreach ($defaults as $heading => $fields)
        <div class="section flex down center">
            <h2>{{ $heading }}</h2>

            @if ($heading == "Dojazd")
            <x-shipyard.ui.input type="select"
                :select-data="[
                    'options' => $places,
                    'emptyOption' => 'inne',
                ]"
                name="place_id"
                label="Miejsce"
                :icon="model_icon('gig-price-places')"
                onchange="changePlace(event.target.value)"
            />
            @endif

            @foreach ($fields as $setting)
            <x-input type="number" step="0.01"
                :name="$setting->name"
                :label="$setting->label"
                :value="$setting->value"
                small
            />
            @endforeach
        </div>
        @endforeach

        <div class="section flex down center">
            <h2>Stawki</h2>

            <x-shipyard.ui.input type="select" name="gain_active_per_h"
                label="Stawka aktywna (granie)"
                :select-data="['options' => $rates]"
            />
            <x-shipyard.ui.input type="select" name="gain_passive_per_h"
                label="Stawka pasywna (dojazd, czekanie)"
                :select-data="['options' => $rates]"
            />
            <x-input type="checkbox"
                name="my_gear"
                label="Własny sprzęt"
            />
        </div>
    </div>

    <div class="flex right center">
        <x-shipyard.ui.button
            action="none"
            class="tertiary"
            label="Oblicz"
            :icon="model_icon('gig-price-rates')"
            onclick="calculateGigPrice()"
        />
    </div>

    <table id="results">

    </table>
</x-section>

<script>
function changePlace(data) {
    const distance_km = data.split("|")[1];
    document.querySelector("#settings input[name='travel_distance_km']").value = distance_km;
}

function calculateGigPrice() {
    // get settings
    const settings = Array.from(document.querySelectorAll("#settings input, #settings select"))
        .reduce((obj, input) => ({ ...obj, [input.name]: (input.type === "checkbox") ? input.checked : parseFloat(input.value) }), {})
    console.log(settings)

    // calculate
    let calculations = {}
    calculations["drive_time"] = {
        "label": "Przewidywany czas jazdy",
        "unit": "h",
        "value": settings.travel_distance_km / 60, // average of 60 km/h
    }
    calculations["distance_traveled"] = {
        "label": "Przejechany dystans",
        "unit": "km",
        "value": 2 * settings.travel_distance_km,
    }
    calculations["distance_cost"] = {
        "label": "Koszt przejazdu (w jedną stronę)",
        "unit": "zł",
        "value": settings.fuel_cost_pln_per_l * settings.fuel_consumption_l_per_100_km / 100 * calculations.distance_traveled.value,
    }
    calculations["passive_cost"] = {
        "label": "Koszt pasywny (dojazd łącznie, czekanie)",
        "unit": "zł",
        "value": settings.gain_passive_per_h * (2 * calculations.drive_time.value + settings.gig_time_buffer_h),
    }
    calculations["active_cost"] = {
        "label": "Koszt aktywny (granie)",
        "unit": "zł",
        "value": settings.gain_active_per_h * (settings.gig_duration_h) + (settings.my_gear * settings.my_gear_surcharge),
    }
    calculations["total_cost"] = {
        "label": "Koszty razem",
        "unit": "zł",
        "value": calculations.passive_cost.value + calculations.active_cost.value,
    }

    let summary = {}
    summary["suggested_price"] = {
        "label": "Sugerowana cena",
        "unit": "zł",
        "value": calculations.total_cost.value,
    }

    let balance = {}
    balance["drive_return"] = {
        "label": "Zwrot za przejazd",
        "unit": "zł",
        "value": summary.suggested_price.value - calculations.distance_cost.value,
    }

    const precision = 10
    summary.suggested_price.value = Math.round(summary.suggested_price.value / precision) * precision

    // show results
    const results = document.querySelector("#results")
    results.innerHTML = ""

    Object.entries(calculations).map(row => results.insertAdjacentHTML("beforeend", `<tr>
        <td>${row[1].label}</td>
        <td>${row[1].value.toFixed(2)} ${row[1].unit}</td>
    </tr>`))
    Object.entries(summary).map(row => results.insertAdjacentHTML("beforeend", `<tr>
        <th>${row[1].label}</th>
        <th>${row[1].value.toFixed(2)} ${row[1].unit}</th>
    </tr>`))
    Object.entries(balance).map(row => results.insertAdjacentHTML("beforeend", `<tr>
        <td>${row[1].label}</td>
        <td>${row[1].value.toFixed(2)} ${row[1].unit}</td>
    </tr>`))
}
</script>

@endsection
