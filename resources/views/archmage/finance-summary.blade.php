@extends('layouts.app')

@section('content')

<div>
    <x-button action="{{ route('finance') }}" label="Wróć" icon="angles-right" />
    <x-button action="{{ route('costs') }}" label="Koszty" icon="money-bill-wave" />
    <x-button action='{{ route("taxes") }}' label="Podatki" icon="cash-register" />
</div>

<section class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
      <h1><i class="fa-solid fa-chart-column"></i> Podsumowanie – {{ \Carbon\Carbon::today()->subMonths(request()->get("subMonths", 0))->format("m.Y") }}</h1>
      @if (request()->get("subMonths"))
        <x-a href="{{ route('finance-summary') }}">Ten miesiąc</x-a>
      @else
        <x-a href="{{ route('finance-summary', ['subMonths' => 1]) }}">Poprzedni miesiąc</x-a>
      @endif
    </div>
    <x-stats-highlight-h :data="$summary" :all-pln="true" />
    <div>
    </div>
</section>

<section>
  <div class="section-header">
    <h1><i class="fa-solid fa-angles-down"></i> Wpływy</h1>
  </div>

  <div id="gains" class="quests-table">
    <style>
      #gains .table-row{ grid-template-columns: 1fr 2fr 1fr 1fr 1fr; }
      #gains .table-row span:last-child{ text-align: right; }
    </style>
    <div class="table-header table-row">
        <span>Data</span>
        <span>Klient</span>
        <span>Zlecenie</span>
        <span>Faktura</span>
        <span>Kwota</span>
    </div>
    @forelse ($gains as $pos)
    <div class="table-row">
        <span>{{ $pos->date->format("d.m.Y") }}</span>
        <span><a href="{{ route('clients', ['search' => $pos->changed_by]) }}">{{ _ct_($pos->client_name) }}</a></span>
        <span>
          @if ($pos->re_quest_id)
          <a href="{{ route('quest', ['id' => $pos->re_quest_id]) }}">{{ $pos->re_quest_id }}</a>
          @else
          <span class="grayed-out">budżet</span>
          @endif
        </span>
        <span>
          @if ($pos->invoice->first())
          <a href="{{ route('invoice', ['id' => $pos->invoice->first()->id]) }}">{{ $pos->invoice->first()->full_code }}</a>
          @endif
        </span>
        <span>{{ _c_(as_pln($pos->comment)) }}</span>
    </div>
    @empty
    <p class="grayed-out">Brak danych</p>
    @endforelse
  </div>
</section>

<section>
  <div class="section-header">
    <h1>
        <i class="fa-solid fa-money-bill-wave"></i> Wydatki
    </h1>
  </div>

  <div id="losses" class="quests-table">
    <style>
      #losses .table-row{ grid-template-columns: 1fr 2fr 1fr 1fr; }
      #losses .table-row span:last-child{ text-align: right; }
    </style>
    <div class="table-header table-row">
        <span>Data</span>
        <span>Typ</span>
        <span>Opis</span>
        <span>Kwota</span>
    </div>
    @forelse ($losses as $pos)
    <div class="table-row">
        <span>{{ $pos->created_at->format("d.m.Y") }}</span>
        <span>{{ _ct_($pos->type->name) }}</span>
        <span>{{ $pos->desc }}</span>
        <span>{{ _c_(as_pln($pos->amount)) }}</span>
    </div>
    @empty
    <p class="grayed-out">Brak danych</p>
    @endforelse
  </div>
</section>

@endsection
