@extends('layouts.app', compact("title"))

@section('content')
    @foreach (["success", "error"] as $status)
        @if (session($status))
            <x-alert :status="$status" />
        @endif
    @endforeach

    <div class="grid-2">
        <section id="who-am-i">
            <div class="section-header">
                <h1><i class="fa-solid fa-user-check"></i> Zalogowany jako</h1>
            </div>
            <h2>
                @if (is_veteran(Auth::id()))
                <i class="fa-solid fa-user-shield" @popper(stały klient)></i>
                @else
                <i class="fa-solid fa-user" @popper(zwykły klient)></i>
                @endif
                {{ Auth::user()->client->client_name }}
            </h2>
            <div class="hint-table">
                <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
                <div class="positions">
                    <span>Ukończonych zleceń</span>
                    <span>{{ $quests_total }}</span>

                    <span>Status klienta</span>
                    <span>
                        @if (is_veteran(Auth::id()))
                        stały klient
                        @else
                        klient zwykły<br>
                        <i>pozostało zleceń: {{ DB::table("settings")->where("setting_name", "veteran_from")->value("value_str") - $quests_total }}</i>
                        @endif
                    </span>

                    @if (is_patron(Auth::id()))
                    <span>Pomoc w reklamie</span>
                    <span>odnotowana</span>
                    @endif

                    <span>Łącznie zniżek</span>
                    <span>
                        {{
                            Auth::user()->client->special_prices ? "spersonalizowany cennik"
                            : (
                                is_veteran(Auth::id()) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
                                + 
                                is_patron(Auth::id()) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
                            )*100 . "%"
                        }}
                    </span>
                </div>
            </div>
            
            @if ($quests_total && !is_patron(Auth::id()) && Auth::user()->client->helped_showcasing != 1)
            <br>
            <div class="section-header showcase-highlight">
                <h1><i class="fa-solid fa-award"></i> Jak Ci się podoba współpraca?</h1>
            </div>
            <p>Recenzje pomagają mi pozyskiwać nowych klientów. Jeśli i Tobie przypadły do gustu efekty moich prac, możesz dać o tym znać innym i uzyskać <strong class="showcase-highlight">dodatkowe 5% zniżki na kolejne zlecenia</strong>!</p>
            <h4><a class="showcase-highlight" href="https://www.facebook.com/wpwwMuzykaSzytaNaMiare/reviews" target="_blank">Przejdź do mojego fanpage'a</a></h4>
            <form>
                <x-button
                    label="Opinia wystawiona" icon="fa-signature"
                    action="{{ route('patron-mode', ['id' => Auth::id(), 'level' => 1]) }}"
                    />
            </form>
            @endif
        </section>

        <section id="dashboard-finances">
            <div class="section-header">
                <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
            </div>
            <div class="dashboard-mini-wrapper">
                🚧 TBD 🚧
            </div>
        </section>
    </div>

    <section id="dashboard-requests">
        <div class="section-header">
            <h1><i class="fa-solid fa-envelope"></i> Zapytania</h1>
            <div>
                <x-a href="{{ route('quests') }}">Wszystkie</x-a>
                <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe</x-a>
            </div>
        </div>
        <div class="dashboard-mini-wrapper">
        @forelse ($requests as $request)
            <x-quest-mini :quest="$request" />
        @empty
            <p class="grayed-out">brak aktywnych zapytań</p>
        @endforelse
        </div>
    </section>

    <section id="dashboard-quests">
        <div class="section-header">
            <h1><i class="fa-solid fa-gears"></i> Aktualne zlecenia</h1>
            <div>
                <x-a href="{{ route('quests') }}">Wszystkie</x-a>
            </div>
        </div>
        <div class="dashboard-mini-wrapper">
        @forelse ($quests as $quest)
            <x-quest-mini :quest="$quest" />
        @empty
            <p class="grayed-out">brak aktywnych zleceń</p>
        @endforelse
        </div>
    </section>
@endsection
