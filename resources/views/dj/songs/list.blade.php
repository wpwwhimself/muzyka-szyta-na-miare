@extends("layouts.app", ["title" => "Lista utworów"])

@section("content")

<x-section title="Lista utworów" icon="compact-disc">
    @forelse ($songs as $song)
    <a href="{{ $song->id }}">{{ $song->full_title }}</a>
    @empty
    <span class="grayed-out">Brak utworów</span>
    @endforelse
</x-section>

@endsection
