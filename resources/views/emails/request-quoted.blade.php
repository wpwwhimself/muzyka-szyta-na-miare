@extends('layouts.mail', [
    "title" => "Wycena zapytania"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        uprzejmie dziękuję za zainteresowanie moimi usługami.
        Poniżej prezentuję skrót wyceny zlecenia.
    </p>

    <x-quest-mini :quest="$request" />
    <div id="quote-summary" class="flex-right">
        @foreach ([
            "Cena" => $request->price . " zł",
            "Termin oddania pierwszej wersji" => gmdate("d.m.Y", strtotime($request->deadline)),
        ] as $key => $val)
        <div class="section-like">
            <p>{{ $key }}</p>
            <h2>{{ $val }}</h2>
        </div>
        @endforeach
    </div>

    @if ($request->hard_deadline)
    <p>Termin wykonania został dopasowany do moich możliwości przerobowych. Jeśli zlecenie powinno zostać wykonane w trybie pilnym, proszę o odpowiednią zmianę daty w wycenie, a przekalkuluję jeszcze raz.</p>
    @endif

    <p>Proszę o potwierdzenie warunków przyciskiem poniżej. Po otrzymaniu pozytywnej odpowiedzi przystąpię do realizacji projektu.</p>

    <x-button
            label="Potwierdź warunki" icon="9"
            action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}"
            />
    <x-button
        label="Szczegóły" icon="circle-info"
        action="{{ route('request', ['id' => $request->id]) }}"
        />

@endsection
