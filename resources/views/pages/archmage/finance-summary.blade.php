@extends('layouts.app')

@section('content')

<div class="flex right center middle">
    <x-shipyard.ui.button :action="route('finance')" label="Wróć" icon="chevron-double-left" />
    <x-shipyard.ui.button :action="route('costs')" label="Koszty" :icon="model_icon('costs')" />
    <x-shipyard.ui.button :action="route('taxes')" label="Podatki" icon="cash-register" />

    <x-shipyard.ui.button :action="route('finance-payout', ['amount' => $summary['Można wypłacić']])" label="Wypłać" icon="sack-dollar" small />
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
                <td><a href="{{ route('clients', ['search' => $pos->changed_by]) }}">{!! $pos->changer !!}</a></td>
                <td>
                    @if ($pos->re_quest_id)
                    <a href="{{ route('quest', ['id' => $pos->re_quest_id]) }}">{{ $pos->re_quest_id }}</a>
                    @else
                    <span class="grayed-out">budżet</span>
                    @endif
                </td>
                <td>
                    @if ($pos->invoice->first())
                    <a href="{{ route('invoice', ['id' => $pos->invoice->first()->id]) }}">{{ $pos->invoice->first()->full_code }}</a>
                    @endif
                </td>
                <td>{{ _c_(as_pln($pos->comment)) }}</td>
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
                @if (get_class($pos) == "App\Models\Cost")
                <td>{{ $pos->created_at->format("d.m.Y") }}</td>
                <td>{{ _ct_($pos->type->name) }}</td>
                <td>{{ $pos->desc }}</td>
                <td>{{ _c_(as_pln($pos->amount)) }}</td>
                @else
                <td>{{ $pos->date->format("d.m.Y") }}</td>
                <td>zwrot wpłaty</td>
                <td><a href="{{ route('quest', ['id' => $pos->re_quest_id]) }}">{{ $pos->re_quest_id }}</a></td>
                <td>{{ _c_(as_pln(-$pos->comment)) }}</td>
                @endif
            </tr>
            @empty
            <tr><td class="grayed-out">Brak danych</td></tr>
            @endforelse
        </tbody>
    </table>
</x-section>

@endsection
