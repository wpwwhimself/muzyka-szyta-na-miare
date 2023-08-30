@extends('layouts.app', ["title" => "Brak uprawnień"])

@section('content')

<div id="error-page">
    <h1 class="error">403</h1>
    <p>{{ $exception->getMessage() ?? "Brak uprawnień" }}</p>
</div>
@endsection
