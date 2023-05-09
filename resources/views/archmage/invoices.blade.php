@extends('layouts.app', compact("title"))

@section('content')

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-plus"></i> Dodaj nową</h1>
    </div>
    <form action="{{ route('invoice-add') }}" method="post">
        @csrf
        <div class="grid-3">
            <x-input type="text" name="payer_name" label="Nazwa płatnika" />
            <x-input type="text" name="payer_title" label="Tytuł płatnika" :small="true" />
            <x-input type="TEXT" name="payer_address" label="Adres" />
            <x-input type="text" name="payer_nip" value="" label="NIP" :small="true" />
            <x-input type="text" name="payer_regon" value="" label="REGON" :small="true" />
            <x-input type="text" name="payer_email" label="E-mail" :small="true" />
            <x-input type="text" name="payer_phone" label="Telefon" :small="true" />
            <x-input type="text" name="quests" label="Zlecenia (oddz. przecinkami)" :small="true" />
        </div>
        <x-button action="submit" label="Dodaj" icon="check" />
    </form>
</section>

<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-file-invoice"></i>
            Lista faktur
        </h1>
    </div>
    <table>
        <thead>
            <tr>
                <th>Faktura</th>
                <th>Dotyczy</th>
                <th>Kwota</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($invoices as $invoice)
            <tr>
                <td>
                    <a href="{{ route('invoice', ['id' => $invoice->id]) }}">
                        <i class="fa-solid fa-{{ $invoice->visible ? 'file-invoice' : 'eye-slash' }}"></i>
                        {{ $invoice->fullCode }}
                    </a>
                </td>
                <td>
                @foreach ($invoice->quests as $quest)
                    <a href="{{ route('quest', ['id' => $quest->id]) }}">{{ $quest->id }}</a>
                @endforeach
                </td>
                <td class="{{ $invoice->isPaid ? '' : 'error' }}">{{ as_pln($invoice->amount) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan=3><span class="grayed-out">Brak faktur</span></td>
            </tr>
        @endforelse
        </tbody>
    </table>
</section>

@endsection
