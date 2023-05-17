@extends('layouts.app', compact("title"))

@section('content')
    @php
        $hint['link'] = ["Link do nagrania" => "Podaj link do oryginalnego wykonania, na którym mam opierać pracę, np. Youtube"];
        $hint['wishes'] = ["Życzenia do zlecenia" => "Wszelkie uwagi związane z projektem: zmiana transpozycji, dodanie linii melodycznej, inna instrumentalizacja itp."];
        $hint['deadline'] = ["Termin wykonania" => "Jeśli Twoje zlecenie jest pilne, tu możesz podać najpóźniejszy termin odebrania plików. Jeśli nie masz takich ograniczeń, pozostaw puste"];
    @endphp
    <form method="post" action={{ route("add-request-back") }}>
        @csrf
        <h1>Dodaj nowe zapytanie</h1>
        <div id="quest-box" class="flex-right">
            <section class="input-group">
                <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
                <div class="bulk-box sc-line">
                    <x-select name="quest_type[]" label="Rodzaj zlecenia" :options="$questTypes" :required="true" :small="true" />
                    <x-input type="text" name="title[]" label="Tytuł utworu" />
                    <x-input type="text" name="artist[]" label="Oryginalny wykonawca" />
                    <x-input type="text" name="link[]" label="Link do nagrania" :small="true" :hint="$hint['link']" />
                    <x-input type="TEXT" name="wishes[]" label="Życzenia" value="{{ Auth::user()->client->default_wishes }}" :hint="$hint['wishes']" />
                    <x-input type="date" name="hard_deadline[]" label="Opcjonalny termin wykonania" :hint="$hint['deadline']" />
                </div>
                <x-button action="#/" id="request_bulk_add" icon="plus" label="Dodaj kolejne zlecenie" :small="true" />
            </section>
        </div>
        <script>
        $(document).ready(function(){
            $("#request_bulk_add").click(function(){
                $(".bulk-box:first-of-type").clone().insertBefore($(this));
                $(".bulk-box:last-of-type :is(input, textarea)").val("");
            });
        });
        </script>
        <input type="hidden" name="intent" value="new" />
        <x-button
            label="Wyślij zapytanie" icon="1" name="new_status" value="1"
            action="submit"
            />
    </form>
@endsection
