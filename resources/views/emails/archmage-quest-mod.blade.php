@extends('layouts.mail', [
    "title" => "Goniec przynosi wieści"
])

@section('content')
    <h2>Zmiana statusu zlecenia</h2>
    <p>
        {{ $quest->client->name }} zmienił(a) status zlecenia:
    </p>

    <x-mail-quest-mini :quest="$quest" />

    <h3>
        <a
            class="button"
            href="{{ route('quest', ['id' => $quest->id]) }}"
            >
            Szczegóły zlecenia
        </a>
    </h3>
@endsection
