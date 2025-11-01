@extends('layouts.app')
@section("title", "Edycja rolki organowej")

@section('content')

<x-shipyard.app.form :action="route('organ-showcase-process')" method="POST">
    <input type="hidden" name="id" value="{{ $showcase?->id }}">

    <x-section title="Dane rolki" :icon="model_icon('organ-showcases')">
        <x-shipyard.ui.input type="select"
            name="platform"
            label="Platforma"
            :icon="model_icon('showcase-platforms')"
            :select-data="[
                'options' => $showcase_platforms,
            ]"
            :value="$showcase?->platform ?? $platform_suggestion['code']"
        />
        <x-shipyard.ui.input type="url"
            name="link"
            label="Link"
            icon="link"
            :value="$showcase?->link"
        />
    </x-section>

    <x-slot:actions>
        <x-shipyard.ui.button
            label="Zapisz" icon="check"
            action="submit"
            name="action" value="save"
            class="primary"
        />
        @if ($showcase)
        <x-shipyard.ui.button
            action="submit"
            name="action" value="delete"
            label="Usuń"
            icon="delete"
            class="danger"
        />
        @endif
    </x-slot:actions>
</x-shipyard.app.form>

<x-section title="Opis" icon="text">
    <x-showcases.description for="organista" />
</x-section>

@endsection
