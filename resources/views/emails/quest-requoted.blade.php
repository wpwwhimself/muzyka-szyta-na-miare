@extends('layouts.mail', [
    "title" => "Zmieniona wycena zlecenia"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        niestety z uwagi na {{ $reason }}, nie jestem w stanie wykonać zlecenia na warunkach określonych poprzednio.
        Wobec tego muszę dokonać zmiany w wycenie {{ $pl["kobieta"] ? "Pani" : "Pana" }} zlecenia.
    </p>

    <x-mail-quest-mini :quest="$quest" />

    <table>
        <tr>
            @foreach ([
                "Cena" => as_pln($quest->price).(($quest->client->budget) ? "*" : ""),
                "Termin oddania pierwszej wersji" => ($quest->deadline)
                    ? "do ".$quest->deadline?->format("d.m.Y")
                    : "brak",
            ] as $key => $val)
            <td class="framed-cell">
                <p>{{ $key }}</p>
                <h2>{{ $val }}</h2>
            </td>
            @endforeach
        </tr>
    </table>

    @if ($quest->delayed_payment)
        <p><b>
            Z uwagi na limity przyjmowanych przeze mnie wpłat z racji prowadzenia działalności nierejestrowanej,
            proszę o dokonanie wpłaty po {{ $quest->delayed_payment->format('d.m.Y') }}.
        </b></p>
    @endif

    @if ($quest->client->budget)
    <p><i>
        *{{ ($quest->client->budget >= $price_difference) ? "Całość" : "Część" }}
        różnicy kwoty zlecenia zostanie pokryta ze zgromadzonego przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} budżet w wysokości
        {{ as_pln($quest->client->budget) }}
    </i></p>
    @endif

    <p>
        
        Jeśli <b>nie zgadza się {{ $pl["kobieta"] ? "Pani" : "Pan" }} na nowe warunki</b> i chce zrezygnować ze zlecenia, proszę o kliknięcie odpowiedniego przycisku w widoku zlecenia.
        W przeciwnym wypadku nie musi {{ $pl["kobieta"] ? "Pani" : "Pan" }} podejmować żadnych czynności.
    </p>

    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route('quest', ['id' => $quest->id]) }}"
            >
            tutaj,
        </a>
        aby zobaczyć szczegóły zlecenia
    </h3>

    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->client->user->password }}</b>
        </i>
    </p>

@endsection
