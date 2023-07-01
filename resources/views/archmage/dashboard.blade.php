@extends('layouts.app', compact("title"))

@section('content')
    <div class="grid-2">
        <section id="dashboard-finances">
            <div class="section-header">
                <h1><i class="fa-solid fa-sack-dollar"></i> Finanse</h1>
                <div>
                    <x-a href="{{ route('finance') }}">Więcej</x-a>
                </div>
            </div>

            <div class="hint-table">
                <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
                <div class="positions">
                    <span>Zaakceptowane do zapłacenia</span>
                    <span>{{ _c_(as_pln(quests_unpaid(1))) }}</span>

                    <span>Wszystkie do zapłacenia</span>
                    <span>{{ _c_(as_pln(quests_unpaid(1, true))) }}</span>

                    <span>Zarobki z ostatnich 30 dni</span>
                    <span>
                        {{ _c_(as_pln($gains["this_month"])) }}
                        <small class="{{ $gains['monthly_diff'] >= 0 ? 'success' : 'error' }}">
                            ({{ _c_(sprintf("%+d", $gains["monthly_diff"])) }})
                        </small>
                    </span>

                    <span>Zarobki w tym miesiącu</span>
                    <span>
                        {{ _c_(as_pln($gains_this_month)) }}
                        <small class="{{
                            ($gains_this_month >= 0.9 * INCOME_LIMIT())
                            ? 'error'
                            : ($gains_this_month >= 0.7 * INCOME_LIMIT()
                                ? 'warning'
                                : '')
                            }}">
                            ({{ _c_(round($gains_this_month / INCOME_LIMIT(), 2)*100) }}%)
                        </small>
                    </span>
                </div>
            </div>
        </section>

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
                    @forelse ($janitor_log as $i)
                    <tr>
                        <td>
                            <a href="{{ route($i->is_request ? 'request' : 'quest', ["id" => $i->re_quest->id]) }}">
                                {{ ($i->is_request ? $i->re_quest->title : $i->re_quest->song->title) ?? "bez tytułu" }}
                            </a>
                        </td>
                        <td>
                            {{ $i->operation }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan=2>
                            <span class="grayed-out">
                                <i class="fa-solid fa-bed"></i>
                                Sprzątacz dzisiaj śpi
                            </span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section id="recent">
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
                    @if ($change->date->gt(now()->subDay()))
                    <tr>
                    @else
                    <tr class="ghost">
                    @endif
                        <td>
                            <a href="{{ route(($change->is_request) ? 'request' : 'quest', ['id' => $change->re_quest_id]) }}">
                                {{ (($change->is_request) ? $change->re_quest->title : $change->re_quest->song->title) ?? "utwór bez tytułu" }}
                            </a>
                        </td>
                        <td>
                        @if ($change->is_request)
                            @if ($change->re_quest->client)
                                <a href="{{ route('clients', ['search' => $change->re_quest->client?->id]) }}">{{ _ct_($change->re_quest->client?->client_name) }}</a>
                            @else
                                {{ _ct_($change->re_quest->client_id) }}
                            @endif
                        @else
                            <a href="{{ route('clients', ['search' => $change->re_quest->client->id]) }}">{{ _ct_($change->re_quest->client->client_name) }}</a>
                        @endif
                        </td>
                        <td>
                            <x-phase-indicator-mini :status="$change->new_status" />
                        </td>
                        <td {{ Popper::pop($change->date) }}>
                            {{ $change->date->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan=3 class="grayed-out">brak ostatnich zleceń</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="sc-line">
            <x-sc-scissors />
            <div class="section-header">
                <h1>
                    <i class="fa-solid fa-calendar"></i>
                    Grafik najbliższych zleceń
                </h1>
                <div>
                    <x-a href="{{ route('quests-calendar') }}">Wszystkie</x-a>
                </div>
            </div>
            <x-calendar :click-days="false" :with-today="true" :length="7" />
        </section>

        @if (count($patrons_adepts) > 0)
        <section id="patrons-adepts" style="grid-column: 1 / span 2">
            <div class="section-header">
                <h1><i class="fa-solid fa-chalkboard-user"></i> Potencjalni patroni</h1>
                <div>
                    <x-a href="https://www.facebook.com/muzykaszytanamiarepl/reviews" target="_blank">Recenzje</x-a>
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
                        <td>
                            <i class="fa-solid fa-{{ is_veteran($patron->id) ? 'user-shield' : 'user' }}"></i>
                            <a href="{{ route('clients', ['search' => $patron->client_id]) }}">{{ _ct_($patron->client_name) }}</a>
                        </td>
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

        <section id="dashboard-requests">
            <div class="section-header">
                <h1><i class="fa-solid fa-envelope"></i> Zapytania</h1>
                <div>
                    <x-a href="{{ route('requests') }}">Wszystkie</x-a>
                    <x-a href="{{ route('add-request') }}" icon="plus">Nowe</x-a>
                </div>
            </div>
            <style>
            #dashboard-requests .table-row{ grid-template-columns: 3fr 2fr; }
            .quest-type{ font-size: 1em; margin: 0; }
            @media screen and (max-width: 600px){
                #dashboard-requests .table-row{
                    grid-template-columns: 1fr;
                }
            }
            </style>
            <div class="quests-table">
                <div class="table-header table-row">
                    <span>Utwór/Klient</span>
                    <span>Meta</span>
                </div>
            @forelse ($requests as $request)
            <a href="{{ route('request', $request->id) }}" class="table-row p-{{ $request->status_id }} {{ is_priority($request->id) ? "priority" : "" }}">
                <span class="quest-main-data flex-down">
                    <h3 class="song-title">{{ $request->title ?? "bez tytułu" }}</h3>
                    @if($request->artist) <span class="song-artist">{{ $request->artist }}</span> @endif
                    @if (is_priority($request->id))
                    <b>Priorytet</b>
                    @endif
                    <br>
                    <span class="ghost">
                        @if ($request->client?->client_name)
                            @if (is_veteran($request->client->id))
                            <i class="fa-solid fa-user-shield" @popper(stały klient)></i> {{ _ct_($request->client->client_name) }}
                            @else
                            <i class="fa-solid fa-user" @popper(zwykły klient)></i> {{ _ct_($request->client->client_name) }}
                            @endif
                        @else
                            <i class="fa-regular fa-user" @popper(nowy klient)></i> {{ _ct_($request->client_name) }}
                        @endif
                    </span>
                </span>
                <span>
                    <span class="quest-status">
                        <x-phase-indicator :status-id="$request->status_id" :small="true" />
                    </span>
                    <div class="quest-meta">
                        @if ($request->price)
                        <p>{{ _c_(as_pln($request->price)) }}</p>
                        <i class="fa-solid fa-sack-dollar" @popper(Cena)></i>
                        @endif

                        @if ($request->hard_deadline)
                        <p
                            @if ($request->hard_deadline?->addDay()->subDays(1)->lte(now()))
                            class="quest-deadline error"
                            @elseif ($request->hard_deadline?->addDay()->subDays(3)->lte(now()))
                            class="quest-deadline warning"
                            @else
                            class="quest-deadline"
                            @endif
                            {{ Popper::pop($request->hard_deadline->format("Y-m-d")) }} >
                            {{ $request->hard_deadline?->addDay()->diffForHumans() }}
                        </p>
                        <i class="fa-solid fa-calendar-xmark" @popper(Termin od klienta)></i>
                        @endif
                        @if ($request->deadline)
                        <p
                            @if(in_array($request->status_id, [11, 12]))
                                @if ($request->deadline?->addDay()->subDays(1)->lte(now()))
                                class="quest-deadline error"
                                @elseif ($request->deadline?->addDay()->subDays(3)->lte(now()))
                                class="quest-deadline warning"
                                @endif
                            @else
                                class="quest-deadline"
                            @endif
                            {{ Popper::pop($request->deadline->format("Y-m-d")) }} >
                            {{ $request->deadline?->addDay()->diffForHumans() }}
                        </p>
                        <i class="fa-solid fa-calendar" @popper(Termin oddania pierwszej wersji)></i>
                        @endif
                    </div>
                </span>
            </a>
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
            <style>
            #dashboard-quests .table-row{ grid-template-columns: 2em 3fr 2fr; }
            .quest-type{ font-size: 1em; margin: 0; }
            @media screen and (max-width: 600px){
                #dashboard-quests .table-row{
                    grid-template-columns: 2em 1fr;
                }
                .table-row span:has(.quest-status){
                    grid-column: 1 / span 2;
                }
            }
            </style>
            <div class="quests-table">
                <div class="table-header table-row">
                    <span><i class="fa-solid fa-signal" @popper(Pozycja w kolejce)></i></span>
                    <span>Utwór/Klient</span>
                    <span>Meta</span>
                </div>
            @forelse ($quests as $key => $quest)
                <a href="{{ route('quest', $quest->id) }}" class="table-row p-{{ $quest->status_id }} {{ is_priority($quest->id) ? "priority" : "" }}">
                    <span>{{ $key + 1 }}</span>
                    <span class="quest-main-data flex-down">
                        <h3 class="song-title">{{ $quest->song->title ?? "bez tytułu" }}</h3>
                        @if($quest->song->artist) <span class="song-artist">{{ $quest->song->artist }}</span> @endif
                        @if (is_priority($quest->id))
                        <b>Priorytet</b>
                        @endif
                        <span class="ghost">
                            @if ($quest->client?->client_name)
                                @if (is_veteran($quest->client->id))
                                <i class="fa-solid fa-user-shield" @popper(stały klient)></i> {{ _ct_($quest->client->client_name) }}
                                @else
                                <i class="fa-solid fa-user" @popper(zwykły klient)></i> {{ _ct_($quest->client->client_name) }}
                                @endif
                            @else
                                <i class="fa-regular fa-user" @popper(nowy klient)></i> {{ _ct_($quest->client_name) }}
                            @endif
                        </span>
                    </span>
                    <span>
                        <span class="quest-status">
                            <x-phase-indicator :status-id="$quest->status_id" :small="true" />
                        </span>
                        <div class="quest-meta">
                            @if ($quest->price)
                            <p class="{{ $quest->paid ? 'success' : ($quest->payments?->sum('comment') > 0 ? 'warning' : '') }}">
                                {{ _c_(as_pln($quest->price)) }}
                            </p>
                            <i class="fa-solid fa-sack-dollar" @popper(Cena)></i>
                            @endif

                            @if ($quest->hard_deadline)
                            <p
                                @if ($quest->hard_deadline?->addDay()->subDays(1)->lte(now()))
                                class="quest-deadline error"
                                @elseif ($quest->hard_deadline?->addDay()->subDays(3)->lte(now()))
                                class="quest-deadline warning"
                                @else
                                class="quest-deadline"
                                @endif
                                {{ Popper::pop($quest->hard_deadline->format("Y-m-d")) }} >
                                {{ $quest->hard_deadline?->addDay()->diffForHumans() }}
                            </p>
                            <i class="fa-solid fa-calendar-xmark" @popper(Termin od klienta)></i>
                            @endif
                            @if ($quest->deadline)
                            <p
                                @if(in_array($quest->status_id, [11, 12]))
                                    @if ($quest->deadline?->addDay()->subDays(1)->lte(now()))
                                    class="quest-deadline error"
                                    @elseif ($quest->deadline?->addDay()->subDays(3)->lte(now()))
                                    class="quest-deadline warning"
                                    @endif
                                @else
                                    class="quest-deadline"
                                @endif
                                {{ Popper::pop($quest->deadline->format("Y-m-d")) }} >
                                {{ $quest->deadline?->addDay()->diffForHumans() }}
                            </p>
                            <i class="fa-solid fa-calendar" @popper(Termin oddania pierwszej wersji)></i>
                            @endif
                        </div>
                    </span>
                </a>
            @empty
                <p class="grayed-out">brak aktywnych zleceń</p>
            @endforelse
            </div>
        </section>
    </div>
@endsection
