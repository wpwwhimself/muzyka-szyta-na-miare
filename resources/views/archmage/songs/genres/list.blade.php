@extends('layouts.app', compact("title"))

@section('content')
<x-section title="Lista gatunków" icon="radio">
    <x-slot name="buttons">
        <x-a :href="route('song-genre-edit')" icon="plus">Dodaj</x-a>
    </x-slot>

    <div class="flex-right wrap">
        @forelse ($genres as $genre)
        <x-a :href="route('song-genre-edit', ['id' => $genre->id])" icon="radio">{{ $genre->name }}</x-a>
        @empty
        <p class="grayed-out">Brak utworzonych gatunków... Dziwne...</p>
        @endforelse
    </div>
</x-section>
@endsection
