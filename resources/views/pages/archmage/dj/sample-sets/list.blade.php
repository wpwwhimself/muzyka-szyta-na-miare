@extends("layouts.app", ["title" => "Lista sampli"])

@section("content")

<x-section title="Lista sampli" icon="list">
    <x-slot name="buttons">
        <x-a :href="route('dj-edit-sample-set')" icon="plus">Dodaj</x-a>
    </x-slot>

    @forelse ($sets as $set)
    <a href="{{ route('dj-edit-sample-set', ['id' => $set->id]) }}" class="quest-mini">
        <div class="flex-down">
            <h3 class="song-title">
                {{ $set->name }}
                <small class="ghost">{{ $set->id }}</small>
            </h3>
            <span class="ghost">{{ $set->description }}</span>
        </div>

        <div class="quest-meta">
            <p>{{ $set->songs->count() }} utworów</p>
        </div>
    </a>
    @empty
    <span class="grayed-out">Brak sampli</span>
    @endforelse

    {{ $sets->links() }}
</x-section>

<div>
    <x-a :href="route('dj')">Wróć</x-a>
</div>

@endsection
