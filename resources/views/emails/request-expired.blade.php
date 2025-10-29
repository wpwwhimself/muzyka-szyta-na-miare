@extends('layouts.mail')
@section("title", "Zapytanie wygasło")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    ze względu na brak odpowiedzi na przygotowaną przeze mnie wycenę, poniższe zapytanie zostało wygaszone.
    Znaczy to, że jego warunki już nie obowiązują.
</p>

<x-requests.tile-mail :request="$request" />

<p>
    Jeśli chcesz odświeżyć zapytanie, kliknij odpowiedni przycisk na stronie zapytania.
</p>

<h3>
    Kliknij przycisk powyżej, aby zobaczyć szczegóły zapytania
</h3>

@if ($request->user)
<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $request->user->notes->password }}</b>
    </i>
</p>
@endif

@endsection
