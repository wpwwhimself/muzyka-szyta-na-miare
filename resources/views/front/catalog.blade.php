@extends("layouts.app-front")
@section("title", "Katalog utworów")
@section("subtitle", setting("app_name"))

@section("content")

<x-front.song-list.popup />

<div class="backdropped rounded">
    <x-front.song-list.section for="podklady" />
</div>

@endsection
