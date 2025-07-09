@extends('layouts.mail', compact("title"))

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        ze względu na {{ $reason }}, zamówione przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} zlecenie zostało wygaszone.
        Oznacza to, że jest ono traktowane jako {{ $treat_as }}.
    </p>

    <x-mail-quest-mini :quest="$quest" />

    <p>
        Jeśli chcesz przywrócić zlecenie, kliknij odpowiedni przycisk na stronie zlecenia.
    </p>

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
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->client->password }}</b>
        </i>
    </p>
@endsection
