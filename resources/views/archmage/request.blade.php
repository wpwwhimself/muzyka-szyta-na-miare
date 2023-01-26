@extends('layouts.app', ["title" => ($request->title ?? "bez tytułu") . " | $title"])

@section('content')
<form method="POST" action="{{ route("mod-request-back") }}">
    @csrf
    <script>
    $(document).ready(function(){
        const status = parseInt($(".quest-phase").attr("status"));
        //disabling inputs if no change is allowed
        if([4, 5, 7, 8, 9].includes(status)){
            $("input:not(input[type=hidden]), select, textarea").prop("disabled", true);
        };
    });
    </script>
    <h1>Szczegóły zapytania</h1>
    <x-phase-indicator :status-id="$request->status_id" />

    @if ($request->quest_id)
    <h2>
        Zlecenie przepisane z numerem {{ $request->quest_id }}
        <x-a href='{{ route("quest", ["id" => $request->quest_id]) }}'>Przejdź do zlecenia</x-a>
    </h2>
    @endif

    <div id="quest-box" class="flex-right">
        <section class="input-group">
            <h2>
                <i class="fa-solid fa-user"></i>
                Dane klienta
                @if ($request->client_id)
                <a href="{{ route('quests', ['client_id' => $request->client_id]) }}" @popper(zlecenia klienta) target="_blank">
                    <i class="fa-solid fa-up-right-from-square"></i>
                </a>
                @endif
            </h2>
            <x-select name="client_id" label="Istniejący klient" :options="$clients" :empty-option="true" value="{{ $request->client_id }}" :small="true" />
            <x-input type="text" name="client_name" label="Nazwisko/Nazwa" :autofocus="true" :required="true" value="{{ $request->client_name }}" />
            <x-input type="email" name="email" label="Adres e-mail" value="{{ $request->email }}" />
            <x-input type="tel" name="phone" label="Numer telefonu" value="{{ $request->phone }}" />
            <x-input type="text" name="other_medium" label="Inna forma kontaktu" value="{{ $request->other_medium }}" />
            <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" placeholder="email" value="{{ $request->contact_preference }}" />
            <script>
            function client_fields(dont_clear_fields = false){
                const empty = $("#client_id").val() == "";
                let cldata = {};

                if(!empty){
                    $.ajax({
                        url: "{{ url('client_data') }}",
                        type: "get",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: $("#client_id").val()
                        },
                        success: function(res){
                            res = JSON.parse(res);
                            $("input", $("#client_id").parent().parent()).prop("disabled", true);
                            $("#client_name").val(res.client_name);
                            $("#email").val(res.email);
                            $("#phone").val(res.phone);
                            $("#other_medium").val(res.other_medium);
                            $("#contact_preference").val(res.contact_preference);
                            // $("#wishes").html(res.default_wishes);
                            if(res.special_prices != null){
                                $("#special-prices-warning").html(`<i class="fa-solid fa-triangle-exclamation"></i> Klient ma specjalną wycenę:<br>${res.special_prices}`);
                            }
                        }
                    });
                }else{
                    if(!dont_clear_fields){
                        $("input", $("#client_id").parent().parent()).prop("disabled", false);
                        $("#client_name").val("");
                        $("#email").val("");
                        $("#phone").val("");
                        $("#other_medium").val("");
                        $("#contact_preference").val("");
                        // $("#wishes").html("");
                        $("#special-prices-warning").html("");
                    }
                }
            }
            $(document).ready(function(){
                client_fields(true);
                $("#client_id").change(function(){ client_fields() });
            });
            </script>
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
            <x-select name="quest_type" label="Rodzaj zlecenia" :small="true" :options="$questTypes" :required="true" value="{{ $request->quest_type_id }}" />
            <x-input type="text" name="title" label="Tytuł utworu" value="{{ $request->title }}" />
            <x-input type="text" name="artist" label="Wykonawca" value="{{ $request->artist }}" />
            <x-input type="url" name="link" label="Link do nagrania" :small="true" value="{{ $request->link }}" />
            <x-link-interpreter :raw="$request->link" />
            <x-select name="genre_id" label="Gatunek" :options="$genres" :small="true" :empty-option="true" value="{{ $request->genre_id }}" :required="true" />
            <x-input type="TEXT" name="wishes" label="Życzenia dot. koncepcji utworu (np. budowa, aranżacja)" value="{{ $request->wishes }}" />
            <x-input type="TEXT" name="wishes_quest" label="Życzenia techniczne (np. liczba partii, transpozycja)" value="{{ $request->wishes_quest }}" />
            <x-input type="date" name="hard_deadline" label="Termin narzucony przez klienta" value="{{ $request->hard_deadline?->format('Y-m-d') }}" />

            <h2><i class="fa-solid fa-compact-disc"></i> Porównanie</h2>
            <x-select name="song_id" label="Istniejący utwór" :options="$songs" :empty-option="true" :small="true" />
            <div id="song-summary" class="hint-table">
                <div class="positions"></div>
            </div>
            <x-input type="checkbox" name="bind_with_song" label="Powiąż z tym utworem" />
            <script>
            function loadSong(){
                const song_id = $("#song_id").val();
                const positions_list = $("#song-summary .positions");
                const bind_checkbox = $("#bind_with_song").parent();
                const empty = $("#song_id").val() == "";
                let songdata = {};

                if(!empty){
                    $.ajax({
                        url: "{{ url('song_data') }}",
                        type: "get",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: $("#song_id").val()
                        },
                        success: function(res){
                            res = JSON.parse(res);
                            let content = ``;
                            content += `<span>Tytuł</span><span><a href="${res.link}" target="_blank">${res.title}</a></span>`;
                            content += `<span>Artysta</span><span>${res.artist}</span>`;
                            content += `<span>Rodzaj zlecenia</span><span>${res.quest_type_id}</span>`;
                            content += `<span>Kod cenowy</span><span id="#song_price_code">${res.price_code}</span>`;
                            content += `<span>Uwagi</span><span>${res.notes}</span>`;
                            positions_list.html(content);
                            bind_checkbox.show();
                        }
                    });
                }else{
                    positions_list.html("");
                    bind_checkbox.hide();
                }
            }
            $(document).ready(function(){
                loadSong();
                $("#song_id").change(function (e) { loadSong() });
            });
            </script>
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
            <div id="special-prices-warning"></div>
            <x-input type="text" name="price_code" label="Kod wyceny" :hint="$prices" value="{{ $request->price_code }}" :required="true" />
            <div id="price-summary" class="hint-table">
                <div class="positions"></div>
                <hr />
                <div class="summary"><span>Razem:</span><span>0 zł</span></div>
            </div>
            <script>
            function calcPriceNow(){
                const labels = $("#price_code").val();
                const client_id = $("#client_id").val();
                const positions_list = $("#price-summary .positions");
                const sum_row = $("#price-summary .summary");
                if(labels == "") positions_list.html(`<p class="grayed-out">podaj kategorie wyceny</p>`);
                else{
                    $.ajax({
                        url: "{{ url('price_calc') }}",
                        type: "post",
                        data: {
                            _token: '{{ csrf_token() }}',
                            labels: labels,
                            client_id: client_id,
                            quoting: {{ $request->status_id == 1 ? "true" : "false" }}
                        },
                        success: function(res){
                            let content = ``;
                            for(line of res[1]){
                                content += `<span>${line[0]}</span><span>${line[1]}</span>`;
                            }
                            positions_list.html(content);
                            sum_row.html(`<span>Razem:</span><span>${res[0]} zł</span>`);
                            if(res[2]) positions_list.addClass("overridden");
                                else positions_list.removeClass("overridden");
                        }
                    });
                }
            }
            $(document).ready(function(){
                calcPriceNow();
                $("#price_code").change(function (e) { calcPriceNow() });
            });
            </script>
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $request->deadline?->format('Y-m-d') }}" />
        </section>

        @if (in_array($request->status_id, [1, 6]))
        <section class="input-group" id="quest-calendar">
            <h2><i class="fa-solid fa-calendar-days"></i> Grafik</h2>
            <x-calendar />
        </section>
        @endif

        <section class="input-group">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            <x-quest-history :quest="$request" />
        </section>
    </div>
    <div id="step-1" class="flexright">
        <x-input type="TEXT" name="comment" label="Komentarz do odrzucenia" />
        <input type="hidden" name="intent" value="{{ in_array($request->status_id, [4, 5, 7, 8]) ? 'review' : 'change' }}" />
        @if ($request->status_id != 9) <x-button label="Podgląd maila do oddania" icon="comment-dots" id="mail-prev" action="{{ route('mp-rq', ['id' => $request->id]) }}" target="_blank" :small="true" /> @endif
        @if (in_array($request->status_id, [1, 6])) <x-button label="Popraw i oddaj do wyceny" icon="5" name="new_status" value="5" action="submit" /> @endif
        @if (in_array($request->status_id, [1, 6])) <x-button label="Nie podejmę się" icon="4" name="new_status" value="4" :danger="true" action="submit" /> @endif
        @if (in_array($request->status_id, [5])) <x-button label="Zatwierdź w imieniu klienta" icon="9" action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}" /> @endif
        @if (in_array($request->status_id, [5])) <x-button label="Odrzuć w imieniu klienta" name="new_status" value="8" icon="8" :danger="true" action="submit" /> @endif
        @if (in_array($request->status_id, [4, 7, 8])) <x-button label="Odnów w imieniu klienta" icon="26" name="new_status" value="1" action="submit" /> @endif
    </div>
</form>

<script>
$(document).ready(function(){
$("#client_id").select2();
$("#song_id").select2();
});
</script>
@endsection
