@extends('layouts.app', compact("title"))

@section('content')

<section id="prices">
    <div class="section-header">
        <h1><i class="fa-solid fa-barcode"></i> Cenniki</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>Usługa</th>
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
                    <td colspan={{ ord(CURRENT_PRICING()) - ord("A") + 2 }}>
                        <h2>{{ $header }}</h2>
                    </td>
                </tr>
                @foreach ($prices->where("quest_type_id", $i) as $price)
                <tr>
                    <td>{{ $price->service }}</td>
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
                    <td colspan={{ ord(CURRENT_PRICING()) - ord("A") + 2 }}>
                        <i class="fa-solid fa-info-circle"></i>
                        Nuty przygotowywane razem z podkładem muzycznym lub nagraniem są wyceniane o połowę taniej.
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</section>

@endsection
