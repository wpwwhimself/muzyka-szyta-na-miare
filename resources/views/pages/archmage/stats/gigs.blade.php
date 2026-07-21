@extends("shipyard::layouts.admin")
@section("title", "Grania")
@section("subtitle", "Statystyki")

@section("content")

<x-shipyard.app.card>
    Dane dotyczą ostatnich {{ $range_in_months }} miesięcy.
</x-shipyard.app.card>

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-shipyard.app.section title="Podsumowanie" icon="chart-pie">
        <div class="flex right but-mobile-down center middle">
            @foreach ($stats["summary"]["general"] as $label => $value)
            <x-shipyard.stats.tile :label="$label">{{ $value }}</x-shipyard.stats.tile>
            @endforeach
        </div>

        <x-shipyard.app.h lvl="3" :icon="model_icon('quest-types')">Rodzaje grań</x-shipyard.app.h>
        <div class="flex right but-mobile-down center middle">
            @foreach ($stats["summary"]["gig_types"]["split"] as $label => $value)
            <x-shipyard.stats.tile :label="$label"
                :value="$value"
                :percentage-of="$stats['summary']['gig_types']['total']"
            />
            @endforeach
        </div>
    </x-shipyard.app.section>

    <x-shipyard.app.section title="Ostatnie grania" icon="trumpet">
        <x-slot:actions>
            <x-shipyard.ui.button
                label="Dodaj"
                icon="plus"
                action="none"
                onclick="openModal('add-gig-transaction', {
                    date: '{{ today()->format('Y-m-d') }}',
                });"
                class="tertiary"
            />
        </x-slot:actions>

        <div class="flex right but-mobile-down center middle">
            @foreach ($stats["gigs"]["recent"]["main"] as $label => $value)
            <x-shipyard.stats.tile :label="$label"
                :value="$value"
                :compared-to="$stats['gigs']['recent']['compared_to'][$label]"
            />
            @endforeach
        </div>

        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Kategoria</th>
                    <th>Opis</th>
                    <th>Kwota</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($stats["gigs"]["recent"]["main_list"] as $gig)
                <tr>
                    <td>{{ $gig->date->format("d.m.Y") }}</td>
                    <td>{{ Str::of($gig->typable->name)->after("granie: ") }}</td>
                    <td>{{ $gig->description }}</td>
                    <td>{{ _c_(as_pln($gig->amount)) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="ghost">Brak wpisów</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-shipyard.app.section>

    <x-shipyard.app.section title="Przychody w miesiącach" icon="cash" style="grid-column: span 2;">
        <x-shipyard.stats.chart.column title="Łącznie" :data="$stats['finances']['income']" />
        @foreach (\App\Models\IncomeType::where("name", "like", "granie:%")->get() as $type)
        <x-shipyard.stats.chart.column :title="$type->name" :data="$stats['finances']['income_'.$type->id]" :max="$max_monthly" />
        @endforeach
    </x-shipyard.app.section>
</div>

@endsection
