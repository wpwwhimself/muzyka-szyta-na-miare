@extends('layouts.app', compact("title"))

@section('content')
<section>
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-chart-pie"></i> Podsumowanie całego dorobku
        </h1>
    </div>
    <div class="stats-highlight-h" style="grid-template-columns: repeat({{ count($big_summary) }}, 1fr);">
        @foreach ($big_summary as $name => $val)
        <p>{{ $name }}</p>
        <h2>{{ $val }}</h2>
        @endforeach
    </div>
</section>

<section class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
        <h1><i class="fa-solid fa-calendar"></i> Ostatni miesiąc</h1>
    </div>
    {{-- nowych zleceń ostatnio --}}
    {{-- ukończonych zleceń ostatnio --}}
    {{-- debiutanckich zleceń ostatnio --}}
</section>

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
    </div>
    {{-- zarobki z ostatniego roku na miesiące --}}
</section>

<section>
    <div class="section-header">
        <h1><i class="fa-solid fa-users"></i> Klienci</h1>
    </div>
    <div class="stats-highlight-h" style="grid-template-columns: repeat({{ count($clients_summary) }}, 1fr);">
        @foreach ($clients_summary as $name => $val)
        <p>{{ $name }}</p>
        <h2>{{ $val }}</h2>
        @endforeach
    </div>
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
