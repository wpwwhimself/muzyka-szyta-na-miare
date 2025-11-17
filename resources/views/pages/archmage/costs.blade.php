@extends('layouts.app')
@section("title", "Koszty")
@section("subtitle", "Centrum finansowe")

@section('content')

<div class="flex right center middle">
    <x-shipyard.ui.button :action="route('cost-types')" label="Typy" :icon="model_icon('cost-types')" />
    <x-shipyard.ui.button :action="route('finance')" label="Wróć" icon="chevron-double-left" />
    <x-shipyard.ui.button :action="route('finance-summary')" label="Podsumowanie" icon="finance" />
</div>

<x-section title="Zapisane koszty" :icon="model_icon('cost-types')">
    <x-slot:buttons>
        <x-shipyard.ui.button
            label="Dodaj"
            icon="plus"
            action="none"
            onclick="openModal('mod-cost', {
                created_at: '{{ date('Y-m-d') }}',
            })"
            class="tertiary"
        />
    </x-slot:buttons>

    <x-stats-highlight-h :data="$summary" :all-pln="true" />

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Typ</th>
                <th>Opis</th>
                <th>Kwota</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($costs as $cost)
            <tr>
                <td>{{ $cost->date->format('d.m.Y') }}</td>
                <td>{{ $cost->typable->name }}</td>
                <td>{{ $cost->description }}</td>
                <td>{{ as_pln($cost->amount) }}</td>
                <td>
                    <x-shipyard.ui.button
                        icon="pencil"
                        pop="Edytuj"
                        action="none"
                        onclick="openModal('mod-cost', {
                            id: '{{ $cost->id }}',
                            created_at: '{{ $cost->date->format('Y-m-d') }}',
                            cost_type_id: '{{ $cost->typable->id }}',
                            desc: '{{ $cost->description }}',
                            amount: '{{ $cost->amount }}',
                        })"
                        class="tertiary"
                    />
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $costs->links("components.shipyard.pagination.default") }}
</x-section>

@endsection
