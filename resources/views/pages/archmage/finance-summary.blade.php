@extends('layouts.app')

@section('content')

<div class="flex right center middle">
    <x-shipyard.ui.button :action="route('finance')" label="Wróć" icon="chevron-double-left" />
    <x-shipyard.ui.button :action="route('admin.model.list', ['model' => 'money-transactions', 'fltr[type]' => 'App\\Models\\CostType'])" label="Koszty" :icon="model_icon('cost-types')" />
    <x-shipyard.ui.button :action="route('taxes')" label="Podatki" icon="cash-register" />

    <x-shipyard.ui.button :action="route('finance-payout', ['amount' => $summary['Można wypłacić']])"
        icon="cash-refund"
        label="Wypłać"
        :pop="'Doda wypłatę w kwocie '.$summary['Można wypłacić'].' zł'"
        class="primary"
    />
</div>

<x-section scissors :title="'Podsumowanie – '.\Carbon\Carbon::today()->subMonths(request()->get('subMonths', 0))->format('m.Y')" icon="finance">
    <x-slot:buttons>
    @if (request()->get("subMonths"))
    <x-a href="{{ route('finance-summary') }}">Ten miesiąc</x-a>
    @else
    <x-a href="{{ route('finance-summary', ['subMonths' => 1]) }}">Poprzedni miesiąc</x-a>
    @endif
    </x-slot:buttons>

    <x-stats-highlight-h :data="$summary" :all-pln="true" />
</x-section>

<x-section title="Wpływy" icon="cash-plus">
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Klient</th>
                <th>Zlecenie</th>
                <th>Faktura</th>
                <th>Kwota</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($gains as $pos)
            <tr>
                <td>{{ $pos->date->format("d.m.Y") }}</td>
                <td>
                    @if ($pos->relatable)
                    <a href="{{ route('client-view', ['id' => $pos->relatable->user?->id ?? $pos->relatable->id]) }}">
                        {!! $pos->relatable->user ?? $pos->relatable !!}
                    </a>
                    @endif
                </td>
                <td>
                    @if ($pos->relatable)
                    @if ($pos->relatable instanceof \App\Models\Quest)
                    <a href="{{ route('quest', ['id' => $pos->relatable->id]) }}">{{ $pos->relatable }}</a>
                    @else
                    <span class="grayed-out">budżet</span>
                    @endif
                    @else
                    {{ $pos->description }}
                    @endif
                </td>
                <td>
                    @if ($pos->invoice->first())
                    <a href="{{ route('invoice', ['id' => $pos->invoice->first()->id]) }}">{{ $pos->invoice->first()->full_code }}</a>
                    @endif
                </td>
                <td>{{ _c_(as_pln($pos->amount)) }}</td>
            </tr>
            @empty
            <tr><td class="grayed-out">Brak danych</td></tr>
            @endforelse
        </tbody>
    </table>
</x-section>

<x-section title="Wydatki" icon="cash-minus">
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Typ</th>
                <th>Opis</th>
                <th>Kwota</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($losses as $pos)
            <tr>
                <td>{{ $pos->date->format("d.m.Y") }}</td>
                <td>{{ _ct_($pos->typable->name) }}</td>
                <td>
                    @if ($pos->relatable)
                    <a href="{{ route('quest', ['id' => $pos->relatable->id]) }}">{{ $pos->relatable }}</a>
                    @endif
                    {{ $pos->description }}
                </td>
                <td>{{ _c_(as_pln($pos->amount)) }}</td>
            </tr>
            @empty
            <tr><td class="grayed-out">Brak danych</td></tr>
            @endforelse
        </tbody>
    </table>
</x-section>

@endsection
