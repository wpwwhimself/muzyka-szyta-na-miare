@extends('layouts.mail', [
    "title" => "Zwrócono wpłatę"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        wysłałem zwrot wpłaty, jaką otrzymałem od {{ $pl["kobieta"] ? "Pani" : "Pana" }} w ramach zlecenia:
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
        Mimo niedogodności polecam się do dalszych usług.
    </p>
    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->client->user->password }}</b>
        </i>
    </p>
@endsection
