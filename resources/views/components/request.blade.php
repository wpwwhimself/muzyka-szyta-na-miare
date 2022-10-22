@props(['request'])

<a href="{{ route("request", $request->id) }}" class="quest-mini hover-lift q-container p-{{ $request->status_id }}">
    <div>
        <p class="song-artist">{{ $request->artist }}</p>
        <h2 class="song-title">{{ $request->title }}</h2>
        <p class="quest-status">Status: <strong>{{ $request->status_name }}</strong></p>
    </div>
    <div class="quest-meta">
        <i title="klient">👤</i><p class="client-name">{{ $request->client_name }}</p>
        <i title="wycena">💰</i><p class="quest-paid">{{ $request->paid }} / {{ price_calc($request->price) }}</p>
        <i title="planowany termin ukończenia">📅</i><p class="quest-deadline">{{ $request->deadline ?? "—" }}</p>
    </div>
</a>
