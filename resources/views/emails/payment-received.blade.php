@extends('layouts.mail', [
    "title" => "Zlecenie zaktualizowane"
])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>
    <p>
        otrzymałem od {{ $pl["kobieta"] ? "Pani" : "Pana" }} wpłatę dotyczącą zlecenia:
    </p>

    <x-quest-mini :quest="$quest" />

    <p>
        Uprzejmie dziękuję za zaufanie i skorzystanie z moich usług.
    </p>

    <x-button
        label="Szczegóły zlecenia" icon="circle-info"
        action="{{ route('quest', ['id' => $quest->id]) }}"
        />
@endsection
