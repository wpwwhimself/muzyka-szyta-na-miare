@extends('layouts.mail', [
    "title" => "Zlecenie zaktualizowane"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        wprowadziłem zmiany w {{ $pl["kobieta"] ? "Pani" : "Pana" }} zleceniu:
    </p>

    <x-mail-quest-mini :quest="$quest" />

    @if ($comment = $quest->changes->first()->comment)
    <p>
        <b>Najnowszy komentarz:</b>
        {{ $comment }}
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
        Uprzejmie proszę o wyrażenie opinii lub ewentualnych uwag celem wprowadzenia poprawek. Możesz to zrobić, klikając w link powyżej.
    </p>
    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $quest->client->user->password }}</b>
        </i>
    </p>
@endsection
