@extends('layouts.app', compact("title"))

@section('content')

<div id="invoice">
    <h1>Faktura nr {{ $invoice->fullCode() }}</h1>
    <div class="dates grid-2 name-value">
        <span>Data wystawienia:</span>
        <span>{{ $invoice->created_at->format("Y-m-d") }}</span>
    </div>
    <div class="grid-2">
        <section class="account">
            <h3>Sprzedawca</h3>
            <h2>Wojciech Przybyła</h2>
            <h3>Muzyka Szyta Na Miarę</h3>
            <div class="grid-2 name-value">
                <span>e-mail:</span>
                <span>{{ env("MAIL_FROM_ADDRESS") }}</span>
                <span>tel:</span>
                <span>530 268 000</span>
                <span>nr konta:</span>
                <span>53 1090 1607 0000 0001 1633 2919</span>
            </div>
        </section>
        <section class="account">
            <h3>Nabywca</h3>
            <h2>{{ $invoice->quest->client->client_name }}</h2>
            <div class="grid-2 name-value">
                @if ($invoice->quest->client->invoice_address)
                    <span>adres zamieszkania:</span>
                    <span>{{ $invoice->quest->client->invoice_address }}</span>
                @endif
                @if($invoice->quest->client->email)
                    <span>e-mail:</span>
                    <span>{{ $invoice->quest->client->email }}</span>
                @endif
                @if($invoice->quest->client->phone)
                    <span>tel:</span>
                    <span>{{ number_format($invoice->quest->client->phone, 0, ",", " ") }}</span>
                @endif
            </div>
        </section>
    </div>

    <div class="quests-table section-like">
        <style>
        .table-row{ grid-template-columns: auto auto; }
        .table-row span:last-child{ text-align: right; }
        </style>
        <div class="table-header table-row">
            <span>Nazwa usługi</span>
            <span>Cena</span>
        </div>
        <div class="table-row">
            <span>
                @switch(song_quest_type($invoice->quest->song_id)->id)
                    @case(1) Przygotowanie podkładu muzycznego @break
                    @case(2) Przygotowanie nut @break
                    @case(3) Obróbka nagrania @break
                    @default Przygotowanie materiałów muzycznych
                @endswitch
                do utworu:
                @if ($invoice->quest->song->artist)
                {{ $invoice->quest->song->artist }} –
                @endif
                <em>{{ $invoice->quest->song->title ?? "bez tytułu" }}</em>
            </span>
            <span>
                {{ number_format($invoice->amount, 2, ",", " ") }} zł
            </span>
        </div>
    </div>

    <div class="grid-2 name-value summary">
        <span>Razem do zapłaty:</span>
        <span>{{ number_format($invoice->amount, 2, ",", " ") }} zł</span>
        @if ($invoice->paid)
        <span>Płatność otrzymana:</span>
        <span>{{ number_format($invoice->paid, 2, ",", " ") }} zł</span>
        <span>Pozostało do zapłaty:</span>
        <span @if ($invoice->isPaid()) class="success" @endif>{{ number_format($invoice->amount - $invoice->paid, 2, ",", " ") }} zł</span>
        @endif
    </div>
</div>

<form action="{{ route('invoice-visibility') }}" method="post" class="flex-right">
    @csrf
    <input type="hidden" name="id" value="{{ $invoice->id }}" />
    <input type="hidden" name="visible" value="{{ intval(!$invoice->visible) }}" />
    <x-button action="submit"
        icon="{{ $invoice->visible ? 'eye-slash' : 'eye' }}"
        label="{{ $invoice->visible ? 'Ukryj' : 'Pokaż' }}"
        />

    <x-button action="{{ route('quest', ['id' => $invoice->quest_id]) }}"
        icon="angles-left" label="Wróć do zlecenia"
        />
    <x-button action="#"
        icon="download" label="Pobierz PDF"
        />
</form>

@endsection
