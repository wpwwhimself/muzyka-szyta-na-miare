@extends("layouts.app")

@section("content")

<script src="{{ mix('js/workTimeClock.js') }}" defer></script>
<div id="clock"></div>

<x-a :href="$data->linkTo">Wróć</x-a>

@endsection
