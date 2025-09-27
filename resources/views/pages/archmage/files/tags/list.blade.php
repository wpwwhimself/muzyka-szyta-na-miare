@extends('layouts.app', compact("title"))

@section("content")

<x-section title="Tagi" icon="tag">
    <x-slot name="buttons">
        <x-a :href="route('file-tag-edit')" icon="plus">Dodaj</x-a>
    </x-slot>

    <div class="flex-right center wrap">
        @forelse ($tags as $tag)
        <div class="flex-down center">
            <x-file-tag :tag="$tag" onclick="window.location.href = '{{ route('file-tag-edit', ['id' => $tag->id]) }}'" />
            {{-- <x-a :href="route('file-tag-edit', ['id' => $tag->id])" icon="pen">Edytuj</x-a> --}}
        </div>
        @empty
        <span class="grayed-out">Brak utworzonych tagów</span>
        @endforelse
    </div>
</x-section>

@endsection
