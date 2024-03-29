@props(['quest', "queue" => null])
<div class="quest-mini hover-light q-container p-{{ $quest->status_id }} {{ ($quest->is_priority) ? "priority" : "" }}">
    <a href="{{ route((strlen($quest->id) > 10) ? "request" : "quest", $quest->id) }}" class="song-title-artist">
        <p class="song-artist"><i class="fa-solid {{ $quest->status->status_symbol }}"></i> {{ $quest->status->status_name }}</p>
        <h2 class="song-title">{{ $quest->song->title ?? $quest->title ?? "bez tytułu" }}</h2>
        <p class="song-artist">{{ $quest->song->artist ?? $quest->artist }}</p>
    </a>
    @if (is_archmage())
    <div class="quest-client">
        @if ($quest->client_id)
            @if ($quest->client->is_veteran)
            <i class="fa-solid fa-user-shield" @popper(Klient)></i>
            @else
            <i class="fa-solid fa-user" @popper(Klient)></i>
            @endif
        <p class="client-name">
            <a href="{{ route('clients', ['search' => $quest->client->client_name]) }}">
            {{ $quest->client->client_name }}
            </a>
        </p>
        @else
        <i class="fa-regular fa-user" @popper(Klient)></i>
        <p class="client-name">{{ $quest->client_name }}</p>
        @endif
    </div>
    @endif
    <div class="quest-details">
        <div class="quest-meta">
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
        <div class="quest-meta">
            @if ($queue !== null)
            <p class="">{{ $queue }}</p>
            <i class="fa-solid fa-signal" @popper(Pozycja zlecenia w kolejce)></i>
            @endif
            @unless (strlen($quest->id) > 10)
            <p class="quest-id">
                <x-quest-type
                    :id="$quest->song->type->id ?? 0"
                    :label="$quest->song->type->type ?? 'nie zdefiniowano'"
                    :fa-symbol="$quest->song->type->fa_symbol ?? 'fa-circle-question'"
                    :small="true"
                    />
                {{ $quest->id }}
            </p>
            <i class="fa-solid fa-hashtag" @popper(Identyfikator zlecenia)></i>
            @endunless
        </div>
    </div>
</div>
