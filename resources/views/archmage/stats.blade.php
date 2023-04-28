@extends('layouts.app', compact("title"))

@section('content')
<div class="flex-right center">
    <x-button action="{{ route('file-size-report') }}" icon="weight-hanging" label="Raport wielkości sejfów" />
</div>
<section>
  <div class="section-header">
    <h1>
      <i class="fa-solid fa-chart-pie"></i> Podsumowanie całego dorobku
    </h1>
  </div>
  <x-stats-highlight-h :data="$stats->summary->general" />
  <x-stats-highlight-h title="Podział zleceń wg typu" :data="$stats->summary->quest_types" :bracketed-numbers="'percentages'" />
  <x-stats-highlight-h title="Najpopularniejsze wyceny <small>(z wycenionych schematycznie)</small>" :data="$stats->summary->quest_pricings" :bracketedNumbers="'percentages'" />
</section>
<section class="sc-line">
  <x-sc-scissors />
  <div class="section-header">
    <h1><i class="fa-solid fa-boxes"></i> Zlecenia</h1>
  </div>
  <x-stats-highlight-h title="Zlecenia w ostatnich 30 dniach" :data="$stats->quests->recent" :bracketed-numbers="'comparison'" />
  <x-stats-highlight-h title="Obecne fazy zleceń" :data="$stats->quests->statuses" :bracketed-numbers="'percentages'" />
  <x-stats-table title="Top 5 najczęściej poprawianych zleceń" :data="$stats->quests->corrections" :footer="true" />
  <x-barplot title="Na ile dni przed deadlinem się wyrabiam?" :data="$stats->quests->deadlines->soft" :percentages="true" />
  {{-- <x-stats-highlight-h title="Ile dni przed hard-deadlinem klient akceptuje" :data="$stats->quests->deadlines->hard" /> --}}
</section>

<section>
  <div class="section-header">
    <h1><i class="fa-solid fa-users"></i> Klienci</h1>
  </div>
  {{-- <x-stats-highlight-h :data="$stats->clients->summary" :bracketed-numbers="'percentages'" /> --}}
  {{-- <x-barplot title="Podział klientów wg doświadczenia" :data="$stats->clients->exp" :percentages="true" /> --}}
  {{-- <x-barplot title="Nowi klienci w ostatnim czasie" :data="$stats->clients->new" /> --}}
  {{-- todo top 5 najbardziej i najmniej wybrednych klientów --}}
</section>

<section>
  <div class="section-header">
    <h1><i class="fa-solid fa-sack-dollar"></i> Finanse (w ostatnich miesiącach)</h1>
  </div>
  {{-- <x-stats-highlight-h :data="$stats->finances->total" :bracketed-numbers="'comparison'" /> --}}
  {{-- <x-barplot title="Przychody" :data="$stats->finances->income" /> --}}
  {{-- <x-barplot title="Koszty" :data="$stats->finances->costs" /> --}}
  {{-- <x-barplot title="Dochody" :data="$stats->finances->gross" /> --}}
  {{-- <x-barplot title="Średnia cena 1 zlecenia" :data="$stats->finances->prop" /> --}}
</section>

<section>
  <div class="section-header">
    <h1><i class="fa-solid fa-compact-disc"></i> Utwory</h1>
  </div>
  {{-- <x-stats-highlight-h title="Czas poświęcony na utwór" :data="$stats->songs->time_summary" /> --}}
  {{-- <x-stats-highlight-h title="Średni czas z podziałem na gatunki <small>(w porównaniu z wynikami miesiąc wstecz)</small>" :data="$stats->songs->time_genres" :bracketedNumbers="'comparison'" /> --}}
</section>
@endsection
