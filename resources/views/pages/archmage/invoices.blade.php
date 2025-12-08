@extends('layouts.app')
@section("title", "Faktury")

@section('content')

<x-section title="Lista faktur" :icon="model_icon('invoices')">
    <x-slot:buttons>
        <x-shipyard.ui.button
            label="Dodaj nową"
            icon="plus"
            action="none"
            onclick="openModal('edit-invoice', {});"
            class="tertiary"
        />
    </x-slot:buttons>

    <table>
        <thead>
            <tr>
                <th>Faktura</th>
                <th>Płatnik</th>
                <th>Dotyczy</th>
                <th>Kwota</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($invoices as $invoice)
            <tr>
                <td class="invoice-number">
                    <a href="{{ route('invoice', ['id' => $invoice->id]) }}">
                        <i class="fa-solid fa-{{ $invoice->visible ? 'file-invoice' : 'eye-slash' }}"></i>
                        {{ $invoice->fullCode }}
                    </a>
                </td>
                <td>
                    {{ $invoice->payer_name }}
                    <span class="ghost">{{ $invoice->payer_title }}</span>
                    @if (!$invoice->visible)
                    <span class="accent error" @popper(Klient nie widzi faktury)>
                        <x-shipyard.app.icon name="eye-off" />
                    </span>
                    @endif
                </td>
                <td>
                @foreach ($invoice->quests as $quest)
                    <a href="{{ route('quest', ['id' => $quest->id]) }}">{{ $quest->id }}</a>
                @endforeach
                </td>
                <td class="{{ $invoice->isPaid ? '' : 'error' }}">{{ _c_(as_pln($invoice->amount)) }}</td>
                <td>
                    <x-shipyard.ui.button
                        pop="Edytuj"
                        icon="pencil"
                        action="none"
                        onclick="openModal('edit-invoice', {
                            id: {{ $invoice->id }},
                            payer_name: '{{ $invoice->payer_name }}',
                            payer_title: '{{ $invoice->payer_title }}',
                            payer_address: '{{ $invoice->payer_address }}',
                            payer_nip: '{{ $invoice->payer_nip }}',
                            payer_regon: '{{ $invoice->payer_regon }}',
                            payer_email: '{{ $invoice->payer_email }}',
                            payer_phone: '{{ $invoice->payer_phone }}',
                            quests: '{{ $invoice->quests->pluck('id')->implode(' ') }}',
                        });"
                        class="tertiary"
                    />
                    <x-shipyard.ui.button
                        pop="Utwórz nową na tego samego płatnika"
                        icon="content-duplicate"
                        action="none"
                        onclick="openModal('edit-invoice', {
                            payer_name: '{{ $invoice->payer_name }}',
                            payer_title: '{{ $invoice->payer_title }}',
                            payer_address: '{{ $invoice->payer_address }}',
                            payer_nip: '{{ $invoice->payer_nip }}',
                            payer_regon: '{{ $invoice->payer_regon }}',
                            payer_email: '{{ $invoice->payer_email }}',
                            payer_phone: '{{ $invoice->payer_phone }}',
                        });"
                        class="tertiary"
                    />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan=3><span class="grayed-out">Brak faktur</span></td>
            </tr>
        @endforelse
        </tbody>
    </table>
</x-section>

@endsection
