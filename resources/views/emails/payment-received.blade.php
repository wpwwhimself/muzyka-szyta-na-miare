@extends('layouts.mail')
@section("title", ($paymentShouldBeDelayed ? "Przedwczesna wpłata" : "Wpłata") . " zarejestrowana")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    otrzymałem od {{ $pl["kobieta"] ? "Pani" : "Pana" }} wpłatę dotyczącą zlecenia:
</p>

<x-quests.tile-mail :quest="$quest" />

@if ($paymentShouldBeDelayed)
<p>
    Chciałbym jednak zauważyć, że z uwagi na limity wpłat, jakie muszę spełniać, <strong>poprosiłem o wpłatę nie wcześniej niż {{ $quest->delayed_payment->format("d.m.Y") }}</strong>.
    Bardzo proszę o zwracanie uwagi na informacje podane na zleceniach w przyszłości.
</p>
<p>
    Dodatkowo będę wdzięczny za każdą informację, która poprawi czytelność tego komunikatu.
</p>
@endif

@if (!($quest->user->notes->is_veteran || $quest->user->notes->trust == 1))
<p>
    Jeśli w zleceniu są dostępne pliki,
    teraz może je {{ $pl["kobieta"] ? "Pani" : "Pan" }} pobierać za pomocą odpowiednich przycisków w widoku zlecenia.
</p>
@endif

<h3>
    Kliknij link powyżej, aby zobaczyć szczegóły zlecenia
</h3>

<p>
    {{ $paymentShouldBeDelayed ? "Niemniej" : "Uprzejmie" }} dziękuję za zaufanie i skorzystanie z moich usług.
</p>
<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->user->notes->password }}</b>
    </i>
</p>

@endsection
