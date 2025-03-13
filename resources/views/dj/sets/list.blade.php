@extends("layouts.app", ["title" => "Lista zestawów"])

@section("content")

<x-section title="Lista zestawów" icon="list">
    <x-slot name="buttons">
        <x-a :href="route('dj-edit-set')" icon="plus">Dodaj</x-a>
    </x-slot>

    @forelse ($sets as $set)
    <a href="{{ route('dj-edit-set', ['id' => $set->id]) }}" class="quest-mini">
        <div class="flex-down">
            <h3 class="song-title">{{ $set->name }}</h3>
            <span class="ghost">{{ $set->description }}</span>
        </div>

        <div class="quest-meta">
            <p>{{ $set->songs->count() }} utworów</p>
        </div>
    </a>
    @empty
    <span class="grayed-out">Brak zestawów</span>
    @endforelse

    {{ $sets->links() }}
</x-section>

<div>
    <x-a :href="route('dj')">Wróć</x-a>
</div>

@endsection
