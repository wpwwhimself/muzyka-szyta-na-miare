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
                    @if ($change->re_quest->status_id == $change->new_status_id)
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
                        <th>Obiekt</th>
                        <th>Komentarz</th>
                        <th>Mail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($janitor_log as $i)
                    <tr>
                        <td>
                            @if(is_object($i->subject))
                            <a href="{{ $i->subject->link_to }}">
                                @if($i->procedure === "re_quests")
                                    <x-phase-indicator-mini :status="$i->subject->status" />
                                    {{ $i->subject->song?->title ?? $i->subject->title ?? "utwór bez tytułu" }}
                                @elseif($i->procedure === "safe")
                                    <i class="fas fa-folder" @popper(Sejf)></i>
                                    {{ $i->subject->title ?? "utwór bez tytułu" }}
                                @endif
                            </a>
                            @else
                            <span>{{ $i->subject }}</span>
                            @endif
                        </td>
                        <td>
                            @if(is_array($i->comment))
                            {{ $i->comment["comment"] }}
                            <x-phase-indicator-mini :status="\App\Models\Status::find($i->comment['status_id'])" />
                            @else
                            {{ $i->comment }}
                            @endif
                        </td>
                        <td>
                            @switch($i->mailing)
                                @case(2)
                                    <i class="fa-solid fa-square-check success" @popper(mail wysłany)></i>
                                    @break
                                @case(1)
                                    <i class="fa-solid fa-triangle-exclamation warning" @popper(mail wysłany, ale wyślij wiadomość)></i>
                                    @break
                                @case(0)
                                    <i class="fa-solid fa-xmark error" @popper(wyślij wiadomość)></i>
                                    @break
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

            @if (count($showcases_missing))
            <div class="section-header">
                <h1><i class="fa-solid fa-bullhorn"></i> Showcase'y do stworzenia</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-box"></i> ID</th>
                        <th><i class="fas fa-compact-disc"></i> ID</th>
                        <th>Utwór</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($showcases_missing as $quest)
                    <tr>
                        <td><a href="{{ route('quest', ['id' => $quest->id]) }}">{{ $quest->id }}</a></td>
                        <td>{{ $quest->song->id }}</td>
                        <td>{{ $quest->song->full_title }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
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
            <x-calendar :click-days="false" :suggest="false" :with-today="true" />

            <div class="section-header">
                <h1>
                    <i class="fa-solid fa-envelope"></i>
                    Zapytania
                </h1>
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
                        <i class="fa-solid fa-calendar" @popper(Do kiedy (włącznie) oddam pliki)></i>
                        @endif
                    </div>
                </span>
            </a>
            @empty
                <p class="grayed-out">brak aktywnych zapytań</p>
            @endforelse
            </div>
        </section>

        @foreach([
            ["Zlecenia w toku", "box", $quests_ongoing],
            ["Zlecenia czekające", "box-open", $quests_review],
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
                        <h3 class="song-title">
                            <x-quest-type
                                :id="$quest->song->type->id ?? 0"
                                :label="$quest->song->type->type ?? 'nie zdefiniowano'"
                                :fa-symbol="$quest->song->type->fa_symbol ?? 'fa-circle-question'"
                                />
                            {{ $quest->song->title ?? "bez tytułu" }}
                            @if ($quest->song->has_safe_files)
                            <i class="fas fa-folder" @popper(Sejf istnieje)></i>
                            @endif
                        </h3>
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
                            <i class="fa-solid fa-calendar" @popper(Do kiedy (włącznie) oddam pliki)></i>
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
