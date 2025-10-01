@extends("layouts.mail")
@section("title", "Nowe zapytanie o DJa")

@section("content")

<x-shipyard.app.h icon="headphones">Nowe zapytanie dla DJa</x-shipyard.app.h>

<x-client.contact-info :data="$data" />

<div class="flex down">
    <span><strong>Rodzaj imprezy</strong>: {{ $data["occasion"] }}</span>
    <span><strong>Data</strong>: {{ $data["date"] }}</span>
    <p>{{ $data["wishes"] }}</p>
</div>

@endsection
