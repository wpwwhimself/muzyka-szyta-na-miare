@props([
    "model" => null,
    "price" => null,
    "positions" => null,
    "override" => false,
    "labels" => null,
    "minimalPrice" => false,
])

@php
if ($model) {
    [
        "price" => $price,
        "positions" => $positions,
        "override" => $override,
        "labels" => $labels,
        "minimal_price" => $minimalPrice,
    ] = \App\Http\Controllers\StatsController::runPriceCalc($model->price_code, $model->client_id, true);
}
@endphp

<div id="price-summary">
    <x-shipyard.app.loader />

    <h4>Podsumowanie wyceny</h4>

    @if ($price)
    <table>
        <tbody @class(["overridden" => $override])>
            @foreach ($positions ?? [] as $line)
            <tr>
                <td>{{ $line[0] }}</td>
                <td>{{ $line[1] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Razem:</th>
                <th class="accent primary">
                    {{ _c_(as_pln($price)) }}
                    @if ($minimalPrice)
                    (cena minimalna)
                    @endif
                </th>
            </tr>
        </tfoot>
    </table>
    @endif
</div>
