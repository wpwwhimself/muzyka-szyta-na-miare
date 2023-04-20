@extends('layouts.mail', [
    "title" => "Prośba o doprecyzowanie"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        w nawiązaniu do złożonego przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} {{ $is_request ? "zapytania" : "zlecenia" }},
        chciałbym doprecyzować kwestie poruszone w {{ $pl["kobieta"] : "Pani" : "Pana" }} komentarzu.
    </p>

    <x-mail-quest-mini :quest="$re_quest" />

    @if ($comment = $re_quest->changes->last()->comment)
    <p>{{ $comment }}</p>
    @endif

    <p>
        Uprzejmie proszę o odpowiedź. Przyspieszy to moje prace nad zleceniem.
    </p>

    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route($is_request ? 'request' : 'quest', ['id' => $re_quest->id]) }}"
            >
            tutaj,
        </a>
        aby zobaczyć szczegóły {{ $is_request ? "zapytania" : "zlecenia" }}
    </h3>

    @if ($request->client)
    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $request->client->user->password }}</b>
        </i>
    </p>
    @endif

@endsection
