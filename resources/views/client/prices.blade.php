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
</section>

@endsection
