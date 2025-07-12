@extends("layouts.app", ["title" => "Gig mode", "stripped" => true])

@section("content")

<script src="{{ mix('js/react/gigMode.js') }}?{{ time() }}" defer></script>
<div id="container"></div>
<x-button :action="request('song')
        ? route('dj-edit-song', ['id' => request('song')])
        : route('dj')
    "
    icon="angles-left"
    label="Wyjdź"
    danger
/>

@endsection
