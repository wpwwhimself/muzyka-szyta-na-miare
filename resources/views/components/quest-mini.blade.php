@props(['quest'])

<div class="quest-mini hover-light q-container p-{{ $quest->status_id }} {{ is_priority($quest->id) ? "priority" : "" }}">
    <a href="{{ route((strlen($quest->id) > 10) ? "request" : "quest", $quest->id) }}" class="song-title-artist">
        <p class="song-artist"><i class="fa-solid {{ $quest->status->status_symbol }}"></i> {{ $quest->status->status_name }}</p>
        <h2 class="song-title">{{ $quest->song->title ?? $quest->title ?? "bez tytułu" }}</h2>
        <p class="song-artist">{{ $quest->song->artist ?? $quest->artist }}</p>
    </a>
    <div class="quest-details">
        <div class="quest-meta">
            @if (Auth::id() == 1)
                @if ($quest->client_id)
                    @if (is_veteran($quest->client_id))
                    <i class="fa-solid fa-user-shield"></i>
                    @else
                    <i class="fa-solid fa-user"></i>
                    @endif
                <p class="client-name">
                    <a href="{{ route('clients') }}#client{{ $quest->client_id }}">
                    {{ $quest->client->client_name }}
                    </a>
                </p>
                @else
                <i class="fa-regular fa-user"></i>
                <p class="client-name">{{ $quest->client_name }}</p>
                @endif
            @endif

            @if ($quest->price)
            <i class="fa-solid fa-sack-dollar"></i>
            <p class={{ $quest->paid ? "quest-paid" : "" }}>{{ $quest->price }} zł</p>
            @endif

            @if ($quest->deadline)
            <i class="fa-solid fa-calendar"></i>
            <p class="quest-deadline">{{ $quest->deadline }}</p>
            @endif
            @if ($quest->hard_deadline)
            <i class="fa-solid fa-calendar-xmark"></i>
            <p class="quest-deadline">{{ $quest->hard_deadline }}</p>
            @endif
        </div>
        <div class="quest-meta">
            @unless (strlen($quest->id) > 10)
            <p class="quest-id">{{ $quest->id }}</p>
            <i class="fa-solid fa-hashtag"></i>
            @endunless


        </div>
    </div>
</div>
