@extends('layouts.app', compact("title"))

@section('content')

@foreach (["success", "error"] as $status)
@if (session($status))
    <x-alert :status="$status" />
@endif
@endforeach

<section id="clients-stats">
    <div class="section-header">
        <h1><i class="fa-solid fa-clipboard-user"></i> Statystyki klientów</h1>
    </div>
    <div id="clients-stats-graph">
        @foreach (array_reverse($clients) as $k => $v)
        <div class="bar-container"><div class="bar" style='height:{{ count($v)*2 }}px'></div></div>
        <div class="label">{{ $classes[count($clients) - $k - 1] }}</div>
        <div class="value">{{ count($v) }}</div>
        @endforeach
    </div>
</section>

<section id="clients-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-users"></i> Lista klientów</h1>
    </div>
    <style>
    .table-row{ grid-template-columns: 4em 4fr 4fr 8em 1fr 1fr; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span @popper(doświadczenie klienta)><i class="fa-solid fa-bars-progress"></i></span>
            <span>Nazwisko</span>
            <span>Dane kontaktowe</span>
            <span>Klient od</span>
            <span @popper(budżet)><i class="fa-solid fa-vault"></i></span>
            <span>Wyjątki</span>
        </div>
        <hr />
        @forelse ($clients as $name => $client_class)
            <h2>
            @switch($name)
                @case(0)
                    <i class="fa-solid fa-user-shield"></i> {{ $classes[0] }} <small class="ghost">{{ VETERAN_FROM() }}+ zleceń</small>
                    @break
                @case(1)
                    <i class="fa-solid fa-user"></i> {{ $classes[1] }} <small class="ghost">4+ zleceń</small>
                    @break
                @case(2)
                    <i class="fa-solid fa-user"></i> {{ $classes[2] }} <small class="ghost">2-3 zlecenia</small>
                    @break
                @case(3)
                    <i class="fa-solid fa-user"></i> {{ $classes[3] }} <small class="ghost">1 zlecenie</small>
                    @break
                @case(4)
                    <i class="fa-regular fa-user"></i> {{ $classes[4] }} <small class="ghost">0 zleceń</small>
                    @break
                @default
            @endswitch
            </h2>

            @foreach ($client_class as $client)
            <div class="table-row">
                <span class="client-exp">
                    {{ $client->exp }} @if (upcoming_quests($client->id))
                    <span class="upcoming-quests">+{{ upcoming_quests($client->id) }}</span>
                    @endif
                </span>
                <span>
                    <h3 class="song-title">{{ $client->client_name }}</h3>
                    <progress id="client_exp" value="{{ $client->exp }}" max="{{ is_veteran($client->id) ? $max_exp : VETERAN_FROM() }}"></progress>
                    <br />
                    <span class="ghost">{{ $client->id }} • {{ $client->user->password }}</span>
                </span>
                <span class="contact-info">
                    <span {{ in_array($client->contact_preference, ["email"]) ? : "class=ghost" }}>{{ $client->email }}</span>
                    <span {{ in_array($client->contact_preference, ["telefon", "sms"]) ?: "class=ghost" }}>{{ implode(" ", str_split($client->phone, 3)) }}</span>
                    <span {{ !in_array($client->contact_preference, ["email", "telefon", "sms"]) ?: "class=ghost" }}>{{ $client->other_medium }}</span>
                </span>
                <span>{{ $client->created_at->toDateString() }}</span>
                <span {{ $client->budget ?: "class=ghost" }}>{{ $client->budget }} zł</span>
                <span>
                    @switch($client->trust)
                        @case(1)
                            <i @popper(ponadprzeciętne zaufanie) class="success fa-solid fa-hand-holding-heart"></i>
                            @break
                        @case(-1)
                            <i @popper(krętacz i oszust) class="error fa-solid fa-user-ninja"></i>
                            @break
                        @default
                    @endswitch
                    @if ($client->special_prices)
                    <i class="success fa-solid fa-address-card" {{ Popper::pop("Niestandardowe ceny:<br>".client->special_prices) }}></i>
                    @endif
                    @if ($client->default_wishes)
                    <i class="fa-solid fa-cloud" {{ Popper::pop("Domyślne życzenia:<br>".$client->default_wishes) }}></i>
                    @endif
                    @if (is_patron($client->id))
                    <i class="showcase-highlight fa-solid fa-award" @popper(patron)></i>
                    @endif
                </span>
            </div>
            @endforeach
        @empty
        <p class="grayed-out">brak klientów</p>
        @endforelse
    </div>
</section>

@endsection
