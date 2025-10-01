@extends('layouts.app', ["title" => "Platformy"])

@section('content')

<x-section title="Platformy" icon="hashtag">
    <x-slot name="buttons">
        <x-a :href="route('showcase-platform-edit')" icon="plus">Dodaj</x-a>
    </x-slot>

    <div class="flex-right center">
        @forelse ($platforms as $platform)
        <div class="flex down center">
            <a href="{{ route('showcase-platform-edit', ['id' => $platform->code]) }}">
                {!! $platform !!}
            </a>
        </div>
        @empty
        <span class="grayed-out">Brak utworzonych platform</span>
        @endforelse
    </div>
</x-section>

@endsection
