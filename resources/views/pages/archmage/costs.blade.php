@extends('layouts.app')
@section("title", "Koszty")
@section("subtitle", "Centrum finansowe")

@section('content')

<div class="flex right center middle">
    <x-shipyard.ui.button :action="route('cost-types')" label="Typy" :icon="model_icon('cost-types')" />
    <x-shipyard.ui.button :action="route('finance')" label="Wróć" icon="chevron-double-left" />
    <x-shipyard.ui.button :action="route('finance-summary')" label="Podsumowanie" icon="finance" />
</div>

<x-section title="Zapisane koszty" :icon="model_icon('costs')">
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
                <td>{{ $cost->created_at->format('d.m.Y') }}</td>
                <td>{{ $cost->type->name }}</td>
                <td>{{ $cost->desc }}</td>
                <td>{{ as_pln($cost->amount) }}</td>
                <td>
                    <x-shipyard.ui.button
                        icon="pencil"
                        pop="Edytuj"
                        action="none"
                        onclick="openModal('mod-cost', {
                            id: '{{ $cost->id }}',
                            created_at: '{{ $cost->created_at->format('Y-m-d') }}',
                            type: '{{ $cost->cost_type_id }}',
                            desc: '{{ $cost->desc }}',
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
