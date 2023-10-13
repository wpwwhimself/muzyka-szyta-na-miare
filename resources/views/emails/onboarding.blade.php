@extends('layouts.mail', [
    "title" => "Konto założone"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        jeszcze raz uprzejmie dziękuję za zainteresowanie moimi usługami.
        Mam nadzieję, że moja twórczość przypadnie {{ $pl["kobieta"] ? "Pani" : "Panu" }} do gustu.
    </p>

    <p>
        W systemie jest już dostępne {{ $pl["kobieta"] ? "Pani" : "Pana" }} konto,
        na którym może {{ $pl["kobieta"] ? "Pani" : "Pan" }} śledzić postępy w pracach i składać nowe zapytania.
    </p>

    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route('dashboard') }}"
            >
            tutaj,
        </a>
        aby się zalogować
    </h3>

    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $client->user->password }}</b>
        </i>
    </p>
@endsection
