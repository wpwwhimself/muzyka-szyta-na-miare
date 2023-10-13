@extends('layouts.mail', [
    "title" => "Goniec przynosi wieści"
])

@section('content')
    <h2>Zmiana statusu zlecenia</h2>
    <p>
        {{ $isRequest ? ($quest->client_name ?? $quest->client?->client_name) : $quest->client->client_name }} zmienił(a) status zlecenia:
    </p>

    <x-mail-quest-mini :quest="$quest" />

    @if ($comment = $quest->changes->first()?->comment)
    {{ Illuminate\Mail\Markdown::parse($comment) }}
    @endif

    <h3>
        <a
            class="button"
            href="{{ route($isRequest ? 'request' : 'quest', ['id' => $quest->id]) }}"
            >
            Szczegóły zlecenia
        </a>
    </h3>
@endsection
