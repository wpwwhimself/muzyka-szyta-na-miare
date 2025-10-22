@extends('layouts.mail')
@section('title', 'Zmiana statusu zlecenia')

@section('content')

<p>
    {{ $quest->client_name }} zmienił(a) status zlecenia:
</p>

@if ($isRequest)
<x-requests.tile :request="$quest" />
@else
<x-quests.tile :quest="$quest" />
@endif

@if ($comment = $quest->history->first()?->comment)
{{ Illuminate\Mail\Markdown::parse($comment) }}
@endif

@endsection
