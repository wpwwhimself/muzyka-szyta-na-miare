@extends('layouts.app', compact("title"))

@section('content')

@if (count($patrons_adepts) > 0)
<x-section id="patrons-adepts"
    title="Potencjalni patroni"
    icon="chalkboard-user"
    style="grid-column: 1 / span 2;"
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
                    <a href="{{ route('clients', ['search' => $patron->id]) }}">{!! $patron !!}</a>
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

<x-section title="Zapytania" icon="envelope">
    <x-slot name="buttons">
        <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe</x-a>
        <x-a href="{{ route('requests') }}">Wszystkie</x-a>
    </x-slot>

    @forelse ($requests as $request)
    <x-quest-mini :quest="$request" />
    @empty
    <p class="grayed-out"><i class="fas fa-check"></i> brak aktywnych zapytań</p>
    @endforelse
</x-section>

@if (count($showcases_missing))
<x-section title="Showcase'y do stworzenia" icon="bullhorn" style="grid-column: 1 / span 2;">
    <table>
        <thead>
            <tr>
                <th><i class="fas fa-box"></i> ID</th>
                <th><i class="fas fa-compact-disc"></i> ID</th>
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
                        <i class="fas fa-photo-film" @popper(Rolka z teledyskiem)></i>
                        @else
                        <i class="fas fa-film" @popper(Rolka)></i>
                        @endif
                    @endif

                    @if (!$quest->song->has_showcase_file)
                    <i class="fas fa-shirt" @popper(Krótki showcase)></i>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-section>
@endif

<x-section id="dashboard-quests"
    title="Zlecenia w toku"
    icon="box"
>
    @forelse ($quests_ongoing as $key => $quest)
    <x-quest-mini :quest="$quest" :no="$key + 1" />
    @empty
    <p class="grayed-out"><i class="fas fa-check"></i> brak aktywnych zleceń</p>
    @endforelse
</x-section>

<div class="grid-2">
    <x-section id="dashboard-requests" class="sc-line"
        title="Grafik"
        icon="calendar"
    >
        <x-sc-scissors />

        <x-slot name="buttons">
            <x-a href="{{ route('quests-calendar') }}">Wszystkie</x-a>
        </x-slot>

        <x-calendar :click-days="false" :suggest="false" :with-today="true" />
    </x-section>

    <x-section id="dashboard-quests"
        title="Zlecenia czekające"
        icon="box-open"
    >
        @forelse ($quests_review as $key => $quest)
        <x-quest-mini :quest="$quest" :no="$key + 1" />
        @empty
        <p class="grayed-out">brak aktywnych zleceń</p>
        @endforelse
    </x-section>

    <x-section id="recent"
        title="Ostatnie zmiany"
        icon="clock-rotate-left"
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
                @if ($change->re_quest?->status_id == $change->new_status_id)
                <tr>
                @else
                <tr class="ghost">
                @endif
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
                        @if ($change->re_quest?->client)
                            <a href="{{ route('clients', ['search' => $change->re_quest?->client?->id]) }}">{{ _ct_($change->re_quest?->client->client_name) }}</a>
                        @else
                            {{ _ct_($change->re_quest?->client_name) }}
                        @endif
                    @else
                        <a href="{{ route('clients', ['search' => $change->re_quest?->client->id]) }}">{{ _ct_($change->re_quest?->client->client_name) }}</a>
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
    </x-section>
</div>

@endsection
