@extends('layouts.app')
@section("title", "Szpica Arcymaga")

@section('content')

@if (count($patrons_adepts) > 0)
<x-section id="patrons-adepts"
    title="Potencjalni patroni"
    icon="seal"
>
    <x-slot name="buttons">
        <x-a href="https://www.facebook.com/muzykaszytanamiarepl/reviews" target="_blank">Recenzje</x-a>
    </x-slot>

    <table>
        <thead>
            <th>Klient</th>
            <th>Decyzja</th>
        </thead>
        <tbody>
            @foreach ($patrons_adepts as $patron)
            <tr>
                <td>
                    <a href="{{ route('client-view', ['id' => $patron->id]) }}">{!! $patron !!}</a>
                </td>
                <td>
                    <x-button label="" icon="check" action="{{ route('patron-mode', ['client_id' => $patron->id, 'level' => 2]) }}" :small="true" />
                    <x-button label="" icon="x" action="{{ route('patron-mode', ['client_id' => $patron->id, 'level' => 0]) }}" :small="true" />
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-section>
@endif

<x-extendo-block key="requests"
    title="Zapytania"
    :header-icon="model_icon('requests')"
    :extended="$requests->filter(fn ($r) => in_array($r->status_id, [1, 6, 96]))->count() > 0"
>
    <x-slot name="buttons">
        <x-shipyard.app.icon-label-value
            icon="counter"
            label="Liczba"
        >
            {{ $requests->count() }}
        </x-shipyard.app.icon-label-value>

        <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe</x-a>
        <x-a href="{{ route('requests') }}">Wszystkie</x-a>
    </x-slot>

    <div class="flex down">
        @forelse ($requests as $request)
        <x-requests.tile :request="$request" />
        @empty
        <p class="grayed-out"><i class="fas fa-check"></i> brak aktywnych zapytań</p>
        @endforelse
    </div>
</x-extendo-block>

@if (count($showcases_missing))
<x-section title="Showcase'y do stworzenia" :icon="model_icon('showcases')">
    <table>
        <thead>
            <tr>
                <th>ID questa</th>
                <th>ID utworu</th>
                <th>Utwór</th>
                <th>Co trzeba zrobić</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($showcases_missing as $quest)
            <tr>
                <td><a href="{{ route('quest', ['id' => $quest->id]) }}">{{ $quest->id }}</a></td>
                <td><a href="{{ route('song-edit', ['id' => $quest->song->id]) }}">{{ $quest->song->id }}</a></td>
                <td>{{ $quest->song->full_title }}</td>
                <td>
                    @if ($quest->song->has_recorded_reel)
                        @if ($quest->song->has_original_mv)
                        <span @popper(Rolka z teledyskiem)><x-shipyard.app.icon name="video-vintage" /></span>
                        @else
                        <span @popper(Rolka)><x-shipyard.app.icon name="movie-roll" /></span>
                        @endif
                    @endif

                    @if (!$quest->song->has_showcase_file)
                    <span @popper(Krótki showcase)><x-shipyard.app.icon name="tshirt-crew" /></span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-section>
@endif

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-section id="dashboard-quests"
        title="Zlecenia w toku"
        :icon="model_icon('quests')"
        :extended="true"
    >
        <x-slot:buttons>
            <x-shipyard.app.icon-label-value
                icon="counter"
                label="Liczba"
            >
                {{ $quests_ongoing->count() }}
            </x-shipyard.app.icon-label-value>
        </x-slot:buttons>

        <div class="flex down">
            @forelse ($quests_ongoing as $key => $quest)
            <x-quests.tile :quest="$quest" :no="$key + 1" />
            @empty
            <p class="grayed-out"><i class="fas fa-check"></i> brak aktywnych zleceń</p>
            @endforelse
        </div>
    </x-section>

    <x-section id="dashboard-requests" class="sc-line"
        title="Grafik"
        icon="calendar"
        :extended="true"
        scissors
    >
        <x-slot name="buttons">
            <x-a href="{{ route('quests-calendar') }}">Wszystkie</x-a>
        </x-slot>

        <x-calendar :click-days="false" :suggest="false" :with-today="true" />
    </x-section>

    <x-section id="dashboard-quests"
        title="Zlecenia czekające"
        icon="package-variant"
        :extended="false"
    >
        <x-slot:buttons>
            <x-shipyard.app.icon-label-value
                icon="counter"
                label="Liczba"
            >
                {{ $quests_review->count() }}
            </x-shipyard.app.icon-label-value>
        </x-slot:buttons>

        <div class="flex down">
            @forelse ($quests_review as $key => $quest)
            <x-quests.tile :quest="$quest" :no="$key + 1" />
            @empty
            <p class="grayed-out">brak aktywnych zleceń</p>
            @endforelse
        </div>
    </x-section>

    <x-section id="recent"
        title="Ostatnie zmiany"
        icon="history"
        :extended="false"
    >
        <table>
            <thead>
                <tr>
                    <th>Zlecenie/Utwór</th>
                    <th>Klient</th>
                    <th>Status</th>
                    <th>Kiedy</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recent as $change)
                <tr @class([
                    "ghost" => $change->re_quest?->status_id == $change->new_status_id,
                ])>
                    <td>
                        <a href="{{ route(($change->is_request) ? 'request' : 'quest', ['id' => $change->re_quest_id]) }}">
                            {{ (($change->is_request) ? $change->re_quest?->title : $change->re_quest?->song->title) ?? "utwór bez tytułu" }}
                        </a>
                        @unless ($change->is_request)
                        <small class="ghost">{{ $change->re_quest?->song->id }}</small>
                        @endunless
                    </td>
                    <td>
                    @if ($change->is_request)
                        @if ($change->re_quest?->user)
                            <a href="{{ route('client-view', ['id' => $change->re_quest?->user?->id]) }}">{{ _ct_($change->re_quest?->user->notes->client_name) }}</a>
                        @else
                            {{ _ct_($change->re_quest?->client_name) }}
                        @endif
                    @else
                        <a href="{{ route('client-view', ['id' => $change->re_quest?->user->id]) }}">{{ _ct_($change->re_quest?->user->notes->client_name) }}</a>
                    @endif
                    </td>
                    <td>
                        <x-phase-indicator-mini :status="$change->new_status" />

                        @if ($change->comment)
                        <span {{ Popper::pop($change->comment) }}>
                            <x-shipyard.app.icon name="comment" />
                        </span>
                        @endif
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
    </x-section>

    <x-section title="Raport sprzątacza" icon="broom">
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
                                <span class="accent success" @popper(mail wysłany)>
                                    <x-shipyard.app.icon name="email-fast" />
                                </span>
                                @break
                            @case(1)
                                <span class="accent danger" @popper(mail wysłany, ale wyślij wiadomość)>
                                    <x-shipyard.app.icon name="email-fast" />
                                </span>
                                @break
                            @case(0)
                                <span class="accent error" @popper(wyślij wiadomość)>
                                    <x-shipyard.app.icon name="email-off" />
                                </span>
                                @break
                        @endswitch
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan=5>
                        <span class="grayed-out">
                            <x-shipyard.app.icon name="bed" />
                            Sprzątacz dzisiaj śpi
                        </span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-section>
</div>

@endsection
