@extends("layouts.app", ["title" => "Lista utworów"])

@section("content")

<x-section title="Lista utworów" icon="compact-disc">
    <x-slot name="buttons">
        <x-a :href="route('dj-edit-song')" icon="plus">Dodaj</x-a>
    </x-slot>

    @forelse ($songs as $song)
    <a href="{{ route('dj-edit-song', ['id' => $song->id]) }}">{{ $song->full_title }}</a>
    @empty
    <span class="grayed-out">Brak utworów</span>
    @endforelse
</x-section>

<div>
    <x-a :href="route('dj')">Wróć</x-a>
</div>

@endsection
