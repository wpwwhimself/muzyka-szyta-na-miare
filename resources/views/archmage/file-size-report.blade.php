@extends('layouts.app', compact("title"))

@section('content')
<x-section title="Podsumowanie" icon="chart-column">
    <x-stats-highlight-h :data="[
        'Całkowity rozmiar' => number_format(
            array_reduce($sizes, fn($total, $safe) => $total + $safe, 0) / pow(2, 20),
            2, ',', ' '
        ) . ' MB',
    ]" />
</x-section>

<x-section title="Rozmiary sejfów" icon="chart-pie">
    <style>
    .table-row{
        grid-template-columns: 1fr 1fr 3fr;
        gap: var(--size-xs);
    }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Utwór (Sejf)</span>
            <span>Ostatnia modyfikacja</span>
            <span>Rozmiar</span>
        </div>
    @forelse ($sizes as $safe => $size)
        <div class="table-row">
            <span>
                @if ($songs[$safe])
                <a href="{{ route('songs', ['search' => $songs[$safe]->id]) }}" class="flex-down">
                    {{ $songs[$safe]->full_title }}
                    <small>{{ $safe }}</small>
                </a>
                @else
                <small>{{ $safe }}</small>
                @endif
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
</x-section>

@endsection
