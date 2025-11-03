@extends('layouts.mail')
@section("title", "Zlecenie zmodyfikowane")

@section('content')

<h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
<p>
    wprowadziłem zmiany w {{ $pl["kobieta"] ? "Pani" : "Pana" }} zleceniu:
</p>

<x-quests.tile-mail :quest="$quest" />

@if ($comment = $quest->history->first()->comment)
{{ Illuminate\Mail\Markdown::parse($comment) }}
@endif

<h3>
    Kliknij link powyżej, aby zobaczyć szczegóły zlecenia
</h3>

@if ($quest->song->has_safe_files)
<p>
    Uprzejmie proszę o wyrażenie opinii lub ewentualnych uwag celem wprowadzenia poprawek. Możesz to zrobić, klikając w link powyżej.
</p>
@endif

<p>
    <i>
        Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->user->notes->password }}</b>
    </i>
</p>

@endsection
