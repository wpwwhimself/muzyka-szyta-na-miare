@extends('layouts.app', compact("title"))

@section('content')
    <div class="grid-2">
        <section id="who-am-i" class="sc-line">
            <x-sc-scissors />
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
                    <span>
                        {{ $gains["this_month"] }} zł
                        <small class="{{ $gains['monthly_diff'] >= 0 ? 'success' : 'error' }}">
                            ({{ sprintf("%+d", $gains["monthly_diff"]) }})
                        </small>
                    </span>

                    <span>Zarobki razem</span>
                    <span>{{ number_format($gains["total"], 2, ",", " ") }} zł</span>
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
        @forelse ($quests as $key => $quest)
            <x-quest-mini :quest="$quest" :queue="$key + 1" />
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

    @if (count($unpaids) > 0)
    <section id="dashboard-unpaids">
        <div class="section-header">
            <h1><i class="fa-solid fa-receipt"></i> Nadal nie zapłacili</h1>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Klient</th>
                    <th>Zaległe projekty</th>
                    <th>Razem do zapłaty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($unpaids as $client_id => $quests)
                <tr>
                    <td><a href="{{ route("clients") }}#client{{ $client_id }}">{{ $quests[0]->client->client_name }}</a></td>
                    <td class="quest-list">
                        @php $amount_to_pay = 0 @endphp
                        @foreach ($quests as $quest)
                        <a href="{{ route("quest", ["id" => $quest->id]) }}">
                            {{ $quest->song->title }}
                            <i class="fa-solid {{ $quest->status->status_symbol }}" {{ Popper::pop($quest->status->status_name) }}></i>
                            {{ $quest->price }} zł
                        </a>
                        @php $amount_to_pay += $quest->price @endphp
                        @endforeach
                    </td>
                    <td>
                        {{ $amount_to_pay }} zł
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
    @endif
@endsection
