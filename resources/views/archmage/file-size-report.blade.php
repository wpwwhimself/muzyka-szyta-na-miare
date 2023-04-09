@extends('layouts.app', compact("title"))

@section('content')
<section>
    <div class="section-header">
        <h1>
        <i class="fa-solid fa-chart-pie"></i> Rozmiary sejfów
        </h1>
    </div>

    <style>
    .grid-2{
        grid-template-columns: auto 1fr;
        gap: 0.5em;
    }
    </style>
    <div class="grid-2">
    @foreach ($sizes as $safe => $size)
        <span>{{ $safe }}</span>
        <div class="bar-container horizontal">
            <div class="bar" style='width:{{ $size/max($sizes)*100 }}%'></div>
            <span class="value">
                {{ number_format($size / pow(2, 20), 2, ",", " ") }} MB
            </span>
        </div>
    @endforeach
    </div>
</section>

@endsection
