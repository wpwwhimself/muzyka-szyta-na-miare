@extends('layouts.mail', [
    "title" => "Zlecenie zaktualizowane"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        otrzymałem od {{ $pl["kobieta"] ? "Pani" : "Pana" }} wpłatę dotyczącą zlecenia:
    </p>

    <x-mail-quest-mini :quest="$quest" />

    @if (!(is_veteran($quest->client_id) || $quest->client->trust == 1))
    <p>
        Teraz może {{ $pl["kobieta"] ? "Pani" : "Pan" }} pobierać pliki związane ze zleceniem za pomocą odpowiednich przycisków w widoku zlecenia.
    </p>
    @endif

    <h3>
        Kliknij
        <a
            class="button"
            href="{{ route('quest', ['id' => $quest->id]) }}"
            >
            tutaj,
        </a>
        aby zobaczyć szczegóły zlecenia
    </h3>

    <p>
        Uprzejmie dziękuję za zaufanie i skorzystanie z moich usług.
    </p>
    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->client->user->password }}</b>
        </i>
    </p>
@endsection
