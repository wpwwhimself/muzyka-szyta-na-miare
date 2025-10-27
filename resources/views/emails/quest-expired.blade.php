@extends('layouts.mail')
@section("title", "Zlecenie wygasło")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    ze względu na {{ $reason }}, zamówione przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} zlecenie zostało wygaszone.
    Oznacza to, że jest ono traktowane jako {{ $treat_as }}.
</p>

<x-quests.tile :quest="$quest" />

<p>
    Jeśli chcesz przywrócić zlecenie, kliknij odpowiedni przycisk na stronie zlecenia.
</p>

<h3>
    Kliknij przycisk powyżej, aby zobaczyć szczegóły zlecenia
</h3>

<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->user->notes->password }}</b>
    </i>
</p>

@endsection
