@extends('layouts.mail', compact("title"))

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        zamówione przez {{ $pl["kobieta"] ? "Panią" : "Pana" }} zlecenie czeka na dokonanie wpłaty:
    </p>

    <x-mail-quest-mini :quest="$quest" />

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
        Uprzejmie proszę o uiszczenie należnych zobowiązań.
    </p>
@endsection
