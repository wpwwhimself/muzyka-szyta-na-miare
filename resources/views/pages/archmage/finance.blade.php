@extends('layouts.app')
@section("title", "Centrum finansowe")

@section('content')

<div class="flex right center middle">
    <x-shipyard.ui.button :action="route('finance-summary')" label="Podsumowanie" icon="finance" />
    <x-shipyard.ui.button :action="route('admin.model.list', ['model' => 'money-transactions', 'fltr[type]' => 'App\\Models\\CostType'])" label="Koszty" :icon="model_icon('cost-types')" />
    <x-shipyard.ui.button :action="route('invoices')" label="Faktury" :icon="model_icon('invoices')" />
    <x-shipyard.ui.button :action="route('taxes')" label="Podatki" icon="cash-register" />
    <x-shipyard.ui.button :action="route('gig-price-suggest')" label="Wycena grania" icon="chat-alert" />
</div>

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-section title="Podsumowanie" icon="finance">
        <x-stats-highlight-h title="Obecny miesiąc" :data="$this_month" :all-pln="true" />
        <x-shipyard.stats.chart.column
            title="Saturacja wpływów w kolejnych miesiącach"
            :subtitle="'Limit: ' . as_pln(INCOME_LIMIT())"
            icon="sack"
            :data="$saturation"
            mode="monetary"
            :max="INCOME_LIMIT()"
        />
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
<x-shipyard.app.form :action="route('finance-pay')" method="post">
    <x-shipyard.app.section title="Zalegający z opłatami"
        icon="account-cash"
        inner-class="grid but-mobile-down"
        inner-style="--col-count: 2;"
    >
        @php $amount_total = ['immediate' => 0, 'delayed' => 0] @endphp
        @foreach ($unpaids as $client)
        <x-shipyard.app.section
            :icon="model_icon('users')"
            :title="$client->notes"
            :subtitle="implode('', [
                view('components.shipyard.stats.counter', [
                    'rank' => $client->questsUnpaid->count(),
                    'style' => 'lines',
                ])->render(),
                $client->questsUnpaid
                    ->partition(fn($q) => !$q->delayed_payment_in_effect)
                    ->map(fn($q) => _c_(as_pln($q->sum('payment_remaining'))))
                    ->implode(' / ')
            ])"
            :extended="false"
            inner-class="flex right but-mobile-down center"
        >
            <x-slot:actions>
                <x-shipyard.ui.button
                    pop="Klient"
                    :icon="model_icon('users')"
                    :action="route('client-view', ['id' => $client->id])"
                />
                <x-shipyard.ui.button
                    pop="Utwórz fakturę na nieopłacone zlecenia"
                    :icon="model_icon('invoices')"
                    action="none"
                    onclick="openModal('edit-invoice', {
                        payer_name: '{{ $client->notes->invoice_data['payer_name'] ?? $client->notes->client_name }}',
                        payer_email: '{{ $client->notes->invoice_data['payer_email'] ?? $client->notes->email }}',
                        payer_phone: '{{ $client->notes->invoice_data['payer_phone'] ?? $client->notes->phone }}',
                        {{ collect(['payer_title', 'payer_address', 'payer_nip', 'payer_regon'])->map(fn ($fld) =>
                            isset($client->notes->invoice_data[$fld]) ? $fld.': \''.$client->notes->invoice_data[$fld].'\',' : ''
                        )->join('') }}
                        {{ collect(['receiver_name', 'receiver_title', 'receiver_address', 'receiver_nip', 'receiver_regon', 'receiver_email', 'receiver_phone'])->map(fn ($fld) =>
                            isset($client->notes->invoice_data[$fld]) ? $fld.': \''.$client->notes->invoice_data[$fld].'\',' : ''
                        )->join('') }}
                        quests: '{{ implode(' ', $client->questsUnpaid->pluck('id')->toArray()) }}'
                    });"
                    class="tertiary"
                />
            </x-slot:actions>

            @php $amount_to_pay = ['immediate' => 0, 'delayed' => 0] @endphp

            @foreach ($client->questsUnpaid as $quest)
            <x-shipyard.app.card
                @class(['ghost' => $quest->delayed_payment_in_effect])
                inner-class="flex down middle no-gap"
            >
                <small class="ghost">
                    {{ $quest->id }}
                    <x-phase-indicator-mini :status="$quest->status" />
                </small>
                <a href="{{ route('quest', ['id' => $quest->id]) }}">
                    {{ $quest->song }}
                </a>
                @if ($quest->delayed_payment_in_effect)
                <span class="accent danger">Opłata opóźniona do za {{ abs(round($quest->delayed_payment->endOfDay()->diffInDays())) }} dni</span>
                @endif
                <x-shipyard.ui.input type="checkbox"
                    :name="$quest->id"
                    :label="_c_(as_pln($quest->price - $quest->payments_sum))"
                    class="compact"
                />

                @php
                $amount_to_pay[($quest->delayed_payment_in_effect) ? "delayed" : "immediate"] += $quest->price - $quest->payments_sum;
                $amount_total[($quest->delayed_payment_in_effect) ? "delayed" : "immediate"] += $quest->price - $quest->payments_sum
                @endphp
            </x-shipyard.app.card>
            @endforeach
        </x-shipyard.app.section>
        @endforeach

        <x-slot:actions>
            <strong>
                Razem:
                @if($amount_total["immediate"]) <span>{{ _c_(as_pln($amount_total["immediate"])) }}</span> @endif
                    @if(min($amount_total) > 0) / @endif
                @if($amount_total["delayed"]) <span class="ghost">{{ _c_(as_pln($amount_total["delayed"])) }}</span> @endif
            </strong>
            <x-shipyard.ui.button class="primary" action="submit" icon="cash-register" label="Opłać zaznaczone" />
        </x-slot:actions>
    </x-shipyard.app.section>
</x-shipyard.app.form>
@endif

@endsection
