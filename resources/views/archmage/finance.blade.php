@extends('layouts.app', compact("title"))

@section('content')

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-chart-pie"></i> Podsumowanie</h1>
    </div>
    <x-stats-highlight-h title="Obecny miesiąc" :data="$this_month" :all-pln="true" />
    <x-barplot title="Saturacja wpływów w kolejnych miesiącach" :data="$saturation" :all-pln="true" :percentages="true" />
</section>

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
                @php $amount_total = ['immediate' => 0, 'delayed' => 0] @endphp
                @foreach ($unpaids as $client_id => $quests)
                <tr>
                    <td><a href="{{ route("clients", ['search' => $client_id]) }}">{{ _ct_($quests[0]->client->client_name) }}</a></td>
                    <td class="quest-list">
                        @php $amount_to_pay = ['immediate' => 0, 'delayed' => 0] @endphp
                        @foreach ($quests as $quest)
                        <div>
                            <a href="{{ route("quest", ["id" => $quest->id]) }}"
                                @if ($quest->delayed_payment_in_effect)
                                class="ghost" {{ Popper::pop("Opłata opóźniona do za ".$quest->delayed_payment->endOfDay()->diffInDays()." dni") }}
                                @endif
                                >
                                {{ $quest->song->title ?? "utwór bez tytułu" }}
                                <x-phase-indicator-mini :status="$quest->status" />
                                {{ _c_(as_pln($quest->price - $quest->payments->sum("comment"))) }}
                            </a>
                            <input type="checkbox" name="{{ $quest->id }}" />
                        </div>
                        @php
                        $amount_to_pay[($quest->delayed_payment_in_effect) ? "delayed" : "immediate"] += $quest->price - $quest->payments->sum("comment");
                        $amount_total[($quest->delayed_payment_in_effect) ? "delayed" : "immediate"] += $quest->price - $quest->payments->sum("comment")
                        @endphp
                        @endforeach
                    </td>
                    <td>
                        @if($amount_to_pay["immediate"]) <span>{{ _c_(as_pln($amount_to_pay["immediate"])) }}</span> @endif
                        @if(min($amount_to_pay) > 0) <br> @endif
                        @if($amount_to_pay["delayed"]) <span class="ghost">{{ _c_(as_pln($amount_to_pay["delayed"])) }}</span> @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th>Razem</th>
                    <th>
                        @if($amount_total["immediate"]) <span>{{ _c_(as_pln($amount_total["immediate"])) }}</span> @endif
                        @if(min($amount_total) > 0) <br> @endif
                        @if($amount_total["delayed"]) <span class="ghost">{{ _c_(as_pln($amount_total["delayed"])) }}</span> @endif
                    </th>
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
        <x-a href="{{ route('finance-summary') }}">Raport</x-a>
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
                    <a href="{{ route('clients', ['search' => $item->changer->id]) }}">
                        {{ _ct_($item->changer->client_name) }}
                    </a>
                </td>
                <td>
                    @if($item->re_quest_id)
                    <a href="{{ route('quest', ['id' => $item->re_quest_id]) }}">
                        {{ $item->quest->song->title ?? "utwór bez tytułu" }}
                    </a>
                    <x-phase-indicator-mini :status="$item->quest->status" />
                    @else
                    <span class="grayed-out">budżet</span>
                    @endif
                </td>
                <td>
                    {{ _c_(as_pln($item->comment)) }}
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
    <x-button action="{{ route('finance-summary') }}" label="Podsumowanie" icon="chart-column" />
    <x-button action='{{ route("costs") }}' label="Koszty" icon="money-bill-wave" />
    <x-button action='{{ route("invoices") }}' label="Faktury" icon="file-invoice" />
</div>
@endsection
