@extends('layouts.mail', compact("title"))

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        uprzejmie dziękuję za @if ($request->client_id) ponowne @endif zainteresowanie moimi usługami.
        Wycena {{ $pl["kobieta"] ? "Pani" : "Pana" }} zlecenia została już przeze mnie przygotowana.
    </p>

    <x-mail-quest-mini :quest="$request" />

    @if ($comment = $request->history->first()->comment)
    {{ Illuminate\Mail\Markdown::parse($comment) }}
    @endif

    <p>Uprzejmie proszę o zapoznanie się i wyrażenie swojej opinii za pomocą przycisków dostępnych pod wyceną.</p>

    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route('request', ['id' => $request->id]) }}"
            >
            tutaj,
        </a>
        aby zobaczyć szczegóły zapytania
    </h3>

    @if ($request->client)
    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $request->client->user->password }}</b>
        </i>
    </p>
    @endif

@endsection
