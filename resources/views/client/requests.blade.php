@extends('layouts.app', compact("title"))

@section('content')
<div class="tutorial">
    <p>
        <i class="fa-solid fa-circle-question"></i>
        To jest lista złożonych przez Ciebie zapytań.
        Każde zlecenie zaczyna się od złożenia zapytania, w którym trzeba zawrzeć, co będzie przedmiotem prac (podkład muzyczny, nuty itp.).
        Następnie ja przygotowuję wycenę, tj. wyznaczam cenę zlecenia oraz termin jego wykonania.
        Zaakceptowana wycena automatycznie sprawia, że zapytanie staje się nowym zleceniem.
    </p>
    <p>
        Zapytania mają na celu zebrać potrzebne informacje przed rozpoczęciem prac nad faktycznym zleceniem.
        Na liście poniżej znajdziesz nie tylko aktualne zapytania, ale też wcześniejsze.
    </p>
</div>
<section id="requests-list">
    <div class="section-header">
        <h1><i class="fa-solid fa-envelope-open-text"></i> Lista zapytań</h1>
        <div>
            <x-a href="{{ route('add-request') }}" icon="plus">Dodaj nowe</x-a>
        </div>
    </div>
    <style>
    .table-row{ grid-template-columns: 4fr 11em; }
    </style>
    <div class="quests-table">
        <div class="table-header table-row">
            <span>Piosenka</span>
            <span><i class="fa-solid fa-traffic-light"></i> Status</span>
        </div>
        <hr />
        @forelse ($requests as $request)
        <a href="{{ route("request", $request->id) }}" class="table-row p-{{ $request->status_id }}">
            <span class="quest-main-data">
                <x-quest-type
                    :id="$request->quest_type_id"
                    :label="$request->quest_type->type"
                    :fa-symbol="$request->quest_type->fa_symbol"
                    />
                <span>
                    <h3 class="song-title">{{ $request->title ?? "bez tytułu" }}</h3>
                    <span class="song-artist">{{ $request->artist }}</span>
                </span>
            </span>
            <span class="quest-status">
                <x-phase-indicator :status-id="$request->status_id" :small="true" />
            </span>
        </a>
        @empty
        <p class="grayed-out">brak zapytań</p>
        @endforelse
        </tbody>
    </div>
    {{ $requests->links() }}
</section>

@endsection
