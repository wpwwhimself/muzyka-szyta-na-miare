@extends("layouts.app", ["title" => "Panel DJa"])

@section("content")

<div>
    <x-button :action="route('dj-gig-mode')" label="Gramy!" icon="microphone" />
    <x-button :action="route('dj-list-songs')" label="Lista utworów" icon="list" small />
</div>

@endsection
