@extends("shipyard::layouts.admin")
@section('title', "Sejfy")
@section("subtitle", "Statystyki")

@section('content')

<x-shipyard::app.section
    title="Rozmiary poszczególnych sejfów"
    subtitle="w megabajtach"
    :icon="model_icon('files')"
>
    <x-slot:actions>
        <x-shipyard::stats.tile
            label="Wszystkie sejfy"
            :value="$summary_size"
        />
    </x-slot:actions>

    <x-shipyard::stats.chart.bar :data="$sizes" />
</x-shipyard::app.section>

@endsection
