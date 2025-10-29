@extends('layouts.app')
@section('title', $invoice->full_code)
@section("subtitle", "Podgląd faktury")

@section('content')

<x-invoice :invoice="$invoice" />

<form action="{{ route('invoice-visibility') }}" method="post" class="flex right hide-for-print">
    @csrf
    <input type="hidden" name="id" value="{{ $invoice->id }}" />
    <input type="hidden" name="visible" value="{{ intval(!$invoice->visible) }}" />
    <x-shipyard.ui.button action="submit"
        :icon="$invoice->visible ? 'eye-off' : 'eye'"
        :label="$invoice->visible ? 'Ukryj' : 'Pokaż'"
    />

    <x-shipyard.ui.button :action="route('invoices')"
        icon="chevron-left" label="Wróć do faktur"
    />

    <x-shipyard.ui.button action="none" onclick="printInvoice();"
        icon="download" label="Drukuj"
    />
</form>

@endsection
