@props([
    "quest",
])

<div id="invoices">
    <x-shipyard.app.h lvl="4" :icon="model_icon('invoices')">Faktury</x-shipyard.app.h>

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
            onclick="openModal('create-invoice', {
                questId: '{{ $quest->id }}'
            });"
            class="tertiary"
        />
    </div>
</div>
