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
                <div>
                    <x-a href="{{ route('stats') }}">Więcej</x-a>
                </div>
            </div>

            <div class="hint-table">
                <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
                <div class="positions">
                    <span>Zaakceptowane do zapłacenia</span>
                    <span>{{ quests_unpaid(1) }} zł</span>

                    <span>Wszystkie do zapłacenia</span>
                    <span>{{ quests_unpaid(1, true) }} zł</span>

                    <span>Zarobki z ostatnich 30 dni</span>
                    <span>
                        {{ number_format($gains["this_month"], 2, ",", " ") }} zł
                        <small class="{{ $gains['monthly_diff'] >= 0 ? 'success' : 'error' }}">
                            ({{ sprintf("%+d", $gains["monthly_diff"]) }})
                        </small>
                    </span>
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
            <h1><i class="fa-solid fa-signal"></i> Kolejka zleceń</h1>
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

    <div class="grid-2">
        <section>
            <div class="section-header">
                <h1><i class="fa-solid fa-clock-rotate-left"></i> Ostatnie zmiany</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ReQuest</th>
                        <th>Klient</th>
                        <th>Status</th>
                        <th>Kiedy</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recent as $change)
                    <tr>
                        <td>
                            <a href="{{ route(($change->is_request) ? 'request' : 'quest', ['id' => $change->re_quest_id]) }}">
                                @if ($change->is_request)
                                <i class="fa-regular fa-square" @popper(zapytanie)></i>
                                @else
                                <i class="fa-solid fa-square-check" @popper(zlecenie)></i>
                                @endif
                                {{ (($change->is_request) ? $change->re_quest->title : $change->re_quest->song->title) ?? "utwór bez tytułu" }}
                            </a>
                        </td>
                        <td>{{ ($change->is_request) ? $change->re_quest->client?->client_name ?? $change->re_quest->client_name : $change->re_quest->client->client_name }}</td>
                        <td>
                            <x-phase-indicator-mini :status="$change->new_status" />
                        </td>
                        <td>
                            {{ $change->date->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan=3 class="grayed-out">brak ostatnich zleceń</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

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
                                {{ $quest->song->title ?? "utwór bez tytułu" }}
                                <x-phase-indicator-mini :status="$quest->status" />
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

        @if (!empty($janitor_log))
        <section id="dashboard-janitor-log">
            <div class="section-header">
                <h1><i class="fa-solid fa-broom"></i> Raport Sprzątacza</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ReQuest</th>
                        <th>Wykonano</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($janitor_log as $i)
                    <tr>
                        <td>
                            <a href="{{ route($i->is_request ? 'request' : 'quest', ["id" => $i->re_quest->id]) }}">
                            {{ $i->is_request ? $i->re_quest->title : $i->re_quest->song->title }}
                            </a>
                        </td>
                        <td>
                            {{ $i->operation }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @endif

        @if (count($patrons_adepts) > 0)
        <section id="patrons-adepts">
            <div class="section-header">
                <h1><i class="fa-solid fa-chalkboard-user"></i> Potencjalni patroni</h1>
                <div>
                    <x-a href="https://www.facebook.com/wpwwMuzykaSzytaNaMiare/reviews" target="_blank">Recenzje</x-a>
                </div>
            </div>
            <table>
                <thead>
                    <th>Klient</th>
                    <th>Decyzja</th>
                </thead>
                <tbody>
                    @foreach ($patrons_adepts as $patron)
                    <tr>
                        <td><i class="fa-solid fa-{{ is_veteran($patron->id) ? 'user-shield' : 'user' }}"></i> {{ $patron->client_name }}</td>
                        <td>
                            <x-button label="" icon="check" action="{{ route('patron-mode', ['id' => $patron->id, 'level' => 2]) }}" :small="true" />
                            <x-button label="" icon="x" action="{{ route('patron-mode', ['id' => $patron->id, 'level' => 0]) }}" :small="true" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @endif
    </div>
@endsection
