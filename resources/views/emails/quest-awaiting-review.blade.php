@extends('layouts.mail')
@section("title", "Zlecenie czeka na opinie")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    nie otrzymałem jeszcze definitywnej opinii co do zamówionego przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} zlecenia:
</p>

<x-quests.tile :quest="$quest" />

<h3>
    Kliknij przycisk powyżej, aby zobaczyć szczegóły zlecenia
</h3>

<p>
    Uprzejmie proszę o wyrażenie opinii lub ewentualnych uwag celem wprowadzenia poprawek.
    Są one dla mnie bardzo ważne, a wręcz kluczowe do płynnego zarządzania zleceniami.
</p>
<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->user->notes->password }}</b>
    </i>
</p>

@endsection
