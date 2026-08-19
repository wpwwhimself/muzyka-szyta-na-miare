@extends("layouts.mail")
@section("title", "Nowe zapytanie o DJa")

@section("content")

<x-client.contact-info :data="$data" />

<span><strong>Rodzaj imprezy</strong>: {{ $data["occasion"] }}</span>
<br />
<span><strong>Data</strong>: {{ $data["date"] }}</span>
<br />
<span><strong>Życzenia</strong>: {{ $data["wishes"] }}</span>

@endsection
