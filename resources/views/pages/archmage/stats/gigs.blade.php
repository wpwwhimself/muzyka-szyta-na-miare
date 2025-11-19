@extends("layouts.app")
@section("title", "Grania")
@section("subtitle", "Statystyki")

@section("content")

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-shipyard.app.section title="Podsumowanie" icon="chart-pie">
        <div class="flex right center middle">
            @foreach ($stats["summary"]["general"] as $label => $value)
            <x-shipyard.stats.tile :label="$label">{{ $value }}</x-shipyard.stats.tile>
            @endforeach
        </div>

        <x-shipyard.app.h lvl="3" :icon="model_icon('quest-types')">Rodzaje grań</x-shipyard.app.h>
        <div class="flex right center middle">
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

        <div class="flex right center middle">
            @foreach ($stats["gigs"]["recent"]["main"] as $label => $value)
            <x-shipyard.stats.tile :label="$label"
                :value="$value"
                :compared-to="$stats['gigs']['recent']['compared_to'][$label]"
            />
            @endforeach
        </div>
    </x-shipyard.app.section>

    <x-shipyard.app.section title="Przychody w ostatnich 12 mc" icon="cash" style="grid-column: span 2;">
        <x-shipyard.stats.chart.column :data="$stats['finances']['income']" />
    </x-shipyard.app.section>
</div>

@endsection
