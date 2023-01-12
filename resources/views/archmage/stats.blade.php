@extends('layouts.app', compact("title"))

@section('content')
<section class="sc-line">
    <x-sc-scissors />
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
        <h1><i class="fa-solid fa-users"></i> ...</h1>
    </div>
</section>

@endsection
