@extends("layouts.app")

@section('content')
    <h2>Zapytanie zostało pomyślnie przeniesione do fazy <span class="quest-status p-{{ $status }}">{{ DB::table("statuses")->where("id", $status)->value("status_name") }}</span></h2>
    @if ($status == 9)
        <p>Wkrótce będę się kontaktował ponownie w sprawie postępów w pracach.</p>
        {{-- <p>Utworzyłem dla Ciebie konto, gdzie będą umieszczone wszystkie informacje dotyczące nie tylko tego zlecenia, ale także całej współpracy ze mną. Szczegóły znajdziesz w wiadomości, którą wkrótce powinieneś otrzymać.</p> --}}
    @endif
@endsection