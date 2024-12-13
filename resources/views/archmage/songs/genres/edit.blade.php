@extends('layouts.app')

@section('content')
<x-a :href="route('song-genres')" icon="angles-left">Wróć</x-a>

<form action="{{ route("song-genre-process") }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $genre?->id }}">

    <x-section title="Dane gatunku" icon="compact-disc">
        <x-input type="text"
            name="name"
            label="Nazwa"
            :value="$genre?->name"
        />
    </x-section>

    <div class="flex-right center">
        <x-button action="submit" name="action" value="save" label="Zatwierdź" icon="check" />
        @if ($genre)
        <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" />
        @endif
    </div>
</form>
@endsection
