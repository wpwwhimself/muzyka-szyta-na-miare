@extends('layouts.app', ["title" => "Nie znaleziono"])

@section('content')

<div id="error-page">
    <h1 class="error">404</h1>
    <p>{{ $exception->getMessage() ?? "Nie znaleziono" }}</p>
</div>
@endsection
