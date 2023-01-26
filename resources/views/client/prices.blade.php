@extends('layouts.app', compact("title"))

@section('content')

<section id="prices">
    <div class="section-header">
        <h1><i class="fa-solid fa-barcode"></i> Cennik</h1>
    </div>

    @if ($discount != 0)
    <p>Podane ceny uwzględniają zniżkę w wysokości {{ $discount * 100 }}%</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Usługa</th>
                <th>Cena</th>
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
                    <td colspan=2>
                        <h2>{{ $header }}</h2>
                    </td>
                </tr>
                @foreach ($prices->where("quest_type_id", $i) as $price)
                <tr>
                    <td>{{ $price->service }}</td>
                    @if ($price->operation == "+")
                    <td>{{ $price->{"price_".strtolower(pricing(Auth::id()))} * (1+$discount) }} zł</td>
                    @else
                    <td>{{ $price->{"price_".strtolower(pricing(Auth::id()))} * 100 }}%</td>
                    @endif
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <h2>Jak naliczam ceny?</h2>
    <p>
        Wszystkie wielkości podane w procentach naliczane są w ostatniej kolejności.
        Jeżeli cena posiada kilka takich składników, procenty są sumowane a następnie mnożone przez kwotę.
    </p>
    <p>
        Na przykład dla zestawu:
    </p>
    <ul>
        <li>50 zł,</li>
        <li>30%,</li>
        <li>15 zł,</li>
        <li>-5%</li>
    </ul>
    <p>
        W pierwszej kolejności dodawane są złotówki (50 + 15 zł),
        a następnie tę kwotę mnoży się przez procenty ((100 + 30 - 5)%),
        otrzymując razem: 65 × 125% = 81,25 zł
    </p>
</section>

@endsection
