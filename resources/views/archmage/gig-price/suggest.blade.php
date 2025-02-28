@extends("layouts.app")

@section("content")

<x-section :title="$title" icon="magnifying-glass-dollar">
    <x-slot name="buttons">
        <x-a :href="route('gig-price-defaults')" icon="gear">Domyślne</x-a>
    </x-slot>

    <div id="settings" class="flex-right center">
        @foreach ($defaults as $heading => $fields)
        <div class="section-like flex-down center">
            <h2>{{ $heading }}</h2>
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
    </div>
    
    <div class="flex-right">
        <x-button action="#/" label="Oblicz" icon="magnifying-glass-dollar" onclick="calculateGigPrice()" />
    </div>

    <table id="results">
    
    </table>
</x-section>

<script>
function calculateGigPrice() {
    // get settings
    const settings = Array.from(document.querySelectorAll("#settings input"))
        .reduce((obj, input) => ({ ...obj, [input.name]: parseFloat(input.value) }), {})

    // calculate
    let calculations = {}
    calculations["time_spent"] = {
        "label": "Czas poświęcony",
        "unit": "h",
        "value": settings.gig_duration_h + 2 * settings.gig_time_buffer_h + 2 * settings.travel_time_h,
    }
    calculations["time_cost"] = {
        "label": "Koszt czasu",
        "unit": "zł",
        "value": settings.gain_per_h * calculations.time_spent.value,
    }
    calculations["distance_traveled"] = {
        "label": "Przejechany dystans",
        "unit": "km",
        "value": 2 * settings.travel_distance_km,
    }
    calculations["distance_cost"] = {
        "label": "Koszt przejazdu",
        "unit": "zł",
        "value": settings.fuel_cost_pln_per_l * settings.fuel_consumption_l_per_100_km / 100 * calculations.distance_traveled.value,
    }
    calculations["total_cost"] = {
        "label": "Koszty razem",
        "unit": "zł",
        "value": calculations.time_cost.value + calculations.distance_cost.value,
    }
    
    let summary = {}
    summary["suggested_price"] = {
        "label": "Sugerowana cena",
        "unit": "zł",
        "value": calculations.total_cost.value + settings.gain_net,
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
}
</script>

@endsection