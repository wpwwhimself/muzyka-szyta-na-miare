@extends('layouts.app', compact("title"))

@section('content')

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-plus"></i> Dodaj nową</h1>
    </div>
    <form action="{{ route('invoice-add') }}" method="post">
        @csrf
        <div class="grid" style="--col-count: 3;">
            <x-input type="text" name="payer_name" label="Nazwa płatnika" value="{{ _ct_($client?->client_name) }}" />
            <x-input type="text" name="payer_title" label="Tytuł płatnika" :small="true" />
            <x-input type="text" name="payer_address" label="Adres" />
            <x-input type="text" name="payer_nip" value="" label="NIP" :small="true" />
            <x-input type="text" name="payer_regon" value="" label="REGON" :small="true" />
            <x-input type="text" name="payer_email" label="E-mail" :small="true" value="{{ _ct_($client?->email) }}" />
            <x-input type="text" name="payer_phone" label="Telefon" :small="true" value="{{ _ct_($client?->phone) }}" />
            <x-input type="text" name="quests" label="Zlecenia (oddz. spacjami)" :small="true" value="{{ $quest_id }}" />
        </div>
        <input type="hidden" id="id" name="id" value="" />
        <x-button action="submit" id="invoice-add-btn" label="Dodaj" icon="check" />
        <x-button action="submit" id="invoice-edit-btn" label="Popraw" icon="pencil" />
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
                <th>Płatnik</th>
                <th>Dotyczy</th>
                <th>Kwota</th>
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
                    <i class="fas fa-pencil invoice-edit interactive" data-invoice-id="{{ $invoice->id }}"></i>
                </td>
                <td>
                    {{ $invoice->payer_name }}
                    <span class="ghost">{{ $invoice->payer_title }}</span>
                </td>
                <td>
                @foreach ($invoice->quests as $quest)
                    <a href="{{ route('quest', ['id' => $quest->id]) }}">{{ $quest->id }}</a>
                @endforeach
                </td>
                <td class="{{ $invoice->isPaid ? '' : 'error' }}">{{ _c_(as_pln($invoice->amount)) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan=3><span class="grayed-out">Brak faktur</span></td>
            </tr>
        @endforelse
        </tbody>
    </table>
</section>

<script>
$(document).ready(function() {
    $("#invoice-edit-btn").hide()

    $(".invoice-edit").click(function() {
        fetch("/api/invoice/" + $(this).attr("data-invoice-id"))
            .then(res => res.json())
            .then(res => {
                const invoice = res.invoice

                $("#invoice-edit-btn").show()
                $("#invoice-add-btn").hide()
                for (let field of [
                    "id",
                    "payer_name",
                    "payer_title",
                    "payer_address",
                    "payer_nip",
                    "payer_regon",
                    "payer_email",
                    "payer_phone",
                ]) {
                    $("#" + field).val(invoice[field])
                }
                $("#quests").val(invoice.quests.map(q => q.id).join(" "))
            })
    })
})
</script>

@endsection
