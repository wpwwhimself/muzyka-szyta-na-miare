<table class="calendar-table">
    <thead>
        <tr>
            <th>Dzień</th>
            <th>Zlecenia</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($calendar as $date => $meta)
        <tr @class(["cal-row", "interactive" => $clickDays])
            @if ($clickDays) onclick="handleCalendarClick('{{ $meta['date_val'] }}')" @endif
        >
            <td @class([
                $meta['day_type'],
                "suggest" => $meta['suggest_date'] && $suggest,
            ])>
                @if ($meta['suggest_date'] && $suggest)
                <x-shipyard.app.icon name="check-circle" />
                @elseif (preg_match("/free/", $meta['day_type']))
                <x-shipyard.app.icon name="circle-outline" />
                @else
                <x-shipyard.app.icon name="circle" />
                @endif

                {{ $date }}
            </td>

            <td>
            @foreach (["", "_hard"] as $suffix)
                @foreach ($meta["quests$suffix"] as $quest)
                <x-shipyard.app.icon-label-value
                    :icon="$quest->song->has_safe_files ? model_icon('files') : $quest->quest_type->icon"
                    label="Zlecenie"
                    @class([
                        "accent danger" => $suffix == "_hard",
                        "accent error" => $quest->hard_deadline?->addDay()->isPast(),
                    ])
                >
                    <a class="quest" href="{{ route('quest', ['id' => $quest->id]) }}" target="_blank" >
                        {!! $quest->song->title ?? "bez tytułu" !!}
                    </a>
                </x-shipyard.app.icon-label-value>
                @endforeach

                {{--
                @foreach ($meta["quests_done$suffix"] as $quest)
                <x-shipyard.app.icon-label-value
                    icon="check"
                    label="Zakończone zlecenie"
                    @class([
                        "ghost",
                        "accent danger" => $suffix == "_hard",
                    ])
                >
                    <a class="quest" href="{{ route('quest', ['id' => $quest->id]) }}" target="_blank" >
                        {!! $quest->song->title ?? "bez tytułu" !!}
                    </a>
                </x-shipyard.app.icon-label-value>
                @endforeach
                --}}

                @foreach ($meta["requests$suffix"] as $request)
                <x-shipyard.app.icon-label-value
                    :icon="model_icon('requests')"
                    label="Zapytanie"
                    @class([
                        "ghost",
                        "accent danger" => $suffix == "_hard",
                    ])
                >
                    <a class="request" href="{{ route('request', ['id' => $request->id]) }}" target="_blank" >
                        {!! $request->title ?? "bez tytułu" !!}
                    </a>
                </x-shipyard.app.icon-label-value>
                @endforeach
            @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
