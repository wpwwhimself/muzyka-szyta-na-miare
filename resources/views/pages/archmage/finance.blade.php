@extends('layouts.app', compact("title"))

@section('content')

<div>
    <x-button action="{{ route('finance-summary') }}" label="Podsumowanie" icon="chart-column" />
    <x-button action='{{ route("costs") }}' label="Koszty" icon="money-bill-wave" />
    <x-button action='{{ route("invoices") }}' label="Faktury" icon="file-invoice" />
    <x-button action='{{ route("taxes") }}' label="Podatki" icon="cash-register" />
    <x-button :action="route('gig-price-suggest')" label="Wycena grania" icon="magnifying-glass-dollar" />
</div>

<div class="grid-2">
    <section>
        <div class="section-header">
            <h1><i class="fa-solid fa-chart-pie"></i> Podsumowanie</h1>
        </div>
        <x-stats-highlight-h title="Obecny miesiąc" :data="$this_month" :all-pln="true" />
        <x-barplot title="Saturacja wpływów w kolejnych miesiącach" :data="$saturation" :all-pln="true" :percentages="true" />
    </section>

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
</div>

@if (count($returns))
<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-backward-step"></i>
            Zwroty pieniędzy do wykonania
        </h1>
    </div>

    <div class="flex down">
        @foreach ($returns as $quest)
        <x-extendo-block :key="$quest->id"
            :header-icon="substr($quest->song->type->fa_symbol, 3)"
            :title="$quest->song->full_title"
            :subtitle="$quest->client->client_name"
        >
            <x-extendo-section title="Suma wpłat">
                {{ _ct_(as_pln($quest->payments_sum)) }}
            </x-extendo-section>

            <x-extendo-section title="ID zlecenia">
                <a href="{{ route('quest', ['id' => $quest->id]) }}">{{ $quest->id }}</a>
            </x-extendo-section>

            <div class="flex down">
                <x-button
                    :action="route('finance-return', ['quest_id' => $quest->id])"
                    label="Potwierdź zwrot"
                    icon="check"
                    :small="true"
                />
                <x-button
                    :action="route('finance-return', ['quest_id' => $quest->id, 'budget' => true])"
                    label="Przesuń na budżet"
                    icon="sack-dollar"
                    :small="true"
                />
            </div>
        </x-extendo-block>
        @endforeach
    </div>
</section>
@endif

@if(count($unpaids))
<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-receipt"></i>
            Zalegający z opłatami
        </h1>
    </div>

    <form action="{{ route('finance-pay') }}" method="post" class="flex down">
        @csrf

        @php $amount_total = ['immediate' => 0, 'delayed' => 0] @endphp
        @foreach ($unpaids as $client)
        <x-extendo-block :key="$client->id"
            :header-icon="($client->is_veteran ? 'user-shield' : 'user')"
            :title="_ct_($client->client_name)"
            :subtitle="implode(' // ', [
                $client->questsUnpaid->count(),
                $client->questsUnpaid
                    ->partition(fn($q) => !$q->delayed_payment_in_effect)
                    ->map(fn($q) => _c_(as_pln($q->sum('payment_remaining'))))
                    ->implode(' + '),
            ])"
        >
            @php $amount_to_pay = ['immediate' => 0, 'delayed' => 0] @endphp
            @foreach ($client->questsUnpaid as $quest)
            <x-extendo-section :title="$quest->id">
                <a href="{{ route('quest', ['id' => $quest->id]) }}"
                    @if ($quest->delayed_payment_in_effect)
                    class="ghost" {{ Popper::pop("Opłata opóźniona do za ".$quest->delayed_payment->endOfDay()->diffInDays()." dni") }}
                    @endif
                >
                    {{ $quest->song->title ?? "utwór bez tytułu" }}
                    <x-phase-indicator-mini :status="$quest->status" />
                </a>
                <span>{{ _c_(as_pln($quest->price - $quest->payments_sum)) }}</span>
                <input type="checkbox" name="{{ $quest->id }}" />
            </x-extendo-section>
            @php
            $amount_to_pay[($quest->delayed_payment_in_effect) ? "delayed" : "immediate"] += $quest->price - $quest->payments_sum;
            $amount_total[($quest->delayed_payment_in_effect) ? "delayed" : "immediate"] += $quest->price - $quest->payments_sum
            @endphp
            @endforeach
            <x-extendo-section title="Razem">
                @if($amount_to_pay["immediate"]) <span>{{ _c_(as_pln($amount_to_pay["immediate"])) }}</span> @endif
                @if($amount_to_pay["delayed"]) <span class="ghost">{{ _c_(as_pln($amount_to_pay["delayed"])) }}</span> @endif
            </x-extendo-section>
            <div>
                <x-button label="Klient" icon="user" :small="true" :action="route('clients', ['search' => $client->id])" />
            </div>
        </x-extendo-block>
        @endforeach

        <h3>
            Razem:
            @if($amount_total["immediate"]) <span>{{ _c_(as_pln($amount_total["immediate"])) }}</span> @endif
                @if(min($amount_total) > 0) / @endif
            @if($amount_total["delayed"]) <span class="ghost">{{ _c_(as_pln($amount_total["delayed"])) }}</span> @endif
        </h3>
        <x-button action="submit" icon="cash-register" label="Opłać zaznaczone" />
    </form>
</section>
@endif

@endsection
