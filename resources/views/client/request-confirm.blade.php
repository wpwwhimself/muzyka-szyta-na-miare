@extends('layouts.app', compact("title"))

@section('content')

<h2>
    Zapytanie zostało pomyślnie przetworzone.
</h2>

<p>Uprzejmie dziękuję za złożenie zamówienia. Wkrótce będę się kontaktował z wyceną dla poniższych zleceń:</p>
<div class="flex-right">
    @foreach($requests_created as $request)
    <x-quest-mini :quest="$request" />
    @endforeach
</div>

@endsection
