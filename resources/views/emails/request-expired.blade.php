@extends('layouts.mail', [
    "title" => "Zapytanie wygasło"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        ze względu na brak odpowiedzi na przygotowaną przeze mnie wycenę, poniższe zapytanie zostało wygaszone.
        Znaczy to, że jego warunki już nie obowiązują.
    </p>

    <x-mail-quest-mini :quest="$request" />

    <p>
        Jeśli chcesz odświeżyć zapytanie, kliknij odpowiedni przycisk na widoku zapytania.
    </p>

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
