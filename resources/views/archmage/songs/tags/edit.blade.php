@extends('layouts.app')

@section('content')
<x-a :href="route('song-tags')" icon="angles-left">Wróć</x-a>

<form action="{{ route("song-tag-process") }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $tag?->id }}">

    <x-section title="Dane taga" icon="compact-disc">
        <x-input type="text"
            name="name"
            label="Nazwa"
            :value="$tag?->name"
        />

        <x-input type="TEXT"
            name="description"
            label="Opis"
            :value="$tag?->description"
        />
    </x-section>

    <div class="flex-right center">
        <x-button action="submit" name="action" value="save" label="Zatwierdź" icon="check" />
        @if ($tag)
        <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" />
        @endif
    </div>
</form>
@endsection
