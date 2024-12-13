@extends('layouts.app', compact("title"))

@section('content')

<x-section id="requests-list"
    title="Lista zapytań"
    icon="envelope-open-text"
>
    <x-slot name="buttons">
        <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe</x-a>
    </x-slot>

    @forelse ($requests as $request)
    <x-quest-mini :quest="$request" />
    @empty
    <p class="grayed-out">brak zapytań</p>
    @endforelse
    
    {{ $requests->links() }}
</x-section>

@endsection
