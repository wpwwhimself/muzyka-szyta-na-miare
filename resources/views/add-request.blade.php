@extends('layouts.app', compact("title"))

@section('content')
    <form method="post" action={{ route("add-request-back") }}>
        <h1>Dodaj nowe zapytanie</h1>
        <div class="grid-3">
            <section class="input-group">
                <h2><i class="fa-solid fa-user"></i> Dane klienta</h2>
                <x-input type="text" name="client_name" label="Imię/Nazwa" :autofocus="true" :required="true" />
                <x-input type="text" name="surname" label="Nazwisko" />
                <x-input type="email" name="email" label="Adres e-mail" />
                <x-input type="tel" name="phone" label="Numer telefonu" />
                <x-input type="text" name="other_medium" label="Inna forma kontaktu" />
                <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" placeholder="email" />
            </section>
            <section class="input-group">
                <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
                <x-select name="quest_type" label="Rodzaj zlecenia" :options="$questTypes" :required="true" />
                <x-input type="text" name="title" label="Tytuł utworu" />
                <x-input type="text" name="artist" label="Oryginalny wykonawca" />
                <x-input type="text" name="cover_artist" label="Coverujący" />
                <x-input type="url" name="link" label="Link do nagrania" />
                <x-input type="TEXT" name="wishes" label="Życzenia" />
            </section>
            <section class="input-group">
                <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
                <x-input type="text" name="price" label="Wycena (kod lub kwota)" :hint="$prices" />
                <x-input type="date" name="deadline" label="Termin wykonania" />
                <x-input type="checkbox" name="hard_deadline" label="Termin narzucony przez klienta" />
            </section>
        </div>
        <input type="submit" name="submit" value="Dodaj" class="hover-lift" />
    </form>
@endsection
