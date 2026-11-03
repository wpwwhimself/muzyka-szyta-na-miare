@props([
    "quest",
])

<div id="costs">
    <x-shipyard::app.h lvl="4" :icon="model_icon('cost-types')">Koszty</x-shipyard::app.h>

    <div class="flex right nowrap">
        <table>
            <thead>
                <tr>
                    <th>Koszt</th>
                    <th>Kwota</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($quest->costs as $cost)
                <tr>
                    <td>
                        {{ Str::substr($cost->typable->name, -1) }}
                        {{ $cost->description }}
                    </td>
                    <td>{{ _c_(as_pln($cost->amount)) }}</td>
                </tr>
                @empty
                <tr><td colspan=2><span class="grayed-out">Brak kosztów</span></td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th>Suma:</th>
                    <th>{{ _c_(as_pln($quest->costs?->sum("amount"))) }}</th>
                </tr>
            </tfoot>
        </table>

        <x-shipyard::ui.button
            icon="plus"
            pop="Koszty"
            action="none"
            onclick="openModal('mod-cost', {
                created_at: '{{ now()->format('Y-m-d') }}',
                relatable_type: 'App\\\\Models\\\\Quest',
                relatable_id: '{{ $quest->id }}',
            });"
            class="tertiary"
        />
    </div>
</div>
