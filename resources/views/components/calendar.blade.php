<table class="calendar-table">
    <thead>
        <tr>
            <th>Dzień</th>
            <th>ReQuesty</th>
        </tr>
    </thead>
    <tbody>
    @foreach ($calendar as $date => $meta)
        <tr @class(["cal-row", "interactive" => $clickDays])
            @if ($clickDays) onclick="updateDeadline('{{ $meta['date_val'] }}')" @endif
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
                @foreach ($meta["quests"] as $quest)
                <a class="quest" href="{{ route('quest', ['id' => $quest->id]) }}" target="_blank" >
                    {!! $quest !!}
                </a>
                @endforeach

                @foreach ($meta["quests_done"] as $quest)
                <a class="quest ghost" href="{{ route('quest', ['id' => $quest->id]) }}" target="_blank" >
                    <x-shipyard.app.icon name="check-circle" />
                    {!! $quest !!}
                </a>
                @endforeach

                @foreach ($meta["requests"] as $request)
                <a class="request ghost" href="{{ route('request', ['id' => $request->id]) }}" target="_blank" >
                    <x-shipyard.app.icon name="chat-question" />
                    {!! $request !!}
                </a>
                @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<script>
function updateDeadline(date) {
    document.querySelector("#deadline").value = date;
}
</script>
