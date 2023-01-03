@extends('layouts.mail', [
    "title" => "Zlecenie oczekuje na opinię"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        nie otrzymałem jeszcze definitywnej opinii co do zamówionego przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} zlecenia:
    </p>

    <x-mail-quest-mini :quest="$quest" />

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
        Uprzejmie proszę o wyrażenie opinii lub ewentualnych uwag celem wprowadzenia poprawek.
        Są one dla mnie bardzo ważne, a wręcz kluczowe do płynnego zarządzania zleceniami.
    </p>
@endsection
