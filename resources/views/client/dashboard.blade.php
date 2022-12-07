@extends('layouts.app', compact("title"))

@section('content')
    <div class="grid-2">
        <section id="who-am-i" class="sc-line">
            <x-sc-scissors />
            <div class="section-header">
                <h1><i class="fa-solid fa-user-check"></i> {{ Auth::user()->client->client_name }}</h1>
            </div>
            <div class="hint-table">
                <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
                <div class="positions">
                    <span>Ukończonych zleceń</span>
                    <span>{{ $quests_total }}</span>

                    <span>Status klienta</span>
                    <span>
                        @if (is_veteran(Auth::id()))
                        <i class="fa-solid fa-user-shield"></i> stały klient
                        @else
                        <i class="fa-solid fa-user"></i> klient początkujący<br>
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
            <form>
                <x-button
                    label="Przejdź do mojego fanpage'a" icon="up-right-from-square" target="_blank"
                    action="https://www.facebook.com/wpwwMuzykaSzytaNaMiare/reviews"
                    />
                <x-button
                    label="Opinia wystawiona" icon="signature"
                    action="{{ route('patron-mode', ['id' => Auth::id(), 'level' => 1]) }}"
                    />
            </form>
            @endif
        </section>

        <section id="dashboard-finances">
            <div class="section-header">
                <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
            </div>

            <h2>Do zapłacenia za zlecenia</h2>
            <div class="hint-table">
                <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
                <div class="positions">
                    <span>Zaakceptowane</span>
                    <span>{{ quests_unpaid(Auth::id()) }} zł</span>

                    <span>Wszystkie</span>
                    <span>{{ quests_unpaid(Auth::id(), true) }} zł</span>
                </div>
            </div>

            <h2>Stan konta</h2>
            <p class="tutorial">
                <i class="fa-solid fa-circle-question"></i>
                Jeśli zdarzy Ci się wpłacić więcej, niż to było planowane, to odnotuję tę różnicę i wpiszę ją na poczet przyszlych zleceń.
            </p>
            <h3>{{ Auth::user()->client->budget }} zł</h3>
        </section>
    </div>

    <p class="tutorial">
        <i class="fa-solid fa-circle-question"></i>
        Kliknij na poniższe okienka, aby zobaczyć szczegóły zlecenia.
    </p>

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

    <section id="dashboard-requests">
        <div class="section-header">
            <h1><i class="fa-solid fa-envelope"></i> Zapytania</h1>
            <div>
                <x-a href="{{ route('quests') }}">Wszystkie</x-a>
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

    <div class="flex-right">
        <x-button
            action="{{ route('add-request') }}"
            label="Dodaj nowe zapytanie" icon="plus"
            />
    </div>
@endsection
