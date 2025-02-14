@extends('layouts.mail', compact("title"))

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        w nawiązaniu do złożonego przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} {{ $is_request ? "zapytania" : "zlecenia" }},
        chciałbym doprecyzować pewne kwestie jego dotyczące.
    </p>

    <x-mail-quest-mini :quest="$re_quest" />

    @if ($comment = $re_quest->history->first()->comment)
    {{ Illuminate\Mail\Markdown::parse($comment) }}
    @endif

    <p>
        Uprzejmie proszę o odpowiedź. Przyspieszy to moje prace nad {{ $is_request ? "zapytaniem" : "zleceniem" }}.
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

    @if ($re_quest->client)
    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $re_quest->client->password }}</b>
        </i>
    </p>
    @endif

@endsection
