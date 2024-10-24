@extends('layouts.app')

@section('content')
<form action="{{ route("song-process") }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $song->id }}">

    <x-section title="Dane utworu" icon="compact-disc">
        <div class="flex-right center">
            @foreach ([
                ["text", "title", "Tytuł"],
                ["text", "artist", "Wykonawca"],
                ["text", "link", "Link"],
                ["TEXT", "notes", "Notatki"],
                ["text", "price_code", "Kod wyceny"],
            ] as [$type, $name, $label])
            <x-input :type="$type"
                :name="$name"
                :label="$label"
                :value="$song->{$name}"
            />
            @endforeach
            
            <x-select
                name="genre_id" label="Gatunek"
                :value="$song->genre_id"
                :options="$genres"
            />
        </div>
    </x-section>

    <x-button action="submit" label="Popraw dane" icon="pencil" />
</form>
@endsection
