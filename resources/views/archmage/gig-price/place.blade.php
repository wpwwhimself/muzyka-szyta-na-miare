@extends('layouts.app', compact("title"))

@section('content')
<form action="{{ route("gig-price-place-process") }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $place?->id }}">

    <x-section title="Dane miejsca" icon="location-dot">
        <x-input type="text"
            name="name"
            label="Nazwa"
            :value="$place?->name"
        />

        <x-input type="TEXT"
            name="description"
            label="Opis"
            :value="$place?->description"
        />

        <x-input type="number"
            name="distance_km"
            label="Odległość [km]"
            :value="$place?->distance_km"
        />

        <x-button action="submit" name="action" value="save" label="Zatwierdź" icon="check" />
        @if ($place)
        <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" />
        @endif
        <x-a :href="route('gig-price-places')">Wróć</x-a>
    </x-section>
</form>
@endsection
