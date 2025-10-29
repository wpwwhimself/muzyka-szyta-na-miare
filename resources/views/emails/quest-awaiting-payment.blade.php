@extends('layouts.mail')
@section("title", "Zlecenie czeka na płatność")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    zamówione przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} zlecenie czeka na dokonanie wpłaty:
</p>

<x-quests.tile-mail :quest="$quest" />

<h3>
    Kliknij przycisk powyżej, aby zobaczyć szczegóły zlecenia
</h3>

<p>
    Uprzejmie proszę o uiszczenie należnych zobowiązań.
</p>

@endsection
