@extends('layouts.mail')
@section("title", "Zapytanie wycenione")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    uprzejmie dziękuję za @if ($request->client_id) ponowne @endif zainteresowanie moimi usługami.
    Wycena {{ $pl["kobieta"] ? "Pani" : "Pana" }} zlecenia została już przeze mnie przygotowana.
</p>

<x-requests.tile-mail :request="$request" />

@if ($comment = $request->history->first()->comment)
{{ Illuminate\Mail\Markdown::parse($comment) }}
@endif

<p>Uprzejmie proszę o zapoznanie się i wyrażenie swojej opinii za pomocą przycisków dostępnych pod wyceną.</p>

<h3>Kliknij link powyżej, aby zobaczyć szczegóły zapytania</h3>

@if ($request->user)
<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $request->user->notes->password }}</b>
    </i>
</p>
@endif

@endsection
