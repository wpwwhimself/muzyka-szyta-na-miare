@extends('layouts.app', compact("title"))

@section('content')
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
            <span>Budżet</span>
            <span>Wyjątki</span>
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
                    <a href="{{ route('quests', ["client_id" => $client->id]) }}">
                        {{ $client->exp }}
                        @if (upcoming_quests($client->id))
                        <span class="upcoming-quests">+{{ upcoming_quests($client->id) }}</span>
                        @endif
                    </a>
                </span>
                <span class="client-main-data">
                    <h3 class="song-title">{{ $client->client_name }}</h3>
                    <progress id="client_exp" value="{{ $client->exp }}" max="{{ is_veteran($client->id) ? $max_exp : VETERAN_FROM() }}"></progress>
                    <br />
                    <span class="ghost">{{ $client->id }} • {{ $client->user->password }}</span>
                </span>
                <span class="contact-info">
                    <span {{ in_array($client->contact_preference, ["email"]) ? : "class=ghost" }}><a href="mailto:{{ $client->email }}">{{ $client->email }}</a></span>
                    <span {{ in_array($client->contact_preference, ["telefon", "sms"]) ?: "class=ghost" }}>{{ implode(" ", str_split($client->phone, 3)) }}</span>
                    <span {{ !in_array($client->contact_preference, ["email", "telefon", "sms"]) ?: "class=ghost" }}>{{ $client->other_medium }}</span>
                </span>
                <span>
                    {{ $client->created_at->diffForHumans() }}<br>
                    {{ $client->created_at->toDateString() }}
                </span>
                <span class="client-budget {{ $client->budget ?: 'ghost' }}">
                    <x-input type="number" name="budget_mod_{{ $client->id }}" label="" value="{{ $client->budget }}" :small="true" />
                    <script>
                    $(document).ready(function(){
                        $("#budget_mod_{{ $client->id }}").change(function(){
                            $.ajax({
                                url: "{{ url('budget_update') }}",
                                type: "post",
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    client_id: '{{ $client->id }}',
                                    new_budget: $("#budget_mod_{{ $client->id }}").val()
                                },
                                success: function(){
                                    location.reload();
                                }
                            })
                        });
                    });
                    </script>
                </span>
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
