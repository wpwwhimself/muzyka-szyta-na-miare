@props(['request'])

<a href="{{ route("request", $request->id) }}" class="quest-mini hover-lift q-container p-{{ $request->status_id }}">
    <div>
        <p class="song-artist">{{ $request->artist }}</p>
        <h2 class="song-title">{{ $request->title }}</h2>
    </div>
    <div class="quest-details">
        <div class="quest-meta">
            <i class="fa-solid fa-hashtag"></i>
            <p class="quest-id">{{ $request->id }}</p>

            <i class="fa-solid fa-traffic-light"></i>
            <p class="quest-status">{{ $request->status->status_name }}</p>
        </div>
        <div class="quest-meta">
            @if (Auth::id() == 1)
                @if ($request->client?->client_name)
                <i class="fa-solid fa-user"></i>
                <p class="client-name">{{ $request->client->client_name }}</p>
                @else
                <i class="fa-regular fa-user"></i>
                <p class="client-name">{{ $request->client_name }}</p>
                @endif
            @endif

            @if ($request->price)
            <i class="fa-solid fa-sack-dollar"></i>
            {{-- <p class="quest-paid">{{ price_calc($request->price)[0] }} zł</p> --}}
            @endif

            @if ($request->deadline)
                @if ($request->hard_deadline)
                <i class="fa-solid fa-calendar-xmark"></i>
                @else
                <i class="fa-solid fa-calendar"></i>
                @endif
            <p class="quest-deadline">{{ $request->deadline ?? "—" }}</p>
            @endif
        </div>
    </div>
</a>
