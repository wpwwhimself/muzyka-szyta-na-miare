@extends('layouts.mail', [
    "title" => "Wycena zapytania"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        uprzejmie dziękuję za zainteresowanie moimi usługami.
        Poniżej prezentuję skrót wyceny zlecenia.
    </p>

    <x-mail-quest-mini :quest="$request" />

    <table>
        <tr>
            @foreach ([
                "Cena" => $request->price . " zł",
                "Termin oddania pierwszej wersji" => gmdate("d.m.Y", strtotime($request->deadline)),
            ] as $key => $val)
            <td class="framed-cell">
                <p>{{ $key }}</p>
                <h2>{{ $val }}</h2>
            </td>
            @endforeach
        </tr>
    </table>

    @if ($request->hard_deadline)
    <p>
        Termin wykonania został dopasowany do moich możliwości przerobowych.
        Jeśli zlecenie powinno zostać wykonane w trybie pilnym, proszę o odpowiednią zmianę daty w wycenie, a przekalkuluję wszystko jeszcze raz.
        Należy się przy tym jednak liczyć z możliwymi większymi kosztami.
    </p>
    @endif

    <p>
        Proszę o potwierdzenie warunków przyciskiem poniżej.
    </p>

    <h3>
        <a
            class="button"
            href="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}"
            >
            Potwierdź warunki
        </a>
        •
        <a
            class="button"
            href="{{ route('request', ['id' => $request->id]) }}"
            >
            Szczegóły zlecenia
        </a>
    </h3>

@endsection
