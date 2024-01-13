@extends('layouts.app', compact("title"))

@section('content')
    <p class="tutorial">
        <i class="fa-solid fa-circle-question"></i>
        To jest Twój pulpit klienta. Znajdziesz tu m.in. podsumowanie Twoich zleceń oraz informacje dotyczące spraw finansowych.
    </p>
    <div class="grid-2">
        <section id="who-am-i" class="sc-line">
            <x-sc-scissors />
            <div class="section-header">
                <h1><i class="fa-solid fa-user-check"></i> {{ Auth::user()->client->client_name }}</h1>
                <x-a href="{{ route('client-view', ['id' => Auth::id()]) }}">Edytuj profil</x-a>
            </div>
            <div class="hint-table">
                <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
                <div class="positions">
                    <span>Ukończonych zleceń</span>
                    <span>{{ $quests_total }}</span>

                    <span>Status klienta</span>
                    <span>
                        @if (Auth::user()->client->trust == -1)
                        <i class="fa-solid fa-user-ninja error"></i> niezaufany
                        @elseif (Auth::user()->client->is_veteran)
                        <i class="fa-solid fa-user-shield"></i> stały klient
                        @else
                        <i class="fa-solid fa-user"></i> klient początkujący<br>
                        <i>pozostało zleceń: {{ setting("veteran_from") - $quests_total }}</i>
                        @endif
                    </span>

                    @if (Auth::user()->client->is_patron)
                    <span>Pomoc w reklamie</span>
                    <span>odnotowana</span>
                    @endif

                    <span>Łącznie zniżek</span>
                    <span>
                        {{
                            Auth::user()->client->special_prices ? "spersonalizowany cennik"
                            : (
                                (Auth::user()->client->is_veteran) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
                                +
                                (Auth::user()->client->is_patron) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
                            )*100 . "%"
                        }}
                    </span>
                </div>
            </div>

            @if (Auth::user()->client->trust == -1)
            <br>
            <div class="section-header error">
                <h1><i class="fa-solid fa-user-ninja"></i> Jesteś na czarnej liście!</h1>
            </div>
            <p>
                Z powodu nieopłaconych przez bardzo długi czas projektów, ograniczyłem możliwości korzystania ze strony.
                Do momentu ich opłacenia nie możesz przeglądać udostępnionych plików.
            </p>
            <h2 class="error">Nieopłacone zlecenia</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tytuł</th>
                        <th>Kwota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($unpaids as $quest)
                    <tr>
                        <td>
                            <a href="{{ route('quest', ['id' => $quest->id]) }}">
                            {{ $quest->song->title ?? "utwór bez tytułu" }}
                            </a>
                        </td>
                        <td>{{ as_pln($quest->price) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            @if ($quests_total && !Auth::user()->client->is_patron && Auth::user()->client->helped_showcasing != 1)
            <br>
            <div class="section-header showcase-highlight">
                <h1><i class="fa-solid fa-award"></i> Oceń naszą współpracę</h1>
            </div>
            <p>
                Recenzje pomagają mi pozyskiwać nowych klientów.
                Jeśli i Tobie przypadły do gustu efekty moich prac,
                możesz dać o tym znać innym i uzyskać <strong class="showcase-highlight">dodatkowe 5% zniżki na kolejne zlecenia</strong>!
            </p>
            <form>
                <x-button
                    label="Przejdź do mojego fanpage'a" icon="up-right-from-square" target="_blank"
                    action="https://www.facebook.com/muzykaszytanamiarepl/reviews"
                    />
                <p>Po wystawieniu opinii kliknij przycisk poniżej – wtedy sprawdzę opinię i przyznam zniżkę.</p>
                <p class="yellowed-out">
                    Uwaga! Zwróć uwagę, żeby widoczność posta była ustawiona na <strong>Wszyscy</strong>.
                    Inaczej nie będę mógł stwierdzić, że faktycznie napisał{{ client_polonize(Auth::user()->client->client_name)['kobieta'] ? 'aś' : 'eś' }} opinię.
                </p>
                <x-button
                    label="Właśnie wystawił{{ client_polonize(Auth::user()->client->client_name)['kobieta'] ? 'am' : 'em' }} opinię" icon="signature"
                    action="{{ route('patron-mode', ['client_id' => Auth::id(), 'level' => 1]) }}"
                    />
            </form>
            @endif
        </section>

        <section id="dashboard-finances">
            <div class="section-header">
                <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
            </div>

            <h2 @if(Auth::user()->client->trust == -1) class="error" @endif>Do zapłacenia za zlecenia</h2>
            <div class="hint-table">
                <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
                <div class="positions">
                    <span>Zaakceptowane</span>
                    <span>{{ as_pln(quests_unpaid(Auth::id())) }}</span>

                    <span>Wszystkie</span>
                    <span>{{ as_pln(quests_unpaid(Auth::id(), true)) }}</span>
                </div>
            </div>

            <h2>Stan konta</h2>
            <p class="tutorial">
                <i class="fa-solid fa-circle-question"></i>
                Jeśli zdarzy Ci się wpłacić więcej, niż to było planowane, to odnotuję tę różnicę i wpiszę ją na poczet przyszlych zleceń.
            </p>
            <h3>{{ as_pln(Auth::user()->client->budget) }}</h3>

            <div class="section-header">
                <h1>
                    <i class="fa-solid fa-address-card"></i>
                    Dane do przelewu
                </h1>
            </div>
            <p>
                Numer konta:
                <b>58 1090 1607 0000 0001 5333 1539</b>
            </p>
            <p>
                W tytule proszę o wpisanie ID zlecenia dla łatwiejszej identyfikacji wpłaty.
                Więcej szczegółów znajdziesz w konkretnym zleceniu.
            </p>
            @if($unpaids->filter(fn($quest) => $quest->delayed_payment?->gte(Carbon\Carbon::today()))->count())
            <p class="yellowed-out">
                <i class="fas fa-triangle-exclamation"></i>
                Posiadasz nieopłacone zlecenia z opóźnionym terminem płatności.
                Zanim dokonasz przelewu, zwróć uwagę, czy nie wykonujesz go zbyt wcześnie.
            </p>
            @endif
        </section>
    </div>

    <p class="tutorial">
        <i class="fa-solid fa-circle-question"></i>
        Kliknij na poniższe okienka, aby zobaczyć szczegóły zlecenia.
        Możesz najechać na większość symboli, aby pokazać ich znaczenie.
    </p>

    <section id="dashboard-quests">
        <div class="section-header">
            <h1><i class="fa-solid fa-gears"></i> Aktualne zlecenia</h1>
            <div>
                <x-a href="{{ route('quests') }}">Wszystkie</x-a>
            </div>
        </div>
        <div class="dashboard-mini-wrapper">
        @php $quest_count = 0 @endphp
        @foreach ($quests as $key => $quest)
            @if ($quest->client_id == Auth::id())
            @php $quest_count++; @endphp
            <x-quest-mini :quest="$quest" :queue="$key + 1" />
            @endif
        @endforeach
        @unless($quest_count)
            <p class="grayed-out">brak aktywnych zleceń</p>
        @endunless
        </div>
    </section>

    <section id="dashboard-requests">
        <div class="section-header">
            <h1><i class="fa-solid fa-envelope"></i> Aktualne zapytania</h1>
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
        @unless (Auth::user()->client->trust == -1)
        <x-button
            action="{{ route('add-request') }}"
            label="Dodaj nowe zapytanie" icon="plus"
            />
        @endunless
    </div>
@endsection
