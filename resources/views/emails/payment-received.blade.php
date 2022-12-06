@extends('layouts.mail', [
    "title" => "Zlecenie zaktualizowane"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        otrzymałem od {{ $pl["kobieta"] ? "Pani" : "Pana" }} wpłatę dotyczącą zlecenia:
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

    <p>
        Uprzejmie dziękuję za zaufanie i skorzystanie z moich usług.
    </p>
@endsection
