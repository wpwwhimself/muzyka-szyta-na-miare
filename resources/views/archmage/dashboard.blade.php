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
            <h2>🧙‍♂️ arcymag we własnej osobie</h2>
        </section>

        <section id="dashboard-finances">
            <div class="section-header">
                <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
            </div>
            
            <div class="hint-table">
                <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
                <div class="positions">
                    <span>Zaakceptowane do zapłacenia</span>
                    <span>{{ quests_unpaid(1) }} zł</span>

                    <span>Wszystkie do zapłacenia</span>
                    <span>{{ quests_unpaid(1, true) }} zł</span>

                    <span>Zarobki w tym miesiącu</span>
                    <span>{{ $gains["this_month"] }} zł</span>

                    <span>Zarobki razem</span>
                    <span>{{ $gains["total"] }} zł</span>
                </div>
            </div>
        </section>
    </div>

    <section id="dashboard-requests">
        <div class="section-header">
            <h1><i class="fa-solid fa-envelope"></i> Zapytania</h1>
            <div>
                <x-a href="{{ route('requests') }}">Wszystkie</x-a>
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

    @if (count($patrons_adepts) > 0)
    <section id="patrons-adepts">
        <div class="section-header">
            <h1><i class="fa-solid fa-chalkboard-user"></i> Potencjalni patroni</h1>
            <div>
                <x-a href="https://www.facebook.com/wpwwMuzykaSzytaNaMiare/reviews" target="_blank">Recenzje</x-a>
            </div>
        </div>
        <div class="dashboard-mini-wrapper">
            @foreach ($patrons_adepts as $patron)
            <x-button
                label="{{ $patron->client_name }}" icon="{{ is_veteran($patron->id) ? 'user-shield' : 'user' }}"
                action="{{ route('patron-mode', ['id' => $patron->id, 'level' => 2]) }}"
                />
            @endforeach
        </div>
    </section>
    @endif

    <section id="dashboard-unpaids">
        <div class="section-header">
            <h1><i class="fa-solid fa-receipt"></i> Nadal nie zapłacili</h1>
        </div>
        <div class="dashboard-mini-wrapper">
        @forelse ($unpaids as $quest)
            <x-quest-mini :quest="$quest" />
        @empty
            <p class="grayed-out">O kurczę, wszyscy zapłacili</p>
        @endforelse
        </div>
    </section>
@endsection
