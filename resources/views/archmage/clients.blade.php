@extends('layouts.app', compact("title"))

@section('content')

<div>
    <x-button action="{{ route('client-mail-prepare') }}" label="Napisz maila" icon="envelope" />
</div>

<section class="sc-line">
    <x-sc-scissors />
    <div class="section-header">
        <h1><i class="fa-solid fa-filter"></i> Filtruj listę</h1>
    </div>
    <div>
        @foreach ([
            ["wszyscy", "users", null, null],
            ["krętacze", "user-ninja", "trust", -1],
            ["zaufani", "hand-holding-heart", "trust", 1],
            ["ulubieni", "heart", "trust", 2],
            ["zapomniani", "ghost", "is_forgotten", 1],
            ["z budżetem", "sack-dollar", "budget", 0],
            ["(prawie) patroni", "award", "helped_showcasing", 0],
            ["wolący telefon", "phone", "contact_preference", "telefon"],
        ] as [$label, $icon, $param, $value])
            @unless (url()->current() == route("clients", compact('param', 'value')))
            <x-button label="{{ $label }}" icon="{{ $icon }}" action="{{ route('clients', compact('param', 'value')) }}" :small="true" />
            @endunless
        @endforeach
    </div>
</section>
<section id="clients-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-users"></i> Lista klientów</h1>
        <form method="get" id="search" class="flex-right" action="">
            <input type="text" name="search" class="small" value="{{ $search }}" />
            <x-button action="submit" icon="magnifying-glass" label="" :small="true" />
        </form>
    </div>
    <style>
    .table-row{ grid-template-columns: 4em 4fr 4fr 8em; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span @popper(doświadczenie klienta)><i class="fa-solid fa-bars-progress"></i></span>
            <span>Nazwisko</span>
            <span>Dane kontaktowe</span>
            <span>Znany od</span>
        </div>
        <hr />
        @forelse ($clients as $name => $client_class)
            <h2>
            @switch($name)
                @case($classes[0])
                    <i class="fa-solid fa-user-shield"></i> {{ $name }} <small class="ghost">{{ VETERAN_FROM() }}+ zleceń</small>
                    @break
                @case($classes[1])
                    <i class="fa-solid fa-user"></i> {{ $name }} <small class="ghost">4+ zleceń</small>
                    @break
                @case($classes[2])
                    <i class="fa-solid fa-user"></i> {{ $name }} <small class="ghost">2-3 zlecenia</small>
                    @break
                @case($classes[3])
                    <i class="fa-solid fa-user"></i> {{ $name }} <small class="ghost">1 zlecenie</small>
                    @break
                @case($classes[4])
                    <i class="fa-regular fa-user"></i> {{ $name }} <small class="ghost">0 zleceń</small>
                    @break
                @default
            @endswitch
            </h2>

            @foreach ($client_class as $client)
            <div class="table-row" id="client{{ $client->id }}">
                <span class="client-exp">
                    <a href="{{ route('quests', ['client' => $client->id]) }}">
                        {{ $client->exp }}
                        @if ($client->upcoming_quests_count)
                        <span class="upcoming-quests">+{{ $client->upcoming_quests_count }}</span>
                        @endif
                    </a>
                </span>
                <span class="client-main-data">
                    <h3 class="song-title">
                        <a href="{{ route('client-view', ['id' => $client->id]) }}">
                            {!! $client !!}
                        </a>
                    </h3>
                    <progress id="client_exp" value="{{ $client->exp }}" max="{{ $client->is_veteran ? $max_exp : VETERAN_FROM() }}"></progress>
                    <br />
                    <span class="ghost">
                        <span @popper(wybredność) class="{{ $client->pickiness > 1 ? 'error' : 'success' }}">
                            {{ round($client->pickiness * 100) }}%
                        </span>
                        •
                        {{ _ct_($client->password) }}
                    </span>
                </span>
                <span class="contact-info">
                    <span {{ in_array($client->contact_preference, ["email"]) ? : "class=ghost" }}><a href="mailto:{{ _ct_($client->email) }}">{{ _ct_($client->email) }}</a></span>
                    <span {{ in_array($client->contact_preference, ["telefon", "sms"]) ?: "class=ghost" }}>{{ _ct_(implode(" ", str_split($client->phone, 3))) }}</span>
                    <span {{ !in_array($client->contact_preference, ["email", "telefon", "sms"]) ?: "class=ghost" }}>{{ _ct_($client->other_medium) }}</span>
                </span>
                <span {{ Popper::pop($client->created_at->toDateString()) }}>
                    {{ $client->created_at->diffForHumans() }}
                </span>
            </div>
            @endforeach
        @empty
        <p class="grayed-out">brak klientów</p>
        @endforelse
    </div>
</section>

@endsection
