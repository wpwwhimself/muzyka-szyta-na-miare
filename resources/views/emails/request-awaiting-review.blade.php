@extends('layouts.mail', compact("title"))

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        nie otrzymałem jeszcze opinii co do wyceny zamówionego przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} zlecenia:
    </p>

    <x-mail-quest-mini :quest="$request" />

    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route('request', ['id' => $request->id]) }}"
            >
            tutaj,
        </a>
        aby zobaczyć szczegóły zlecenia
    </h3>

    <p>
        Uprzejmie proszę o odpowiedź na podane warunki.
        Jest ona dla mnie bardzo ważna, a wręcz kluczowa do płynnego zarządzania zleceniami.
    </p>

    @if ($request->client)
    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $request->client->password }}</b>
        </i>
    </p>
    @endif
@endsection
