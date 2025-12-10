@extends('layouts.app')
@section('title', $invoice->full_code)
@section("subtitle", "Podgląd faktury")

@section('content')

<x-invoice :invoice="$invoice" />

<div class="flex right hide-for-print">
    <x-shipyard.ui.button action="none" onclick="printInvoice();"
        icon="download" label="Drukuj" class="tertiary"
    />
</div>

@endsection
