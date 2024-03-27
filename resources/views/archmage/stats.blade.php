@extends('layouts.app', compact("title"))

@section('content')

<div>
    <x-button action="{{ route('file-size-report') }}" icon="weight-hanging" label="Raport wielkości sejfów" />
</div>

<div class="grid-2 force-block">
  <section>
    <div class="section-header">
      <h1>
        <i class="fa-solid fa-chart-pie"></i> Podsumowanie całego dorobku
      </h1>
    </div>
    <x-stats-highlight-h :data="$stats->summary->general" />
    <x-stats-highlight-h title="Podział zleceń wg typu" :data="$stats->summary->quest_types" :bracketed-numbers="'percentages'" />
    <x-stats-highlight-h title="Najpopularniejsze wyceny <small>(z wycenionych schematycznie)</small>" :data="$stats->summary->quest_pricings" :bracketedNumbers="'percentages'" />
    <x-barplot title="Dochody roczne ogółem" :data="$stats->summary->income_total" :all-pln="true" />
  </section>

  <section>
    <div class="section-header">
      <h1><i class="fa-solid fa-users"></i> Klienci</h1>
    </div>
    <x-stats-highlight-h :data="$stats->clients->summary" :bracketed-numbers="'percentages'" />
    <x-barplot title="Podział klientów wg doświadczenia" :data="$stats->clients->exp" :percentages="true" />
    <x-barplot title="Nowi klienci w ostatnim czasie" :data="$stats->clients->new" />
  </section>
</div>

<section>
  <div class="section-header">
    <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
  </div>
  <x-stats-highlight-h :data="$stats->finances->total" :bracketed-numbers="'comparison'" :all-pln="true" />
  <x-barplot title="Przychody w ostatnich 12 mc" :data="$stats->finances->income" :all-pln="true" />
  <x-barplot title="Koszty w ostatnich 12 mc" :data="$stats->finances->costs" :all-pln="true" />
  <x-barplot title="Dochody w ostatnich 12 mc" :data="$stats->finances->gross" :all-pln="true" />
  <x-barplot title="Średnia cena 1 zlecenia w ostatnich 12 mc" :data="$stats->finances->prop" :all-pln="true" />
</section>

<div class="grid-2 force-block">
  <section class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
      <h1><i class="fa-solid fa-boxes"></i> Zlecenia</h1>
    </div>
    <x-stats-highlight-h title="Zlecenia w ostatnich 30 dniach" :data="$stats->quests->recent" :bracketed-numbers="'comparison'" />
    <x-stats-highlight-h title="Obecne fazy zleceń" :data="$stats->quests->statuses" :bracketed-numbers="'percentages'" />
    <x-stats-highlight-h title="Obecne fazy zapytań" :data="$stats->quests->requests" :bracketed-numbers="'percentages'" />
    <x-barplot title="Na ile dni przed deadlinem się wyrabiam?" :data="$stats->quests->deadlines->soft" :percentages="true" />
    <x-barplot title="Ile dni przed hard-deadlinem klient akceptuje" :data="$stats->quests->deadlines->hard" />
  </section>

  <section>
    <div class="section-header">
      <h1><i class="fa-solid fa-ranking-star"></i> Top 10</h1>
    </div>
    <x-stats-table title="Najczęściej poprawiane zlecenia" :data="$stats->quests->corrections" :footer="true" />
    <x-stats-table title="Najbardziej wybredni klienci" :data="$stats->clients->pickiness->high" />
    <x-stats-table title="Najbardziej aktywni w ostatnich 3 mc" :data="$stats->clients->most_active" />
  </section>
</div>

<section>
  <div class="section-header">
    <h1><i class="fa-solid fa-compact-disc"></i> Utwory</h1>
  </div>
  <x-stats-highlight-h title="Czas poświęcony na utwór" :data="$stats->songs->time_summary" />
  <x-stats-highlight-h title="Średni czas z podziałem na gatunki</small>" :data="$stats->songs->time_genres" :bracketed-numbers="'comparison-raw'" />
</section>

@endsection
