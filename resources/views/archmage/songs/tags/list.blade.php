@extends('layouts.app', compact("title"))

@section('content')
<x-section title="Lista tagów" icon="tag">
    <x-slot name="buttons">
        <x-a :href="route('song-tag-edit')" icon="plus">Dodaj</x-a>
    </x-slot>

    <div class="flex-right wrap">
        @forelse ($tags as $tag)
        <x-a :href="route('song-tag-edit', ['id' => $tag->id])" icon="tag">{{ $tag->name }}</x-a>
        @empty
        <p class="grayed-out">Brak utworzonych tagów</p>
        @endforelse
    </div>
</x-section>
@endsection
