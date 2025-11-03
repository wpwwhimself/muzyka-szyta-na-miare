@extends('layouts.mail')
@section("title", "Zapytanie oczekuje na opinię")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    nie otrzymałem jeszcze opinii co do wyceny zamówionego przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} zlecenia:
</p>

<x-requests.tile-mail :request="$request" />

<h3>
    Kliknij link powyżej, aby zobaczyć szczegóły zapytania
</h3>

<p>
    Uprzejmie proszę o odpowiedź na podane warunki.
    Jest ona dla mnie bardzo ważna, a wręcz kluczowa do płynnego zarządzania zleceniami.
</p>

@if ($request->user)
<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $request->user->notes->password }}</b>
    </i>
</p>
@endif

@endsection
