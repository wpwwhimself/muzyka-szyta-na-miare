@props([
    "quest",
])

<div id="invoices">
    <x-shipyard.app.h lvl="4" :icon="model_icon('invoices')">Faktury i rachunki</x-shipyard.app.h>

    @if (is_archmage())
    <div class="flex right nowrap">
        <table>
            <thead>
                <tr>
                    <th>Numer</th>
                    <th>Kwota (zlec./całk.)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quest->allInvoices as $invoice)
                <tr>
                    <td>
                        <a href="{{ route('invoice', ['id' => $invoice->id]) }}">
                            <i class="fa-solid fa-{{ $invoice->visible ? 'file-invoice' : 'eye-slash' }}"></i>
                            {{ $invoice->fullCode }}
                        </a>
                        @if (!$invoice->visible)
                        <span class="accent error" @popper(Klient nie widzi faktury)>
                            <x-shipyard.app.icon name="eye-off" />
                        </span>
                        @endif
                    </td>
                    <td>
                        {{ _c_(as_pln($invoice->quests->filter(fn($q) => $q->id == $quest->id)->first()->pivot->amount)) }} / {{ _c_(as_pln($invoice->amount)) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan=2>
                        <span class="grayed-out">Brak</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <x-shipyard.ui.button
            icon="plus"
            pop="Dodaj"
            action="none"
            onclick="openModal('edit-invoice', {
                payer_name: '{{ $quest->user->notes->invoice_data['payer_name'] ?? $quest->user->notes->client_name }}',
                payer_email: '{{ $quest->user->notes->invoice_data['payer_email'] ?? $quest->user->notes->email }}',
                payer_phone: '{{ $quest->user->notes->invoice_data['payer_phone'] ?? $quest->user->notes->phone }}',
                {{ collect(['payer_title', 'payer_address', 'payer_nip', 'payer_regon'])->map(fn ($fld) =>
                    isset($quest->user->notes->invoice_data[$fld]) ? $fld.': \''.$quest->user->notes->invoice_data[$fld].'\',' : ''
                )->join('') }}
                {{ collect(['receiver_name', 'receiver_title', 'receiver_address', 'receiver_nip', 'receiver_regon', 'receiver_email', 'receiver_phone'])->map(fn ($fld) =>
                    isset($quest->user->notes->invoice_data[$fld]) ? $fld.': \''.$quest->user->notes->invoice_data[$fld].'\',' : ''
                )->join('') }}
                quests: '{{ $quest->id }}'
            });"
            class="tertiary"
        />
    </div>

    @else
    <div class="flex right">
        @forelse($quest->visibleInvoices ?? [] as $invoice)
        <x-shipyard.ui.button
            :icon="model_icon('invoices')"
            :label="$invoice->fullCode"
            :action="route('invoice', ['id' => $invoice->id])"
            target="_blank"
        />
        @empty
        <p class="grayed-out">Brak przypisanych faktur</p>
        @endforelse
    </div>

    @endif
</div>
