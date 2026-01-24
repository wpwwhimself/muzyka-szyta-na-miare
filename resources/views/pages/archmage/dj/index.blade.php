@extends("layouts.app")
@section("title", "Panel DJa")

@section("content")

<div class="flex right center middle">
    <x-shipyard.ui.button :action="route('dj-gig-mode')"
        label="Panel DJa"
        icon="headphones"
        class="primary"
    />
    <x-shipyard.ui.button :action="route('dj-lottery-mode')"
        label="Loteria koncertowa"
        icon="slot-machine"
        class="primary"
    />
</div>

<x-shipyard.app.card
    title="Zarządzanie danymi DJa"
    :icon="model_icon('dj-songs')"
>
    <x-shipyard.ui.button :action="route('dj-list-songs')" label="Utwory" :icon="model_icon('dj-songs')" />
    <x-shipyard.ui.button :action="route('dj-list-sample-sets')" label="Sample" :icon="model_icon('dj-sample-sets')" />
    <x-shipyard.ui.button :action="route('dj-list-sets')" label="Zestawy" :icon="model_icon('dj-sets')" />
</x-shipyard.app.card>

@endsection
