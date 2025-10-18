@props([
    "model" => null,
    "saturation" => null,
    "whenToAsk" => null,
    "limitCorrected" => null,
])

@php
if ($model) {
    [
        "saturation" => $saturation,
        "when_to_ask" => $whenToAsk,
        "limit_corrected" => $limitCorrected,
    ] = \App\Http\Controllers\StatsController::runMonthlyPaymentLimit($model->price);
}
@endphp

<table id="delayed-payments-summary">
    <thead>
        <tr>
            <th>Miesiąc</th>
            <th>0</th>
            <th>+1</th>
            <th>+2</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Saturacja</td>
            <td>{{ as_pln($saturation[0]) }}</td>
            <td>{{ as_pln($saturation[1]) }}</td>
            <td>{{ as_pln($saturation[2]) }}</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td>Kiedy można brać?</td>
            <td colspan="3">
                @switch ($whenToAsk)
                    @case (0)
                    <span class="accent success">od razu</span>
                    @break

                    @case (1)
                    <span class="accent warning">w przyszłym miesiącu</span>
                    @break

                    @default
                    <span class="accent danger">za {{ $whenToAsk }} mc</span>
                @endswitch
            </td>
        </tr>
    </tfoot>
</table>
