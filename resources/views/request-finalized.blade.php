@extends("layouts.app")

@section('content')
    <h2>
        Zapytanie zostało pomyślnie przeniesione do nowej fazy.
    </h2>
    <x-phase-indicator :status-id="$status" />
    
    @if ($status == 9)
        <p>Wkrótce będę się kontaktował ponownie w sprawie postępów w pracach.</p>
        @if ($is_new_client)    
        <p>Utworzyłem dla Ciebie konto, gdzie będą umieszczone wszystkie informacje dotyczące nie tylko tego zlecenia, ale także całej współpracy ze mną. Hasło do tego konta to</p>
        <h3 style="text-align: center;">{{ DB::table("requests")->join("users", "requests.client_id", "=", "users.id")->where("requests.id", $id)->value("users.password"); }}</h3>
        <p>Szczegóły znajdziesz w wiadomości, którą wkrótce powinieneś otrzymać.</p>
        @endif
    @endif

    @if (in_array($status, [7, 8]))
        <p>
            @if ($status == 8)
            Przykro mi, że nie spodobało Ci się to, co dla Ciebie przygotowałem.
            @endif
        Zostaw, proszę, kilka słów komentarza, dlaczego odrzucasz projekt.
        </p>

        <form method="POST" action="{{ route("quest-reject") }}">
            @csrf
            <x-input type="TEXT" name="comment" label="" />
            <input type="hidden" name="id" value="{{ $id }}" />
            <input type="hidden" name="status" value="{{ $status }}" />
            <x-button action="submit" label="Prześlij" icon="paper-plane" />
        </form>
    @endif
@endsection