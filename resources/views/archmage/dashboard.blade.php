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
                                {{ $i->is_request ? $i->re_quest->title : $i->re_quest->song->title }}
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
                        <td>{{ ($change->is_request) ? $change->re_quest->client?->client_name ?? $change->re_quest->client_name : $change->re_quest->client->client_name }}</td>
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
            </div>
            <x-calendar :click-days="false" :with-today="true" :length="7" />
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
        @if (count($patrons_adepts) > 0)
        <section id="patrons-adepts">
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
