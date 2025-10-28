@extends('layouts.app')
@section("title", "Ceny")

@section('content')

<x-section id="prices" title="Cenniki" :icon="model_icon('prices')">
    <table>
        <thead>
            <tr>
                <th>Usługa</th>
                <th>Kod</th>
                @for ($letter = "A"; $letter != chr(ord(CURRENT_PRICING()) + 1); $letter = chr(ord($letter) + 1))
                <th>Cena {{ $letter }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ([
                "1" => "Podkłady muzyczne",
                "2" => "Nuty",
                "3" => "Nagrania",
                null => "Pozostałe"
            ] as $i => $header)
                <tr>
                    <td colspan={{ ord(CURRENT_PRICING()) - ord("A") + 3 }}>
                        <h2>{{ $header }}</h2>
                    </td>
                </tr>
                @foreach ($prices->where("quest_type_id", $i) as $price)
                <tr>
                    <td>{{ $price->service }}</td>
                    <td>{{ $price->indicator }}</td>
                    @for ($letter = "A"; $letter != chr(ord(CURRENT_PRICING()) + 1); $letter = chr(ord($letter) + 1))
                        @if ($price->operation == "+")
                        <td>{{ _c_(as_pln($price->{"price_".strtolower($letter)})) }}</td>
                        @else
                        <td>{{ _c_($price->{"price_".strtolower($letter)} * 100) }}%</td>
                        @endif
                    @endfor
                </tr>
                @endforeach
                @if ($header == "Nuty")
                <tr class="ghost">
                    <td colspan={{ ord(CURRENT_PRICING()) - ord("A") + 3 }}>
                        <i class="fa-solid fa-info-circle"></i>
                        Nuty przygotowywane razem z podkładem muzycznym lub nagraniem są wyceniane o połowę taniej.
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</x-section>

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-section title="Uwagi" icon="alert-circle">
        <ul>
            <li>
                Dla każdego typu zlecenia jest wyznaczona <b>cena minimalna</b>. Obecnie jest to:
                <ul>
                    @foreach ($minimal_prices as $label => $price)
                    <li>{{ $label }}: {{ _c_(as_pln($price)) }}</li>
                    @endforeach
                </ul>
            </li>
            <li>
                <i>Przygotowanie filmu</i> w przypadku nagrania siebie z instrumentem
                wyceniane jest do 2 filmów (czyli 6 instrumentów = qqq).
            </li>
        </ul>
    </x-section>

    <x-section title="Szybka wycena" icon="cash">
        @php
        $request = new App\Models\Request;
        @endphp

        <x-shipyard.ui.input type="select"
            name="client_id"
            label="Klient"
            :icon="model_icon('users')"
            :select-data="[
                'options' => $clients,
            ]"
        />
        <x-shipyard.ui.field-input :model="$request" field-name="price_code"
            onchange="reQuestCalcPrice(event.target.value, parseInt(document.querySelector('#client_id').value));"
        />
        <x-re_quests.price-summary :model="$request" />
        <x-re_quests.monthly-payment-limit :model="$request" />
    </x-section>
</div>

@endsection
