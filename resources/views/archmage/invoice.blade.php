@extends('layouts.app', compact("title"))

@section('content')

<div id="invoice">
    <h1>Faktura nr {{ $invoice->fullCode() }}</h1>
    <div class="dates">
        <p><strong>Data wystawienia:</strong> {{ $invoice->created_at->format("Y-m-d") }}</p>
    </div>
    <div class="grid-2">
        <section>
            <h3>Sprzedawca</h3>
            <h2>Wojciech Przybyła</h2>
            <h3>Muzyka Szyta Na Miarę</h3>
            <p><strong>e-mail:</strong> {{ env("MAIL_FROM_ADDRESS") }}</p>
            <p><strong>tel:</strong> 530 268 000</p>
            <p><strong>nr konta:</strong> 53 1090 1607 0000 0001 1633 2919</p>
        </section>
        <section>
            <h3>Nabywca</h3>
            <h2>{{ $invoice->quest->client->client_name }}</h2>
            @if($invoice->quest->client->email)
            <p><strong>e-mail:</strong> {{ $invoice->quest->client->email }}</p>
            @endif
            @if($invoice->quest->client->phone)
            <p><strong>tel:</strong> {{ $invoice->quest->client->phone }}</p>
            @endif
        </section>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nazwa usługi</th>
                <th>Cena</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @switch(song_quest_type($invoice->quest->song_id)->id)
                        @case(1) Przygotowanie podkładu muzycznego @break
                        @case(2) Przygotowanie nut @break
                        @case(3) Obróbka nagrania @break
                        @default Przygotowanie materiałów muzycznych
                    @endswitch
                    do utworu
                    „{{ $invoice->quest->song->title ?? "bez tytułu" }}”
                </td>
                <td>{{ number_format($invoice->amount, 2, ",", " ") }} zł</td>
            </tr>
        </tbody>
    </table>

    <h1>Razem do zapłaty: {{ number_format($invoice->amount, 2, ",", " ") }} zł</h1>
</div>

<x-button action="{{ route('quest', ['id' => $invoice->quest_id]) }}"
    icon="angles-left" label="Wróć do zlecenia"
    />
<x-button action="#"
    icon="download" label="Pobierz PDF"
    />

@endsection
