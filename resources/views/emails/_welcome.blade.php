@extends('layouts.mail', compact("title"))

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        uprzejmie informuję, że wraz z początkiem 2023 roku uruchomiona została
        <b>
            nowa wersja mojej strony internetowej,
        </b>
        a co za tym idzie, zmienia się m.in. sposób zarządzania zlecanymi projektami.
    </p>
    <p>
        W dużym skrócie: za pomocą nowej strony internetowej będzie {{ $pl["kobieta"] ? "mogła Pani" : "mógł Pan" }} <b>składać nowe i przeglądać złożone już zamówienia</b>, a także <b>pobierać przygotowane przeze mnie pliki</b>.
        W tym celu będzie {{ $pl["kobieta"] ? "Pani" : "Pan" }} potrzebować <b>hasła dostępu</b> do swojego konta na tej stronie.
    </p>
    <p>
        Przypominam więc, że do swojego konta może się {{ $pl["kobieta"] ? "Pani" : "Pan" }} zalogować za pomocą hasła o treści:
    </p>
    <h2>
        {{ $client->user->password }}
    </h2>
@endsection
