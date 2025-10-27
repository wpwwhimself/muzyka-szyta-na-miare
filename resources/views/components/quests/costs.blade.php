@props([
    "quest",
])

<div id="costs">
    <x-shipyard.app.h lvl="4" :icon="model_icon('costs')">Koszty</x-shipyard.app.h>

    <div class="flex right nowrap">
        <table>
            <thead>
                <tr>
                    <th>Kategoria</th>
                    <th>Kwota</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($quest->song->costs as $cost)
                <tr>
                    <td>{{ $cost->type->name }}</td>
                    <td>{{ _c_(as_pln($cost->amount)) }}</td>
                </tr>
                @empty
                <tr><td colspan=2><span class="grayed-out">Brak kosztów</span></td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th>Suma:</th>
                    <th>{{ _c_(as_pln($quest->song->costs?->sum("amount"))) }}</th>
                </tr>
            </tfoot>
        </table>

        <x-shipyard.ui.button
            :icon="model_icon('costs')"
            pop="Koszty"
            :action="route('costs')"
        />
    </div>
</div>
