@extends('layouts.app', compact("title"))

@section('content')
<section>
    <div class="section-header">
        <h1>
        <i class="fa-solid fa-chart-pie"></i> Rozmiary sejfów
        </h1>
    </div>

    <style>
    .table-row{
        grid-template-columns: 1fr 1fr 3fr;
        gap: 0.5em;
    }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Sejf</span>
            <span>Ostatnia modyfikacja</span>
            <span>Rozmiar</span>
        </div>
    @forelse ($sizes as $safe => $size)
        <div class="table-row">
            <span>
                <a href="{{ route('songs', ['search' => preg_replace('/.*\/(.{4}).*/', '$1', $safe)]) }}">
                    {{ $safe }}
                </a>
            </span>
            <span {{ Popper::pop($times[$safe]) }}
                @if ($times[$safe]->diffInDays() >= setting("safe_old_enough"))
                class="success"
                @endif
                >
                {{ $times[$safe]->diffForHumans() }}
            </span>
            <div class="bar-container horizontal">
                <div class="bar" style='width:{{ $size/max($sizes)*100 }}%'></div>
                <span class="value">
                    {{ number_format($size / pow(2, 20), 2, ",", " ") }} MB
                </span>
            </div>
        </div>
    @empty
        <div class="table-row">
            <p class="grayed-out">Pusto...</p>
        </div>
    @endforelse
    </div>
</section>

@endsection
