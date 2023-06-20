@extends('layouts.mail', [
    "title" => "Wycena zapytania"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        uprzejmie dziękuję za @if ($request->client_id) ponowne @endif zainteresowanie moimi usługami.
        Poniżej prezentuję skrót wyceny zlecenia.
    </p>

    <x-mail-quest-mini :quest="$request" />

    <table>
        <tr>
            @foreach ([
                "Cena" => as_pln($request->price),
                "Termin oddania pierwszej wersji" => ($request->deadline)
                    ? "do ".$request->deadline?->format("d.m.Y")
                    : "brak",
            ] as $key => $val)
            <td class="framed-cell">
                <p>{{ $key }}</p>
                <h2>{{ $val }}</h2>
            </td>
            @endforeach
        </tr>
    </table>

    @if ($request->delayed_payment)
        <p><b>
            Z uwagi na limity przyjmowanych przeze mnie wpłat z racji prowadzenia działalności nierejestrowanej,
            proszę o dokonanie wpłaty po {{ $request->delayed_payment->format('d.m.Y') }}.
        </b></p>
    @endif

    <p>
        Termin wykonania został dopasowany do moich możliwości przerobowych. Jest szansa na szybsze wykonanie przeze mnie zlecenia, mimo wszystko jednak podaję górną granicę oczekiwania.
    </p>
    <p>
        Jeśli zlecenie powinno zostać wykonane w trybie pilnym, proszę o odpowiedni komentarz w wycenie, a przekalkuluję wszystko jeszcze raz.
        Należy się przy tym jednak liczyć z możliwymi większymi kosztami.
    </p>

    @if ($comment = $request->changes->last()->comment)
    <p>{{ $comment }}</p>
    @endif

    <p>
        Proszę o potwierdzenie warunków przyciskiem poniżej.
    </p>

    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}"
            >
            tutaj,
        </a>
        aby potwierdzić warunki
    </h3>
    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route('request', ['id' => $request->id]) }}"
            >
            tutaj,
        </a>
        aby zobaczyć szczegóły zapytania
    </h3>

    @if ($request->client)
    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $request->client->user->password }}</b>
        </i>
    </p>
    @endif

@endsection
