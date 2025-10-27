@extends("layouts.app", ["title" => "Lista utworów"])

@section("content")

<x-section title="Lista utworów" icon="compact-disc">
    <x-slot name="buttons">
        <x-a :href="route('dj-edit-song')" icon="plus">Dodaj</x-a>
    </x-slot>

    @forelse ($songs as $song)
    <a href="{{ route('dj-edit-song', ['id' => $song->id]) }}" class="quest-mini">
        <div class="flex down">
            <h3 class="song-title">{{ $song->title }}</h3>
            <span class="ghost">{{ $song->artist }}</span>
        </div>

        <div class="quest-meta">
            <p>{{ $song->tempo_pretty }}</p>
            <p>{{ $song->sampleSet?->full_name }}</p>
        </div>
    </a>
    @empty
    <span class="grayed-out">Brak utworów</span>
    @endforelse

    {{ $songs->links() }}
</x-section>

<div>
    <x-a :href="route('dj')">Wróć</x-a>
</div>

@endsection
