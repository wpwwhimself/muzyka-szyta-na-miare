<a href="{{ $quest->link_to }}"
    class="quest-mini p-{{ $quest->status_id }} {{ ($quest->is_priority) ? "priority" : "" }}"
>
    <span class="flex-right keep-for-mobile middle">
        @unless (empty($no))
        <span>{{ $no }}</span>
        @endunless

        <x-quest-type :type="$quest->quest_type" />

        <div class="flex-down">
            <h3 class="song-title">
                {{ $song->title ?? "bez tytułu" }}
                @if (is_archmage() && (
                    $song->has_safe_files || $song->created_at->lte(now()->subMonth())
                ))
                <i class="fas fa-folder" @popper(Sejf istnieje)></i>
                @endif
            </h3>

            @if ($song->artist)
            <span class="song-artist">{{ $song->artist }}</span>
            @endif

            @if ($quest->is_priority)
            <b>Priorytet</b>
            @endif

            @if (is_archmage())
            <span class="ghost">
                @if (gettype($client) == "object")
                    @if ($client->is_veteran)
                    <i class="fa-solid fa-user-shield" @popper(stały klient)></i> {{ _ct_($client->client_name) }}
                    @else
                    <i class="fa-solid fa-user" @popper(zwykły klient)></i> {{ _ct_($client->client_name) }}
                    @endif
                @else
                    <i class="fa-regular fa-user" @popper(nowy klient)></i> {{ _ct_($client) }}
                @endif
            </span>
            @endif
        </div>
    </span>

    <div class="quest-meta grid-2 keep-for-mobile">
        @if ($quest->price)
        <i class="fa-solid fa-sack-dollar" @popper(Cena)></i>
        <p class="{{ $quest->paid ? 'success' : ($quest->payments?->sum('comment') > 0 ? 'warning' : '') }}">
            {{ _c_(as_pln($quest->price)) }}
        </p>
        @endif

        @if ($quest->hard_deadline)
        <i class="fa-solid fa-calendar-xmark" @popper(Termin od klienta)></i>
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
        @endif

        @if ($quest->deadline)
        <i class="fa-solid fa-calendar" @popper(Do kiedy (włącznie) oddam pliki)></i>
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
        @endif
    </div>

    <div class="quest-status">
        <x-phase-indicator :status-id="$quest->status_id" :small="true" />
    </div>
</a>
