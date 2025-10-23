@props([
    "quest",
])

<div id="payments">
    <x-shipyard.app.h lvl="4" icon="account-cash">Wpłaty</x-shipyard.app.h>

    <div class="flex right middle nowrap">
        <div class="loader-bar" style="--progress: {{ $quest->paid ? 100 : round($quest->payments_sum / $quest->price * 100) }}%">
            Opłacono: {{ as_pln($quest->payments_sum) }}
            @if (!$quest->paid)
            —
            Pozostało: {{ as_pln($quest->price - $quest->payments_sum) }}
            @endif
        </div>

        @if (!$quest->paid && is_archmage())
        <form action="{{ route("mod-quest-back") }}" method="post" id="quest-pay" class="flex right middle nowrap">
            @csrf
            <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
            <x-shipyard.ui.input type="number"
                name="comment"
                label="Opłać"
                :icon="model_field_icon('quest', 'price')"
                step="0.01"
                :value="$quest->price - $quest->payments_sum"
            />
            <x-shipyard.ui.button
                icon="account-cash"
                pop="Opłać"
                action="submit"
                name="status_id"
                value="32"
                class="primary"
            />
        </form>
        @endif
    </div>
</div>

