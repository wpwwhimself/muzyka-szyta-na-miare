@extends("layouts.app")

@section('content')
    <h2>
        Zapytanie zostało pomyślnie przeniesione do nowej fazy.
    </h2>
    <x-phase-indicator :status-id="$status" />

    @if ($status == 9)
        <p>Wkrótce będę się kontaktował ponownie w sprawie postępów w pracach.</p>
        @if ($is_new_client)
        <p>
            Utworzyłem dla Ciebie konto, gdzie będą umieszczone wszystkie informacje dotyczące nie tylko tego zlecenia, ale także całej współpracy ze mną.
            Aby się zalogować, kliknij poniższy przycisk. Do logowania potrzebne jest jedynie następujące hasło:
        </p>
        <h3 style="text-align: center;">{{ DB::table("requests")->join("users", "requests.client_id", "=", "users.id")->where("requests.id", $id)->value("users.password"); }}</h3>
        <p><b>Zachowaj je, bo będzie przydatne!</b> Jeśli się zgubi, zawsze można poprosić mnie o jego ponowne wysłanie.</p>
        <div class="flex-right">
            <x-button
                action="{{ route('login') }}" label="Zaloguj się" icon="user-circle"
                />
        </div>
        @endif
    @endif
@endsection
