@props(["invoice"])

<div id="invoice" class="flex down">
    <h1>Faktura nr {{ $invoice->fullCode }}</h1>
    <div class="dates grid name-value" style="--col-count: 2;">
        <span>Data wystawienia:</span>
        <span>{{ $invoice->created_at->format("Y-m-d") }}</span>
    </div>
    <div class="grid" style="--col-count: 2;">
        <section class="account">
            <h3>Sprzedawca</h3>
            <h2 class="accent primary">Wojciech Przybyła</h2>
            <h3>Muzyka Szyta Na Miarę</h3>
            <div class="grid name-value" style="--col-count: 2;">
                <span>e-mail:</span>
                <span>{{ env("MAIL_MAIN_ADDRESS") }}</span>
                <span>tel:</span>
                <span>530 268 000</span>
                <span>nr konta:</span>
                <span>58 1090 1607 0000 0001 5333 1539</span>
            </div>
        </section>
        <section class="account">
            <h3>Nabywca</h3>
            <h2 class="accent primary">{{ _ct_($invoice->payer_name ?? $invoice->quest->user->notes->client_name) }}</h2>
            <h3>{{ _ct_($invoice->payer_title) }}</h3>
            <div class="grid name-value" style="--col-count: 2;">
                @if ($invoice->payer_address)
                    <span>adres:</span>
                    <span>{{ _ct_($invoice->payer_address) }}</span>
                @endif
                @if($invoice->payer_email)
                    <span>e-mail:</span>
                    <span>{{ _ct_($invoice->payer_email) }}</span>
                @endif
                @if($invoice->payer_phone)
                    <span>tel:</span>
                    <span>{{ _ct_(implode(" ", str_split($invoice->payer_phone, 3))) }}</span>
                @endif
                @if($invoice->payer_nip)
                    <span>NIP:</span>
                    <span>{{ _ct_($invoice->payer_nip) }}</span>
                @endif
                @if($invoice->payer_regon)
                    <span>REGON:</span>
                    <span>{{ _ct_($invoice->payer_regon) }}</span>
                @endif
            </div>
        </section>
    </div>

    <table class="bordered rounded padded">
        <thead>
            <tr>
                <th>Nazwa usługi</th>
                <th>Cena</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($invoice->quests as $quest)
            <tr class="table-row">
                <td>
                    @if ($quest->pivot->primary)
                        @switch($quest->song->type->id)
                            @case(1) Przygotowanie podkładu muzycznego @break
                            @case(2) Przygotowanie nut @break
                            @case(3) Obróbka nagrania @break
                            @default Przygotowanie materiałów muzycznych
                        @endswitch
                    @else
                        @switch($quest->song->type->id)
                            @case(1) Przygotowanie poprawek do podkładu muzycznego @break
                            @case(2) Przygotowanie poprawek do nut @break
                            @case(3) Dodatkowa obróbka nagrania @break
                            @default Przygotowanie poprawek do materiałów muzycznych
                        @endswitch
                    @endif
                    do utworu:
                    @if ($quest->song->artist)
                    {{ $quest->song->artist }} –
                    @endif
                    <em>{{ $quest->song->title ?? "bez tytułu" }}</em>
                </td>
                <td>
                    {{ _c_(as_pln($quest->pivot->amount)) }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="grid name-value summary" style="--col-count: 2;">
        <span>Razem do zapłaty:</span>
        <span>{{ _c_(as_pln($invoice->amount)) }}</span>
        @if ($invoice->paid)
        <span class="small">Płatność otrzymana:</span>
        <span class="small">{{ _c_(as_pln($invoice->paid)) }}</span>
        <span>Pozostało do zapłaty:</span>
        <span @if ($invoice->isPaid) class="success" @endif>{{ _c_(as_pln($invoice->amount - $invoice->paid)) }}</span>
        @endif
    </div>
</div>
