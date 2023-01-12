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
    <div id="clients-stats-graph">
        @foreach ($clients_counts as $label => $value)
        <div class="bar-container"><div class="bar" style='height:{{ $value*2 }}px'></div></div>
        <div class="label">{{ $label }}</div>
        <div class="value">{{ $value }}</div>
        @endforeach
    </div>
</section>

@endsection
