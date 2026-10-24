@extends("layouts.mail")
@section("title", "Nowe zapytanie dla organisty")

@section("content")

<x-client.contact-info :data="$data" />

<span><strong>Rodzaj uroczystości</strong>: {{ $data["occasion"] }}</span>
<br />
<span><strong>Data i miejsce</strong>: {{ $data["date"] }}, {{ $data["place"] }}</span>
<br />
<span><strong>Skład</strong>: {{ $data["people"] }}</span>
<br />
<span><strong>Mój sprzęt</strong>? {{ isset($data["equipment"]) ? "Tak" : "Nie" }}</span>
<br />
<span><strong>Życzenia</strong>: {{ $data["wishes"] }}</span>

@endsection
