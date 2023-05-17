@extends('layouts.app')

@section('content')

<section>
  <div class="section-header">
    <h1><i class="fa-solid fa-angles-down"></i> Wpływy</h1>
  </div>

  <div class="quests-table">
    <style>
      .table-row{ grid-template-columns: 1fr 2fr 1fr 1fr 1fr; }
      .table-row span:last-child{ text-align: right; }
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
        <span {{ Popper::pop($pos->date) }}>{{ $pos->date->diffForHumans() }}</span>
        <span><a href="{{ route('clients') }}#client{{ $pos->changed_by }}">{{ $pos->client_name }}</a></span>
        <span>
          @if ($pos->re_quest_id)
          <a href="{{ route('quest', ['id' => $pos->re_quest_id]) }}">{{ $pos->re_quest_id }}</a>
          @else
          {{ $pos->re_quest_id }}
          @endif
        </span>
        <span>
          @if ($pos->invoice->first())
          <a href="{{ route('invoice', ['id' => $pos->invoice->first()->id]) }}">{{ $pos->invoice->first()->full_code }}</a>
          @endif
        </span>
        <span>{{ as_pln($pos->comment) }}</span>
    </div>
    @empty
    <p class="grayed-out">Brak danych</p>
    @endforelse
  </div>
  {{ $gains->links() }}
</section>

<div>
  <x-button action="{{ route('costs') }}" label="Koszty" icon="money-bill-wave" />
</div>

@endsection