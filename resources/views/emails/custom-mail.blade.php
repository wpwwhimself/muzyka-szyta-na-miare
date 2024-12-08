@extends('layouts.mail', ["title" => $subject])

@section('content')
    <h2>{{ $pl["kobieta"] ? "Szanowna Pani" : "Szanowny Panie" }} {{ $pl["imiewolacz"] }},</h2>

    {!! \Illuminate\Mail\Markdown::parse($content) !!}

    <p>
        <i>
            Dla przypomnienia: hasło dostępu do {{ $pl["kobieta"] ? "Pani" : "Pana" }} konta to <b>{{ $client->user->password }}</b>
        </i>
    </p>
@endsection
