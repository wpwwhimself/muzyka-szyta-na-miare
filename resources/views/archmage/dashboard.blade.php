@extends('layouts.app', compact("title"))

@section('content')
    <div class="grid-2">
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
                            <i class="fa-solid fa-{{ $patron->is_veteran ? 'user-shield' : 'user' }}"></i>
                            <a href="{{ route('clients', ['search' => $patron->id]) }}">{{ _ct_($patron->client_name) }}</a>
                        </td>
                        <td>
                            <x-button label="" icon="check" action="{{ route('patron-mode', ['client_id' => $patron->id, 'level' => 2]) }}" :small="true" />
                            <x-button label="" icon="x" action="{{ route('patron-mode', ['client_id' => $patron->id, 'level' => 0]) }}" :small="true" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @endif

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
                                <a href="{{ route('clients', ['search' => $change->re_quest->client?->id]) }}">{{ _ct_($change->re_quest->client->client_name) }}</a>
                            @else
                                {{ _ct_($change->re_quest->client_name) }}
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

            <div class="section-header">
                <h1><i class="fa-solid fa-broom"></i> Raport Sprzątacza</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ReQuest</th>
                        <th>Klient</th>
                        <th>Status</th>
                        <th>Komentarz</th>
                        <th>Mail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($janitor_log as $i)
                    <tr>
                        <td>
                            <a href="{{ route($i->is_request ? 'request' : 'quest', ["id" => $i->re_quest->id]) }}" @if ($i->is_request)
                                class="ghost"
                            @endif>
                                {{ ($i->is_request ? $i->re_quest->title : $i->re_quest->song->title) ?? "bez tytułu" }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('clients', ['search' => $i->re_quest->client_id]) }}">
                                {{ $i->is_request ? $i->re_quest->client_name : $i->re_quest->client->client_name }}
                            </a>
                        </td>
                        <td>
                            <x-phase-indicator-mini :status="$i->re_quest->status" />
                        </td>
                        <td>
                            {{ $i->comment }}
                        </td>
                        <td>
                            @switch($i->mailing)
                                @case(2)
                                    <i class="fa-solid fa-square-check success" @popper(mail wysłany)></i>
                                    @break
                                @case(1)
                                    <i class="fa-solid fa-triangle-exclamation warning" @popper(mail wysłany, ale wyślij wiadomość)></i>
                                    @break
                                @default
                                    <i class="fa-solid fa-xmark error" @popper(wyślij wiadomość)></i>
                            @endswitch
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan=5>
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

        <section id="dashboard-requests" class="sc-line">
            <x-sc-scissors />
            <div class="section-header">
                <h1>
                    <i class="fa-solid fa-calendar"></i>
                    Grafik
                </h1>
                <div>
                    <x-a href="{{ route('quests-calendar') }}">Wszystkie</x-a>
                </div>
            </div>
            <x-calendar :click-days="false" :suggest="false" :with-today="true" :length="7" />

            <div class="section-header">
                <h1><i class="fa-solid fa-envelope"></i> Zapytania</h1>
                <div>
                    <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe</x-a>
                    <x-a href="{{ route('requests') }}">Wszystkie</x-a>
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
            <a href="{{ route('request', $request->id) }}" class="table-row p-{{ $request->status_id }} {{ ($request->is_priority) ? "priority" : "" }}">
                <span class="quest-main-data flex-down">
                    <h3 class="song-title">{{ $request->title ?? "bez tytułu" }}</h3>
                    @if($request->artist) <span class="song-artist">{{ $request->artist }}</span> @endif
                    @if ($request->is_priority)
                    <b>Priorytet</b>
                    @endif
                    <br>
                    <span class="ghost">
                        @if ($request->client?->client_name)
                            @if ($request->client->is_veteran)
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

        @php $statuses_for_split = [11, 12, 13, 16, 26, 96]; @endphp
        @foreach([
            ["Zlecenia w toku", "box", $quests->filter(fn($q) => in_array($q->status_id, $statuses_for_split))],
            ["Zlecenia czekające", "box-open", $quests->filter(fn($q) => !in_array($q->status_id, $statuses_for_split))],
        ] as [$sec_title, $icon, $data])
        <section id="dashboard-quests">
            <div class="section-header">
                <h1><i class="fa-solid fa-{{ $icon }}"></i> {{ $sec_title }}</h1>
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
            @forelse ($data as $key => $quest)
                <a href="{{ route('quest', $quest->id) }}" class="table-row p-{{ $quest->status_id }} {{ ($quest->is_priority) ? "priority" : "" }}">
                    <span>{{ $key + 1 }}</span>
                    <span class="quest-main-data flex-down">
                        <h3 class="song-title">{{ $quest->song->title ?? "bez tytułu" }}</h3>
                        @if($quest->song->artist) <span class="song-artist">{{ $quest->song->artist }}</span> @endif
                        @if ($quest->is_priority)
                        <b>Priorytet</b>
                        @endif
                        <span class="ghost">
                            @if ($quest->client?->client_name)
                                @if ($quest->client->is_veteran)
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
        @endforeach
    </div>
@endsection
