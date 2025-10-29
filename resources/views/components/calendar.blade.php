<table class="calendar-table">
    <thead>
        <tr>
            <th>Dzień</th>
            <th>Mój termin</th>
            <th>Termin klienta</th>
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

            @foreach (["", "_hard"] as $suffix)
            <td>
                @foreach ($meta["quests$suffix"] as $quest)
                <x-shipyard.app.icon-label-value
                    :icon="$quest->song->has_safe_files ? model_icon('files') : $quest->quest_type->icon"
                    label="Zlecenie"
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
                    class="ghost"
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
                    class="ghost"
                >
                    <a class="request" href="{{ route('request', ['id' => $request->id]) }}" target="_blank" >
                        {!! $request->title ?? "bez tytułu" !!}
                    </a>
                </x-shipyard.app.icon-label-value>
                @endforeach
            </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
