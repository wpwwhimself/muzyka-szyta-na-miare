@props(['quest'])

<a href="{{ route("quest", $quest->id) }}" class="quest-mini hover-lift q-container p-{{ $quest->status_id }}">
    <div>
        <p class="song-artist">{{ $quest->artist }}</p>
        <h2 class="song-title">{{ $quest->title }}</h2>
        <p class="quest-status">Status: <strong>{{ $quest->status_name }}</strong></p>
    </div>
    <div class="quest-meta">
        <i title="klient">👤</i><p class="client-name">{{ $quest->surname == null ? $quest->client_name : $quest->client_name . " " . $quest->surname }}</p>
        <i title="wycena">💰</i><p class="quest-paid">{{ $quest->paid }} / {{ price_calc($quest->price) }}</p>
        <i title="planowany termin ukończenia">📅</i><p class="quest-deadline">{{ $quest->deadline ?? "—" }}</p>
    </div>
</a>
