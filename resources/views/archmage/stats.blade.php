@extends('layouts.app', compact("title"))

@section('content')
<section id="clients-stats" class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
        <h1>
            <i class="fa-solid fa-chart-pie"></i> Podsumowanie całego dorobku
        </h1>
        Liczba skończonych questów, zarobki total, całkowity czas trwania biznesu
    </div>
</section>

<section id="clients-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-users"></i> Lista klientów</h1>
    </div>
</section>

@endsection
