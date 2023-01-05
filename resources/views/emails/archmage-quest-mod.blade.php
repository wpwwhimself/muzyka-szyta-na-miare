@extends('layouts.mail', [
    "title" => "Goniec przynosi wieści"
])

@section('content')
    <h2>Zmiana statusu zlecenia</h2>
    <p>
        {{ $isRequest ? ($quest->client_name ?? $quest->client->client_name) : $quest->client->client_name }} zmienił(a) status zlecenia:
    </p>

    <x-mail-quest-mini :quest="$quest" />

    <p>
        Teraz w statusie: <b>{{ $quest->status->status_name }}</b>
    </p>

    <h3>
        <a
            class="button"
            href="{{ route($isRequest ? 'request' : 'quest', ['id' => $quest->id]) }}"
            >
            Szczegóły zlecenia
        </a>
    </h3>
@endsection
