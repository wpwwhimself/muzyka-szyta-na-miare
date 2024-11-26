@extends('layouts.app', compact("title"))

@section("content")

<x-section title="Tagi" icon="tag">
    <x-slot name="buttons">
        <x-a :href="route('file-tag-edit')" icon="plus">Dodaj</x-a>
    </x-slot>

    <table>
        <thead>
            <tr>
                <th>Tag</th>
                <th>Nazwa</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tags as $tag)
            <tr>
                <td><x-file-tag :tag="$tag" /></td>
                <td>{{ $tag->name }}</td>
                <td>
                    <x-a :href="route('file-tag-edit', ['id' => $tag->id])" icon="pen">Edytuj</x-a>
                </td>
            </tr>
            @empty
            <tr><td colspan=3><span class="grayed-out">Brak utworzonych tagów</span></td></tr>
            @endforelse
        </tbody>
    </table>
</x-section>

@endsection