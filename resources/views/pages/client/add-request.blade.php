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
        <x-section title="Dane zlecenia" icon="cart-flatbed">
            <div class="bulk-box sc-line flex right center">
                <x-select name="quest_type[]" label="Rodzaj zlecenia" :options="$questTypes" :required="true" :small="true" />
                <x-input type="text" name="title[]" label="Tytuł utworu" />
                <x-input type="text" name="artist[]" label="Wykonawca" />
                <x-input type="text" name="link[]" label="Link do oryginalnych nagrań (oddzielone przecinkami)" :small="true" :hint="$hint['link']" />
                <x-input type="TEXT" name="wishes[]" label="Życzenia (np. styl, czy z linią melodyczną)" value="{{ Auth::user()->notes->default_wishes }}" :hint="$hint['wishes']" />
                <x-input type="date" name="hard_deadline[]" label="Kiedy najpóźniej chcesz otrzymać materiały? (opcjonalnie)" :hint="$hint['deadline']" />
            </div>
            <x-button action="#/" id="request_bulk_add" icon="plus" label="Dodaj kolejny utwór" :small="true" />
        </x-section>

        <x-section title="Uwagi" icon="circle-question">
            <ul>
                <li>Jeśli zamieszczasz wiele źródeł, będę zakładać, że budowa utworu będzie oparta głównie na pierwszym z nich.</li>
            </ul>
        </x-section>

        <script>
        $(document).ready(function(){
            $("#request_bulk_add").click(function(){
                console.log($(".bulk-box:first-of-type"));
                $(".bulk-box:first").clone().insertBefore($(this));
                $(".bulk-box:last input, .bulk-box:last textarea").val("");
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
