@extends('layouts.mail', ["title" => "Faktura gotowa"])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>

    <p>
        faktura do {{ $pl["kobieta"] ? "Pani" : "Pana" }} zleceń została wystawiona
        i jest dostępna <a href="{{ route('invoice', ['id' => $invoice->id]) }}">tutaj</a>.
    </p>

    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $user->password }}</b>
        </i>
    </p>
@endsection
