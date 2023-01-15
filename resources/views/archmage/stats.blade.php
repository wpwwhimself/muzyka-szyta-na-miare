@extends('layouts.app', compact("title"))

@section('content')
<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-chart-pie"></i> Podsumowanie całego dorobku
        </h1>
    </div>
    <x-stats-highlight-h :data="$big_summary" />
</section>

<section class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
        <h1><i class="fa-solid fa-calendar"></i> Ostatni miesiąc</h1>
    </div>
    <x-stats-highlight-h :data="$last_month" />
</section>

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
    </div>
    <x-barplot title="Zarobki w ostatnich miesiącach" :data="$income" />
</section>

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-users"></i> Klienci</h1>
    </div>
    <x-stats-highlight-h :data="$clients_summary" />
    <x-barplot title="Podział klientów wg doświadczenia" :data="$clients_counts" />
    <x-barplot title="Nowi klienci w ostatnich miesiącach" :data="$new_clients" />
</section>

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-boxes"></i> Zlecenia</h1>
    </div>
    {{-- zlecenia w podziale na typy (prices) --}}
    {{-- zlecenia oddane n przed deadlinem --}}
</section>

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-compact-disc"></i> Utwory</h1>
    </div>
    {{-- czas spędzony nad utworami --}}
</section>

@endsection
