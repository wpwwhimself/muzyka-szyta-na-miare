@extends('layouts.mail')
@section('title', 'Prośba o doprecyzowanie')

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    w nawiązaniu do złożonego przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} {{ $is_request ? "zapytania" : "zlecenia" }},
    chciałbym doprecyzować pewne kwestie jego dotyczące.
</p>

@if ($re_quest->is_request)
<x-requests.tile :request="$re_quest" />
@else
<x-quests.tile :quest="$re_quest" />
@endif

@if ($comment = $re_quest->history->first()->comment)
{{ Illuminate\Mail\Markdown::parse($comment) }}
@endif

<p>
    Uprzejmie proszę o odpowiedź. Przyspieszy to moje prace nad {{ $is_request ? "zapytaniem" : "zleceniem" }}.
</p>

<h3>Kliknij przycisk powyżej, aby zobaczyć szczegóły {{ $is_request ? "zapytania" : "zlecenia" }}</h3>

@if ($re_quest->user)
<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $re_quest->user->notes->password }}</b>
    </i>
</p>
@endif

@endsection
