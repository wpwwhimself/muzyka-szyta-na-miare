@extends('layouts.mail')
@section("title", "Wycena zlecenia zmieniona")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    niestety z uwagi na {{ $reason }}, nie jestem w stanie wykonać zlecenia na warunkach określonych poprzednio.
    Wobec tego muszę dokonać zmiany w wycenie {{ $pl["kobieta"] ? "Pani" : "Pana" }} zlecenia.
</p>

<x-quests.tile :quest="$quest" />

@if ($quest->user->notes->budget)
<p><i>
    *{{ ($quest->user->notes->budget >= $price_difference) ? "Całość" : "Część" }}
    różnicy kwoty zlecenia zostanie pokryta ze zgromadzonego przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} budżet w wysokości
    {{ as_pln($quest->user->notes->budget) }}
</i></p>
@endif

<p>
    Proszę o przejście do zlecenia, aby zaakceptować lub odrzucić nowe warunki zlecenia.
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
