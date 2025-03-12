@extends("layouts.app", ["title" => "Panel DJa"])

@section("content")

<div class="flex-right">
    <x-button :action="route('dj-list-songs')" label="Lista utworów" icon="list" />
</div>

@endsection
