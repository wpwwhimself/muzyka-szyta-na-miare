@extends("layouts.app", ["title" => "Panel DJa"])

@section("content")

<div class="flex-right">
    <x-button :action="route('dj-gig-mode')" label="Gramy!" icon="microphone" />
</div>

<div>
    <x-button :action="route('dj-list-songs')" label="Lista utworów" icon="list" small />
    <x-button :action="route('dj-list-sets')" label="Lista zestawów" icon="list" small />
</div>

@endsection
