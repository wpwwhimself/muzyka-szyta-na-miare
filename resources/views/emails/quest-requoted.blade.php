@extends('layouts.mail', [
    "title" => "Zmieniona wycena zlecenia"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        z uwagi na {{ $reason }},
        jestem zmuszony dokonać zmiany w wycenie {{ $pl["kobieta"] ? "Pani" : "Pana" }} zlecenia.
    </p>

    <x-mail-quest-mini :quest="$quest" />

    <table>
        <tr>
            @foreach ([
                "Cena" => $quest->price . " zł",
                "Termin oddania pierwszej wersji" => $quest->deadline?->format("d.m.Y") ?? "brak",
            ] as $key => $val)
            <td class="framed-cell">
                <p>{{ $key }}</p>
                <h2>{{ $val }}</h2>
            </td>
            @endforeach
        </tr>
    </table>

    <p>
        Niestety z przyczyn wyżej określonych nie jestem w stanie wykonać zlecenia na warunkach określonych poprzednio.
        Jeśli nie zgadza się {{ $pl["kobieta"] ? "Pani" : "Pan" }} na nowe warunki i chce zrezygnować ze zlecenia, proszę o kliknięcie odpowiedniego przycisku w widoku zlecenia.
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