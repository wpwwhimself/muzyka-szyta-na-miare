@extends("layouts.app")

@section("content")

<x-section title="Stawki" icon="percent">
    <x-slot name="buttons">
        <x-a :href="route('gig-price-rate')" icon="plus">Dodaj</x-a>
    </x-slot>

    <table>
        <thead>
            <tr>
                <th>Nazwa</th>
                <th>Stawka</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rates as $rate)
            <tr>
                <td>{{ $rate->label }}</td>
                <td>{{ _c_(as_pln($rate->value)) }}/h</td>
                <td>
                    <a href="{{ route('gig-price-rate', ['rate' => $rate]) }}">
                        <i class="fas fa-pencil" @popper(Edytuj)></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="grayed-out">Brak stawek</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</x-section>

<x-a :href="route('gig-price-suggest')">Wróć</x-a>

@endsection
