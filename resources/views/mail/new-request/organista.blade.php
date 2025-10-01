@extends("layouts.shipyard.mail")

@section("content")

<x-shipyard.app.h icon="piano">Nowe zapytanie dla organisty</x-shipyard.app.h>

<x-client.contact-info :data="$data" />

<div class="flex down">
    <span><strong>Rodzaj uroczystości</strong>: {{ $data["occasion"] }}</span>
    <span><strong>Data</strong>: {{ $data["date"] }}</span>
    <span><strong>Mój sprzęt</strong>? {{ isset($data["equipment"]) ? "Tak" : "Nie" }}</span>
    <p>{{ $data["wishes"] }}</p>
</div>

@endsection
