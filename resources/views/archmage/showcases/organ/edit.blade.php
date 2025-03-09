@extends('layouts.app', ["title" => "Edycja rolki organowej"])

@section('content')

<form action="{{ route('organ-showcase-process') }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $showcase?->id }}">

    <x-section title="Dane rolki" icon="hashtag">
        <x-select name="platform" label="Platforma" :options="$showcase_platforms" :value="$showcase?->platform ?? $platform_suggestion" />
        <x-input type="url" name="link" label="Link" :value="$showcase?->link" small />

        <div>
            <x-button action="submit" name="action" value="save" label="Zapisz" icon="check" />
            @if ($showcase) <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" danger /> @endif
        </div>
    </x-section>
</form>

<x-section title="Opis" icon="text">
    <x-showcases.description for="organista" />
</x-section>

@endsection
