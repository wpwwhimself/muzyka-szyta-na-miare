@extends('layouts.app')
@section("title", "Zapytania")

@section('content')

<x-section id="requests-list"
    title="Lista zapytań"
    :icon="model_icon('requests')"
>
    <x-slot name="buttons">
        <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe</x-a>
    </x-slot>

    <div class="flex down">
        @forelse ($requests as $request)
        <x-requests.tile :request="$request" />
        @empty
        <p class="grayed-out">brak zapytań</p>
        @endforelse
    </div>

    {{ $requests->links("components.shipyard.pagination.default") }}
</x-section>

@endsection
