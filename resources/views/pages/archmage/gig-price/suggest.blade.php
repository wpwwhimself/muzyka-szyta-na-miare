@extends("shipyard::layouts.admin")
@section("title", "Wycena grania")

@section("content")

<x-shipyard::app.section
    :title="$title"
    :icon="model_icon('gig-price-rates')"
    inner-class="flex down center middle"
>
    <x-slot:actions>
        <x-a :href="route('admin.model.list', ['model' => 'gig-price-defaults'])" :icon="model_icon('gig-price-defaults')">Domyślne</x-a>
        <x-a :href="route('admin.model.list', ['model' => 'gig-price-rates'])" :icon="model_icon('gig-price-rates')">Stawki</x-a>
        <x-a :href="route('admin.model.list', ['model' => 'gig-price-places'])" :icon="model_icon('gig-price-places')">Miejsca</x-a>
    </x-slot:actions>

    <div id="settings" class="flex right center">
        @foreach ($defaults as $heading => $fields)
        <x-shipyard::app.card :title="$heading">
            @if ($heading == "Dojazd")
            <x-shipyard::ui.input type="select"
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
        </x-shipyard::app.card>
        @endforeach

        <x-shipyard::app.card title="Stawki">
            <x-shipyard::ui.input type="select" name="gain_active_per_h"
                label="Stawka aktywna (granie)"
                :select-data="['options' => $rates]"
            />
            <x-shipyard::ui.input type="select" name="gain_passive_per_h"
                label="Stawka pasywna (dojazd, czekanie)"
                :select-data="['options' => $rates]"
            />
            <x-input type="checkbox"
                name="my_gear"
                label="Własny sprzęt"
            />
        </x-shipyard::app.card>
    </div>

    <div class="flex right center">
        <x-shipyard::ui.button
            action="none"
            class="tertiary"
            label="Oblicz"
            :icon="model_icon('gig-price-rates')"
            onclick="calculateGigPrice()"
        />
    </div>

    <x-shipyard::app.card id="results" inner-class="flex right center but-mobile-down">
        <x-shipyard::stats.tile label="Sugerowana cena" class="accent primary" />
        <x-shipyard::stats.tile label="Cena za h" />
        <x-shipyard::stats.tile label="Czas poświęcony" />
        <x-shipyard::stats.tile label="Koszt paliwa" />
        <x-shipyard::stats.tile label="Koszt czekania" />
        <x-shipyard::stats.tile label="Koszt grania" />
    </x-shipyard::app.card>
</x-shipyard::app.section>

<script>
function changePlace(data) {
    const distance_km = data.split("|")[1];
    document.querySelector("#settings input[name='travel_distance_km']").value = distance_km;
}

function calculateGigPrice() {
    // get settings
    const settings = Array.from(document.querySelectorAll("#settings input, #settings select"))
        .reduce((obj, input) => ({ ...obj, [input.name]: (input.type === "checkbox") ? input.checked : parseFloat(input.value) }), {});

    // calculate
    let calculations = {
        time_spent: {},
        costs: {},
        suggestions: {},
    };

    calculations.time_spent.driving = 2 * settings.travel_distance_km / 62.5; // average speed in mixed driving
    calculations.time_spent.waiting = settings.gig_time_buffer_h * 1.5; // assumes teardown takes half as long
    calculations.time_spent.playing = settings.gig_duration_h;

    calculations.costs.driving = 2 * settings.travel_distance_km * settings.fuel_cost_pln_per_l * settings.fuel_consumption_l_per_100_km / 100;
    calculations.costs.waiting_in_car = calculations.time_spent.driving * settings.gain_passive_per_h;
    calculations.costs.waiting_in_venue = calculations.time_spent.waiting * settings.gain_passive_per_h;
    calculations.costs.playing = calculations.time_spent.playing * settings.gain_active_per_h + settings.my_gear * settings.my_gear_surcharge;

    const precision = 10
    calculations.suggestions.price = Math.round(Math.sumPrecise(Object.values(calculations.costs)) / precision) * precision;
    calculations.suggestions.price_per_h = calculations.suggestions.price / Math.sumPrecise(Object.values(calculations.time_spent));

    // show results
    const pretty_mapping = [
        ["Sugerowana cena", "zł", calculations.suggestions.price],
        ["Cena za h", "zł", calculations.suggestions.price_per_h],
        ["Czas poświęcony", "h", Math.sumPrecise(Object.values(calculations.time_spent))],
        ["Koszt paliwa", "zł", calculations.costs.driving],
        ["Koszt czekania", "zł", calculations.costs.waiting_in_car + calculations.costs.waiting_in_venue],
        ["Koszt grania", "zł", calculations.costs.playing],
    ];
    pretty_mapping.forEach(([label, unit, value]) => {
        value = value.toFixed((unit == "h") ? 2 : 0);
        document.querySelector(`#results .stat-tile[data-label="${label}"] [role="value"]`).innerHTML = `${value} ${unit}`;
    });
}
</script>

@endsection
