@extends('layouts.app', compact("title"))

@section('content')

<x-section id="showcases-list" title="Lista reklam" icon="list">
    <x-slot name="buttons">
        <x-a :href="route('showcase-platforms')" icon="hashtag">Platformy</x-a>
    </x-slot>

    <table>
        <thead>
            <tr>
                <th>Tytuł</th>
                <th>Platforma</th>
                <th>Link</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($showcases as $showcase)
            <tr>
                <td><a href="{{ route('songs', ['search' => $showcase->song_id]) }}">{{ $showcase->song->full_title }}</a></td>
                <td>{!! $showcase->platformData?->icon !!}</td>
                <td>
                    <a href="{{ $showcase->link }}" target="_blank">{{ $showcase->link }}</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="grayed-out">Nie ma żadnych reklam</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $showcases->links() }}
</x-section>

<x-section id="organ-showcases-list" title="Rolki organowe" icon="list">
    <x-slot name="buttons">
        <x-a :href="route('organ-showcase-edit')" icon="plus">Dodaj</x-a>
    </x-slot>

    <table>
        <thead>
            <tr>
                <th>Platforma</th>
                <th>Link</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($organ_showcases as $showcase)
            <tr>
                <td>{!! $showcase->platformData?->icon !!}</td>
                <td>
                    <a href="{{ $showcase->link }}" target="_blank">{{ $showcase->link }}</a>
                </td>
                <td>
                    <a href="{{ route('organ-showcase-edit', ['showcase' => $showcase]) }}">
                        <i class="fas fa-pencil" @popper(Edytuj)></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="grayed-out">Nie ma żadnych rolek</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $organ_showcases->links() }}
</x-section>

<x-section id="client-showcases-list" title="Reklamy klienta" icon="list">
    <form action="{{ route('add-client-showcase') }}" method="POST" class="flex-right">
        @csrf
        <x-select name="song_id" label="Utwór" :options="$all_songs" :small="true" />
        <x-input type="text" name="embed" label="Embed" :small="true" />
        <x-button action="submit" label="Dodaj" icon="plus" :small="true" />
    </form>

    <table>
        <thead>
            <tr>
                <th>Tytuł</th>
                <th>Embed</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($client_showcases as $showcase)
            <tr>
                <td><a href="{{ route('songs', ['search' => $showcase->song_id]) }}">{{ $showcase->song->full_title }}</a></td>
                <td>{!! $showcase->embed ?? "<span></span>" !!}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="grayed-out">Nie ma żadnych reklam</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $client_showcases->links() }}
</x-section>

@endsection
