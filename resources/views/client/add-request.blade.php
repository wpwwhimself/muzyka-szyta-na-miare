@extends('layouts.app', compact("title"))

@section('content')
    <form method="post" action={{ route("mod-request-back") }}>
        @csrf
        <h1>Dodaj nowe zapytanie</h1>
        <div id="request-box">
            <section class="input-group">
                <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
                <x-select name="quest_type" label="Rodzaj zlecenia" :options="$questTypes" :required="true" />
                <x-input type="text" name="title" label="Tytuł utworu" />
                <x-input type="text" name="artist" label="Oryginalny wykonawca" />
                <x-input type="text" name="cover_artist" label="Coverujący" />
                <x-input type="text" name="link" label="Link do nagrania" :small="true" />
                <x-input type="TEXT" name="wishes" label="Życzenia" value="{{ Auth::user()->client->default_wishes }}" />
                <x-input type="date" name="hard_deadline" label="Opcjonalny termin wykonania" />
            </section>
        </div>
        <input type="hidden" name="modifying" value="0" />
        <button type="submit" class="hover-lift">
            <i class="fa-solid fa-check"></i> Dodaj
        </button>
    </form>
@endsection