@extends('layouts.app', compact("title"))

@section('content')

<form action="{{ route('file-tag-process') }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $tag?->id }}" />

    <x-section title="Dane taga" icon="tag">
        <x-input type="text"
            name="name"
            label="Nazwa"
            :value="$tag?->name"
        />

        <x-input type="text"
            name="icon"
            label="Ikona"
            :value="$tag?->icon"
            small
        />

        <x-input type="color"
            name="color"
            label="Kolor"
            :value="$tag?->color"
            small
        />

        <x-button action="submit" name="action" value="save" label="Zatwierdź" icon="check" />
        @if ($tag)
        <x-button action="submit" danger name="action" value="delete" label="Usuń" icon="trash" />
        @endif
        <x-a :href="route('file-tags')">Wróć</x-a>
    </x-section>
</form>

@endsection
