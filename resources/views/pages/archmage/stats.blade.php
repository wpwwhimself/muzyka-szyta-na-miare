@extends('layouts.app')
@section('title', "Statystyki")

@section('content')

<div class="grid but-mobile-down" style="--col-count: 2;">
  <x-section title="Podsumowanie całego dorobku" icon="chart-pie">
    <x-stats-highlight-h :data="$stats->summary->general" />
    <x-stats-highlight-h title="Podział zleceń wg typu" :data="$stats->summary->quest_types" :bracketed-numbers="'percentages'" />
    <x-stats-highlight-h title="Najpopularniejsze wyceny <small>(z wycenionych schematycznie)</small>" :data="$stats->summary->quest_pricings" :bracketedNumbers="'percentages'" />
    <x-barplot title="Dochody roczne ogółem" :data="$stats->summary->income_total" :all-pln="true" />
  </x-section>

  <x-section title="Klienci" :icon="model_icon('users')">
    <x-stats-highlight-h :data="$stats->clients->summary" :bracketed-numbers="'percentages'" />
    <x-barplot title="Podział klientów wg doświadczenia" :data="$stats->clients->exp" :percentages="true" />
    <x-barplot title="Nowi klienci w ostatnim czasie" :data="$stats->clients->new" />
  </x-section>
</div>

<x-section title="Finanse w ostatnich 12 mc" icon="cash">
  <x-stats-highlight-h :data="$stats->finances->total" :bracketed-numbers="'comparison'" :all-pln="true" />
  <x-barplot title="Przychody" :data="$stats->finances->income" :all-pln="true" />
  <x-barplot title="Koszty" :data="$stats->finances->costs" :all-pln="true" />
  <x-barplot title="Dochody" :data="$stats->finances->gross" :all-pln="true" />
  <x-barplot title="Średnia cena 1 zlecenia" :data="$stats->finances->prop" :all-pln="true" />
  <x-barplot title="Przychód na godzinę" :data="$stats->finances->prop_per_h" :all-pln="true" />
</x-section>

<div class="grid but-mobile-down" style="--col-count: 2;">
  <x-section scissors title="Zlecenia" :icon="model_icon('quests')">
    <x-stats-highlight-h title="Zlecenia w ostatnich 30 dniach" :data="$stats->quests->recent" :bracketed-numbers="'comparison'" />
    <x-stats-highlight-h title="Obecne fazy zleceń" :data="$stats->quests->statuses" :bracketed-numbers="'percentages'" />
    <x-stats-highlight-h title="Obecne fazy zapytań" :data="$stats->quests->requests" :bracketed-numbers="'percentages'" />
    <x-barplot title="Na ile dni przed deadlinem się wyrabiam?" :data="$stats->quests->deadlines->soft" />
    <x-barplot title="Ile dni przed hard-deadlinem klient akceptuje" :data="$stats->quests->deadlines->hard" />
  </x-section>

  <x-section title="Top 10" icon="finance">
    <x-stats-table title="Najczęściej poprawiane zlecenia" :data="$stats->quests->corrections" :footer="true" />
    <x-stats-table title="Najbardziej wybredni klienci" :data="$stats->clients->pickiness->high" />
    <x-stats-table title="Najbardziej aktywni w ostatnich 3 mc" :data="$stats->clients->most_active" />
  </x-section>
</div>

<x-section title="Utwory" :icon="model_icon('songs')">
  <x-stats-highlight-h title="Czas poświęcony na utwór" :data="$stats->songs->time_summary" />
  <x-stats-highlight-h title="Średni czas z podziałem na gatunki</small>" :data="$stats->songs->time_genres" :bracketed-numbers="'comparison-raw'" />
</x-section>

<div class="flex right center middle">
  <x-shipyard.ui.button
    label="Grania"
    icon="trumpet"
    :action="route('stats-gigs')"
  />
</div>

@endsection
