@extends('layouts.mail')
@section("title", "Wpłata zwrócona")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    wysłałem zwrot wpłaty, jaką otrzymałem od {{ $pl["kobieta"] ? "Pani" : "Pana" }} w ramach zlecenia:
</p>

<x-quests.tile-mail :quest="$quest" />

<h3>
    Kliknij przycisk powyżej, aby zobaczyć szczegóły zlecenia
</h3>

<p>
    Mimo niedogodności polecam się do dalszych usług.
</p>
<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->user->notes->password }}</b>
    </i>
</p>

@endsection
