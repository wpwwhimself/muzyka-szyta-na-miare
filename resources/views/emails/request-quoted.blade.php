@extends('layouts.mail', [
    "title" => "Wycena zapytania"
])

@section('content')
    <h2>Szanowny Panie XYZ,</h2>
    <p>
        uprzejmie dziękuję za zainteresowanie moimi usługami.
        Poniżej prezentuję skrót wyceny zlecenia. Pełną jej wersję można znaleźć, klikając w link poniżej.
    </p>

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
