@extends("layouts.app")

@section("content")

<x-section title="Miejsca" icon="location-dot">
    <x-slot name="buttons">
        <x-a :href="route('gig-price-place')" icon="plus">Dodaj</x-a>
    </x-slot>

    <table>
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Opis</th>
                <th>Odległość [km]</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($places as $place)
            <tr>
                <td>{{ $place->name }}</td>
                <td>{{ $place->description }}</td>
                <td>{{ $place->distance_km }} km</td>
                <td>
                    <a href="{{ route('gig-price-place', ['place' => $place]) }}">
                        <i class="fas fa-pencil" @popper(Edytuj)></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="grayed-out">Brak miejsc</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</x-section>

<x-a :href="route('gig-price-suggest')">Wróć</x-a>

@endsection
