@extends('layouts.app')
@section("title", "Pulpit klienta")

@section('content')

<div class="grid" style="--col-count: 2;">
    <x-section id="who-am-i" class="sc-line"
        :title="Auth::user()->notes->client_name"
        :icon="model_icon('user_notes')"
    >
        <x-slot name="buttons">
            <x-tutorial>
                To jest Twój pulpit klienta. Znajdziesz tu m.in. podsumowanie Twoich zleceń oraz informacje dotyczące spraw finansowych.
            </x-tutorial>
            <x-a href="{{ route('client-view', ['id' => Auth::id()]) }}">Edytuj profil</x-a>
        </x-slot>

        <x-sc-scissors />

        <div class="hint-table">
            <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
            <div class="positions">
                <span>Ukończonych zleceń</span>
                <span>{{ $quests_total }}</span>

                <span>Status klienta</span>
                <span>
                    @if (Auth::user()->notes->trust == -1)
                    <span class="error"><x-shipyard.app.icon name="ninja" /></span> niezaufany
                    @elseif (Auth::user()->notes->is_veteran)
                    <span><x-shipyard.app.icon name="shield-account" /></span> stały klient
                    @else
                    <span><x-shipyard.app.icon name="account" /></span> klient początkujący<br>
                    <i>pozostało zleceń: {{ setting("msznm_veteran_from") - $quests_total }}</i>
                    @endif
                </span>

                @if (Auth::user()->notes->is_patron)
                <span>Pomoc w reklamie</span>
                <span>odnotowana</span>
                @endif

                <span>Łącznie zniżek</span>
                <span>
                    {{
                        Auth::user()->notes->special_prices ? "spersonalizowany cennik"
                        : (
                            (Auth::user()->notes->is_veteran) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
                            +
                            (Auth::user()->notes->is_patron) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
                        )*100 . "%"
                    }}
                </span>
            </div>
        </div>

        @if (Auth::user()->notes->trust == -1)
        <br>
        <div class="section-header error">
            <h1><x-shipyard.app.icon name="ninja" /> Jesteś na czarnej liście!</h1>
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

        @if ($quests_total && !Auth::user()->notes->is_patron && Auth::user()->notes->helped_showcasing != 1)
        <br>
        <div class="section-header showcase-highlight">
            <h1><x-shipyard.app.icon name="seal" /> Oceń naszą współpracę</h1>
        </div>
        <p>
            Recenzje pomagają mi pozyskiwać nowych klientów.
            Jeśli i Tobie przypadły do gustu efekty moich prac,
            możesz dać o tym znać innym i uzyskać <strong class="showcase-highlight">dodatkowe 5% zniżki na kolejne zlecenia</strong>!
        </p>
        <form>
            <x-button
                label="Przejdź do mojego fanpage'a" icon="open-in-new" target="_blank"
                action="https://www.facebook.com/muzykaszytanamiarepl/reviews"
                />
            <p>
                Po wystawieniu opinii kliknij przycisk poniżej – wtedy sprawdzę opinię i przyznam zniżkę.
                <x-warning>
                    Zwróć uwagę, żeby widoczność posta była ustawiona na <strong>Wszyscy</strong>.
                    Inaczej nie będę mógł stwierdzić, że faktycznie napisał{{ client_polonize(Auth::user()->notes->client_name)['kobieta'] ? 'aś' : 'eś' }} opinię.
                </x-warning>
            </p>
            <x-button
                label="Właśnie wystawił{{ client_polonize(Auth::user()->notes->client_name)['kobieta'] ? 'am' : 'em' }} opinię" icon="signature"
                action="{{ route('patron-mode', ['client_id' => Auth::id(), 'level' => 1]) }}"
                />
        </form>
        @endif
    </x-section>

    <x-section id="dashboard-finances" title="Finanse" :icon="model_icon('prices')">
        <h2 @if(Auth::user()->notes->trust == -1) class="error" @endif>Do zapłacenia za zlecenia</h2>
        <div class="hint-table">
            <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
            <div class="positions">
                <span>Zaakceptowane</span>
                <span>{{ as_pln(quests_unpaid(Auth::id())) }}</span>

                <span>Wszystkie</span>
                <span>{{ as_pln(quests_unpaid(Auth::id(), true)) }}</span>
            </div>
        </div>

        <h2>
            Stan konta:
            {{ as_pln(Auth::user()->notes->budget) }}

            <x-tutorial>
                Jeśli zdarzy Ci się wpłacić więcej, niż to było planowane, to odnotuję tę różnicę i wpiszę ją na poczet przyszlych zleceń.
            </x-tutorial>
        </h2>

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
    </x-section>
</div>

<x-section title="Zlecenia wymagające odpowiedzi" icon="bell" id="dashboarrd-quests-review">
    <x-slot name="buttons">
        <x-tutorial>
            Kliknij na poniższe wiersze, aby zobaczyć szczegóły zlecenia. Możesz najechać na większość symboli, aby pokazać ich znaczenie.
        </x-tutorial>
        <x-a href="{{ route('quests') }}">Wszystkie</x-a>
    </x-slot>

    @forelse ($quests_review as $quest)
    <x-quests.tile :quest="$quest" />
    @empty
    <p class="grayed-out">brak aktywnych zleceń</p>
    @endforelse
</x-section>

<div class="grid" style="--col-count: 2;">
    <x-section title="Aktualne zlecenia" :icon="model_icon('quests')" id="dashboard-quests-ongoing">
        <x-slot name="buttons">
            <x-a :href="route('quests')">Wszystkie</x-a>
        </x-slot>

        @forelse ($quests_ongoing as $quest)
        <x-quests.tile :quest="$quest" />
        @empty
        <p class="grayed-out">brak aktywnych zleceń</p>
        @endforelse
    </x-section>

    <x-section title="Aktualne zapytania" :icon="model_icon('requests')" id="dashboard-requests">
        <x-slot name="buttons">
            <x-a :href="route('requests')">Wszystkie</x-a>
        </x-slot>

        @forelse ($requests as $request)
        <x-requests.tile :request="$request" />
        @empty
        <p class="grayed-out">brak aktywnych zapytań</p>
        @endforelse
    </x-section>
</div>

<div class="flex right center">
    @unless (Auth::user()->notes->trust == -1)
    <x-shipyard.ui.button
        label="Złóż zapytanie o podkład/nuty"
        icon="send"
        action="none"
        onclick="openModal('send-podklady-request', {
            client_id: {{ Auth::user()?->id ?? 'null' }},
            client_name: '{{ Auth::user()?->notes?->client_name }}' || null,
            email: '{{ Auth::user()?->notes?->email }}' || null,
            phone: '{{ Auth::user()?->notes?->phone }}' || null,
            other_medium: '{{ Auth::user()?->notes?->other_medium }}' || null,
        })"
        class="primary"
    />
    @endunless
</div>

@endsection
