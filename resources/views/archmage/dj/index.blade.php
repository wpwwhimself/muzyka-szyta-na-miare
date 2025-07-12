@extends("layouts.app", ["title" => "Panel DJa"])

@section("content")

<div class="flex-right">
    <x-button :action="route('dj-gig-mode')" label="Gramy!" icon="microphone" />
</div>

<div>
    <x-button :action="route('dj-list-songs')" label="Utwory" icon="compact-disc" small />
    <x-button :action="route('dj-list-sets')" label="Zestawy" icon="list" small />
</div>

@endsection
