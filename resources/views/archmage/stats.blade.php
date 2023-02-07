@extends('layouts.app', compact("title"))

@section('content')
<form
    action="{{ route('stats-import') }}"
    method="post"
    class="flex-right"
    enctype="multipart/form-data"
    >
    @csrf
    <p>Ostatnia aktualizacja: {{ $stats->today }}</p>
    <x-input type="file" name="json" label="JSON z danymi" :small="true" />
    <x-button action="submit" label="Wgraj" icon="upload" :small="true" />
</form>
<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-chart-pie"></i> Podsumowanie całego dorobku
        </h1>
    </div>
    <x-stats-highlight-h :data="$stats->summary->general" />
    <x-stats-highlight-h title="Podział zleceń wg typu" :data="$stats->summary->quest_types" :percentages="true" />
    <x-stats-highlight-h title="Najpopularniejsze wyceny <small>(z wycenionych schematycznie)</small>" :data="$stats->summary->quest_pricings" :percentages="true" />
</section>
<section class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
        <h1><i class="fa-solid fa-calendar"></i> Ostatni miesiąc</h1>
    </div>

    <x-stats-highlight-h title="Zlecenia w ostatnich 30 dniach" :data="$stats->recent->quests" />
</section>

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-users"></i> Klienci</h1>
    </div>
    <x-stats-highlight-h :data="$stats->clients->summary" :percentages="true" />
    <x-barplot title="Podział klientów wg doświadczenia" :data="$stats->clients->exp" :percentages="true" />
    <x-barplot title="Nowi klienci w ostatnim czasie" :data="$stats->clients->new" />
</section>

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
    </div>
    <x-barplot title="Zarobki w ostatnich miesiącach" :data="$stats->income->total" />
    <x-barplot title="Średnia cena 1 zlecenia" :data="$stats->income->prop" />
</section>

{{--
<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-boxes"></i> Zlecenia</h1>
    </div>
    {{-- zlecenia oddane n przed deadlinem --}}
{{-- </section> --}}

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-compact-disc"></i> Utwory</h1>
    </div>
    <x-stats-highlight-h title="Czas poświęcony na utwór" :data="$stats->songs->time_summary" />
</section>

@endsection
