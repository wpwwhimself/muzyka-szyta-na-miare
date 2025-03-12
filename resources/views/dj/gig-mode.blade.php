@extends("layouts.app", ["title" => "Gig mode", "stripped" => true])

@section("content")

<script src="{{ mix('js/react/gigMode.js') }}" defer></script>
<div id="container"></div>
<x-button :action="route('dj')" icon="angles-left" label="Wyjdź" danger />

@endsection
