@extends("layouts.app")

@section('content')
    <h2>Zapytanie zostało pomyślnie przeniesione do fazy <span class="quest-status p-{{ $status }}">{{ DB::table("statuses")->where("id", $status)->value("status_name") }}</span></h2>
    <p class="grayed-out">ID zapytania: {{ $id }}</p>
    
    @if ($status == 9)
        <p>Wkrótce będę się kontaktował ponownie w sprawie postępów w pracach.</p>
        @if ($is_new_client)    
        <p>Utworzyłem dla Ciebie konto, gdzie będą umieszczone wszystkie informacje dotyczące nie tylko tego zlecenia, ale także całej współpracy ze mną. Szczegóły znajdziesz w wiadomości, którą wkrótce powinieneś otrzymać.</p>
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
            <x-button action="submit" label="Prześlij" icon="fa-paper-plane" />
        </form>
    @endif
@endsection