<table class="calendar-table">
    <thead>
        <tr>
            <th>Dzień</th>
            <th @popper(Obłożenie dnia) role="capacity" class="hidden">OD</th>
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
                {{ $meta["day_symbol"] }} {{ $date }}
            </td>

            <td role="capacity" class="hidden">
                @if (!Str::contains($meta["day_type"], "free"))
                <span class="flex down no-gap">
                    @for ($i = 0; $i < $meta["capacity"]; $i++)
                        @if (count($meta["quests"]) > $i)
                        <x-shipyard::app.icon name="square-rounded" />
                        @else
                        <x-shipyard::app.icon name="square-rounded-outline" />
                        @endif
                    @endfor
                </span>
                @endif
            </td>

            <td>
            @foreach (["", "_hard"] as $suffix)
                @foreach ($meta["quests$suffix"] as $quest)
                <div data-client="{{ $quest->user }}" @class([
                    "quest-row animatable",
                    "flex right middle",
                    "accent danger ghost" => $suffix == "_hard",
                    "accent error" => $quest->hard_deadline?->addDay()->isPast(),
                ])>
                    <a href="{{ route('quest', ['id' => $quest->id]) }}">
                        {!! $quest->display_subtitle !!}
                        {!! $quest->song->title ?? "bez tytułu" !!}
                    </a>
                </div>
                @endforeach

                @foreach ($meta["requests$suffix"] as $request)
                <div data-client="{{ $request->user ?? $request->client_name }}" @class([
                    "quest-row animatable",
                    "flex right middle",
                    "ghost",
                    "accent danger" => $suffix == "_hard",
                ])>
                    <a href="{{ route('request', ['id' => $request->id]) }}">
                        {!! $request->display_subtitle !!}
                        {!! $request->title ?? "bez tytułu" !!}
                    </a>
                </div>
                @endforeach
            @endforeach
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div role="switches" class="flex right center middle">
    <x-shipyard::ui.button
        icon="eye"
        label="OD"
        pop="Wyświetl obłożenie dnia"
        action="none"
        onclick="cToggleCapacity()"
        class="toggle"
    />
    <x-shipyard::ui.button
        icon="eye"
        label="Klienci"
        pop="Wyświetl przypisanie do klientów"
        action="none"
        onclick="cToggleOwnership()"
        class="toggle"
    />
</div>

<div role="ownership-highlighters" class="flex right center middle hidden">
    @foreach ($clients as $client)
    <span
        class="interactive"
        onmouseover="cHighlightClient('{{ $client }}')"
        onmouseout="cHighlightClient(null)"
    >
        {{ $client }}
    </span>
    @endforeach
</div>

<script>
function cToggleCapacity() {
    document.querySelectorAll(`.calendar-table [role="capacity"]`).forEach(el => el.classList.toggle("hidden"));
    document.querySelector(`[role="switches"] [data-label="OD"]`).classList.toggle("active");
}

function cToggleOwnership() {
    document.querySelector(`[role="ownership-highlighters"]`).classList.toggle("hidden");
    document.querySelector(`[role="switches"] [data-label="Klienci"]`).classList.toggle("active");
}

function cHighlightClient(client) {
    document.querySelector(`.calendar-table`).classList.toggle("highlight-mode", client !== null);
    document.querySelectorAll(`.calendar-table [data-client]`).forEach(el => el.classList.toggle("highlighted", el.dataset.client === client));
}
</script>
