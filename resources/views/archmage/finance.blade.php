@extends('layouts.app', compact("title"))

@section('content')

@if(count($unpaids))
<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-receipt"></i>
            Zalegający z opłatami
        </h1>
    </div>

    <form action="{{ route('finance-pay') }}" method="post">
        @csrf
        <table id="finance-unpaids">
            <thead>
                <tr>
                    <th>Klient</th>
                    <th>Zlecenia</th>
                    <th>Razem</th>
                </tr>
            </thead>
            <tbody>
                @php $amount_total = 0 @endphp
                @foreach ($unpaids as $client_id => $quests)
                <tr>
                    <td><a href="{{ route("clients") }}#client{{ $client_id }}">{{ $quests[0]->client->client_name }}</a></td>
                    <td class="quest-list">
                        @php $amount_to_pay = 0 @endphp
                        @foreach ($quests as $quest)
                        <div>
                            <a href="{{ route("quest", ["id" => $quest->id]) }}">
                                {{ $quest->song->title ?? "utwór bez tytułu" }}
                                <x-phase-indicator-mini :status="$quest->status" />
                                {{ as_pln($quest->price - $quest->payments->sum("comment")) }}
                            </a>
                            <input type="checkbox" name="{{ $quest->id }}" />
                        </div>
                        @php
                        $amount_to_pay += $quest->price - $quest->payments->sum("comment");
                        $amount_total += $quest->price - $quest->payments->sum("comment")
                        @endphp
                        @endforeach
                    </td>
                    <td>
                        {{ as_pln($amount_to_pay) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th>Razem</th>
                    <th>{{ as_pln($amount_total) }}</th>
                </tr>
            </tfoot>
        </table>
        <x-button action="submit" icon="cash-register" label="Opłać zaznaczone" />
    </form>
</section>
@endif

<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-clock-rotate-left"></i>
            Ostatnie wpłaty
        </h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>Klient</th>
                <th>Zlecenie</th>
                <th>Kwota</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($recent as $item)
            @if ($item->date->gt(now()->subDay()))
            <tr>
            @else
            <tr class="ghost">
            @endif
                <td>
                    <a href="{{ route('clients') }}#client{{ $item->quest->client_id }}">
                        {{ $item->quest->client->client_name }}
                    </a>
                </td>
                <td>
                    <a href="{{ route('quest', ['id' => $item->re_quest_id]) }}">
                        {{ $item->quest->song->title ?? "utwór bez tytułu" }}
                    </a>
                    <x-phase-indicator-mini :status="$item->quest->status" />
                </td>
                <td>
                    {{ as_pln($item->comment) }}
                </td>
                <td {{ Popper::pop($item->date) }}>
                    {{ $item->date->diffForHumans() }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</section>
<div>
    <x-button action='{{ route("costs") }}' label="Koszty" icon="money-bill-wave" />
    <x-button action='{{ route("invoices") }}' label="Faktury" icon="file-invoice" />
</div>
@endsection
