@extends('layouts.app')
@section("title", "Centrum finansowe")

@section('content')

<div class="flex right center middle">
    <x-shipyard.ui.button :action="route('finance-summary')" label="Podsumowanie" icon="finance" />
    <x-shipyard.ui.button :action="route('costs')" label="Koszty" :icon="model_icon('cost-types')" />
    <x-shipyard.ui.button :action="route('invoices')" label="Faktury" :icon="model_icon('invoices')" />
    <x-shipyard.ui.button :action="route('taxes')" label="Podatki" icon="cash-register" />
    <x-shipyard.ui.button :action="route('gig-price-suggest')" label="Wycena grania" icon="chat-alert" />
</div>

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-section title="Podsumowanie" icon="finance">
        <x-stats-highlight-h title="Obecny miesiąc" :data="$this_month" :all-pln="true" />
        <x-barplot title="Saturacja wpływów w kolejnych miesiącach" :data="$saturation" :all-pln="true" :percentages="true" />
    </x-section>

    <x-section title="Ostatnie wpłaty" icon="history">
        <x-slot:buttons>
            <x-a href="{{ route('finance-summary') }}">Raport</x-a>
        </x-slot:buttons>

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
                        @if ($item->relatable)
                        <a href="{{ route('client-view', ['id' => $item->relatable->user?->id ?? $item->relatable->id]) }}">
                            {!! $item->relatable->user ?? $item->relatable !!}
                        </a>
                        @endif
                    </td>
                    <td>
                        @if ($item->relatable)
                        @if($item->relatable instanceof App\Models\Quest)
                        <a href="{{ route('quest', ['id' => $item->relatable->id]) }}">
                            {{ $item->relatable->song }}
                        </a>
                        <x-phase-indicator-mini :status="$item->relatable->status" />
                        @else
                        <span class="grayed-out">budżet</span>
                        @endif
                        @endif
                    </td>
                    <td>
                        {{ _c_(as_pln($item->amount)) }}
                    </td>
                    <td {{ Popper::pop($item->date) }}>
                        {{ $item->date->diffForHumans() }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-section>
</div>

@if (count($returns))
<x-section title="Zwroty pieniędzy do wykonania" icon="cash-refund">
    <div class="flex down">
        @foreach ($returns as $quest)
        <x-shipyard.app.section
            :icon="$quest->song->type->icon"
            :title="$quest->user"
        >
            <x-slot:actions>
                <x-shipyard.ui.button
                    :action="route('finance-return', ['quest_id' => $quest->id])"
                    label="Potwierdź zwrot"
                    icon="check"
                    class="primary"
                />
                <x-shipyard.ui.button
                    :action="route('finance-return', ['quest_id' => $quest->id, 'budget' => true])"
                    label="Przesuń na budżet"
                    icon="safe-square"
                    class="primary"
                />
            </x-slot:actions>

            <div class="flex right center middle">
                <a href="{{ route("quest", ["id" => $quest->id]) }}">
                    {{ $quest }}
                </a>
                <x-shipyard.app.icon-label-value
                    icon="cash"
                    label="Suma wpłat"
                >
                    {{ _ct_(as_pln($quest->payments_sum)) }}
                </x-shipyard.app.icon-label-value>
            </div>
        </x-shipyard.app.section>
        @endforeach
    </div>
</x-section>
@endif

@if(count($unpaids))
<x-section title="Zalegający z opłatami" icon="account-cash">
    <form action="{{ route('finance-pay') }}" method="post" class="flex down">
        @csrf

        @php $amount_total = ['immediate' => 0, 'delayed' => 0] @endphp
        @foreach ($unpaids as $client)
        <x-extendo-block :key="$client->id"
            :header-icon="model_icon('users')"
            :title="$client->notes"
            :subtitle="implode(' // ', [
                $client->questsUnpaid->count(),
                $client->questsUnpaid
                    ->partition(fn($q) => !$q->delayed_payment_in_effect)
                    ->map(fn($q) => _c_(as_pln($q->sum('payment_remaining'))))
                    ->implode(' + '),
            ])"
        >
            <x-slot:buttons>
                <x-button label="Klient" :icon="model_icon('users')" :action="route('client-view', ['id' => $client->id])" />
            </x-slot:buttons>

            @php $amount_to_pay = ['immediate' => 0, 'delayed' => 0] @endphp
            <div class="flex right center">
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
            </div>
        </x-extendo-block>
        @endforeach

        <x-shipyard.ui.button class="primary" action="submit" icon="cash-register" label="Opłać zaznaczone" />
    </form>

    <x-slot:buttons>
        <h3>
            Razem:
            @if($amount_total["immediate"]) <span>{{ _c_(as_pln($amount_total["immediate"])) }}</span> @endif
                @if(min($amount_total) > 0) / @endif
            @if($amount_total["delayed"]) <span class="ghost">{{ _c_(as_pln($amount_total["delayed"])) }}</span> @endif
        </h3>
    </x-slot:buttons>
</x-section>
@endif

@endsection
