@props([
    "quest",
])

<div id="payments">
    <x-shipyard.app.h lvl="4" icon="account-cash">Wpłaty</x-shipyard.app.h>

    @if ($quest->delayed_payment_in_effect)
    <div class="flex right spread middle accent danger">
        <x-shipyard.ui.field-input :model="$quest" field-name="delayed_payment" dummy />
        <x-warning>
            Z uwagi na limity przyjmowanych przeze mnie wpłat,
            <b>proszę o dokonanie wpłaty po {{ $quest->delayed_payment->format('d.m.Y') }}</b>.
            Po zaakceptowaniu zlecenia dostęp do plików
            zostanie przyznany automatycznie.
        </x-warning>
    </div>
    @endif

    <div class="flex right middle nowrap">
        <div class="loader-bar" style="--progress: {{ $quest->paid ? 100 : round($quest->payments_sum / $quest->price * 100) }}%">
            Opłacono: {{ as_pln($quest->payments_sum) }}
            @if (!$quest->paid)
            —
            Pozostało: {{ as_pln($quest->price - $quest->payments_sum) }}
            @endif
        </div>

        @if (!$quest->paid && is_archmage())
        <x-shipyard.ui.button
            icon="account-cash"
            pop="Opłać"
            action="none"
            onclick="openModal(`pay-for-quest`, {
                quest_id: '{{ $quest->id }}',
                status_id: 32,
                comment: '{{ $quest->price - $quest->payments_sum }}'
            })"
            class="tertiary"
        />
        @endif
    </div>
</div>

