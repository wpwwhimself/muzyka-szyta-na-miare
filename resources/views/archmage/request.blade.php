@extends('layouts.app', ["title" => ($request->title ?? "bez tytułu") . " | $title"])

@section('content')
<form method="POST" action="{{ route("mod-request-back") }}">
    @csrf
    <script>
    $(document).ready(function(){
        const status = parseInt($(".quest-phase").attr("status"));
        //disabling inputs if no change is allowed
        if([4, 5, 7, 8, 9].includes(status)){
            $("input:not(input[type=hidden]), select, textarea:not(#comment)").prop("disabled", true);
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
                <a href="{{ route('clients', ['search' => $request->client?->id]) }}" @popper(szczegóły klienta) target="_blank" id="client_info"><i class="fa-solid fa-up-right-from-square"></i></a>
            </h2>
            <x-select name="client_id" label="Istniejący klient" :options="$clients" :empty-option="true" value="{{ $request->client_id }}" :small="true" />
            <x-input type="text" name="client_name" label="Nazwisko/Nazwa" :autofocus="true" :required="true" value="{{ _ct_($request->client_name) }}" />
            <x-input type="email" name="email" label="Adres e-mail" value="{{ _ct_($request->email) }}" :small="true" />
            <x-input type="tel" name="phone" label="Numer telefonu" value="{{ _ct_($request->phone) }}" :small="true" />
            <x-input type="text" name="other_medium" label="Inna forma kontaktu" value="{{ _ct_($request->other_medium) }}" :small="true" />
            <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" placeholder="email" value="{{ _ct_($request->contact_preference) }}" />

            @if(in_array($request->status_id, [1, 6, 96]))
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
                            $("#client_name").val(res.client_name);
                            $("#email").val(res.email);
                            $("#phone").val(res.phone);
                            $("#other_medium").val(res.other_medium);
                            $("#contact_preference").val(res.contact_preference);
                            if(res.default_wishes != null){
                                $("#default-wishes-button")
                                    .attr("data-fill", res.default_wishes)
                                    .show()
                                    .click(() => {$("#wishes").val($("#default-wishes-button").attr("data-fill"))});
                            }else{
                                $("#default-wishes-button")
                                    .attr("data-fill", "")
                                    .hide()
                                    .off();
                            }
                            if(res.special_prices != null){$("#special-prices-warning").html(`<i class="fa-solid fa-triangle-exclamation"></i> Klient ma specjalną wycenę:<br>${res.special_prices}`);}
                            $("#client_info").attr("href", "{{ route('quests') }}" + `?client=${$("#client_id").val()}`).show();
                        }
                    });
                }else{
                    if(!dont_clear_fields){
                        $("#client_name").val("");
                        $("#email").val("");
                        $("#phone").val("");
                        $("#other_medium").val("");
                        $("#contact_preference").val("");
                        $("#default-wishes-button").attr("data-fill", "").hide().off();
                        $("#special-prices-warning").html("");
                        $("#client_info").hide().attr("href", "");
                    }
                }
            }
            $(document).ready(function(){
                client_fields(true);
                if($("#client_id").val() == "") $("#client_info").hide();
                $("#client_id").change(function(){ client_fields() });
            });
            </script>
            @endif
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-cart-flatbed"></i> Dane utworu</h2>
            <x-select name="song_id" label="Istniejący utwór" :options="$songs" :empty-option="true" :small="true" :value="$request->song_id" />
            <x-input type="text" name="title" label="Tytuł utworu" value="{{ $request->title }}" />
            <x-input type="text" name="artist" label="Wykonawca" value="{{ $request->artist }}" />
            <x-input type="text" name="link" label="Link do nagrania" :small="true" value="{{ $request->link }}" />
            <x-link-interpreter :raw="$request->link" />
            <x-select name="genre_id" label="Gatunek" :options="$genres" :small="true" :empty-option="true" value="{{ $request->genre_id }}" :required="true" />
            <x-input type="TEXT" name="wishes" label="Życzenia dot. koncepcji utworu (np. budowa, aranżacja)" value="{{ $request->wishes }}" />
                <x-button label="Wpisz domyślne życzenia" icon="circle-question" action="#/" id="default-wishes-button" :small="true" />
            <x-input type="TEXT" name="wishes_quest" label="Życzenia techniczne (np. liczba partii, transpozycja)" value="{{ $request->wishes_quest }}" />
            <x-input type="date" name="hard_deadline" label="Termin narzucony przez klienta" value="{{ $request->hard_deadline?->format('Y-m-d') }}" />

            @if(in_array($request->status_id, [1, 6, 96]))
            <script>
            function song_fields(dont_clear_fields = false){
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
                                $("#title").val(res.title);
                                $("#artist").val(res.artist);
                                $("#link").val(res.link);
                                $("#genre_id").val(res.genre_id);
                                $("#wishes").val(res.notes);
                                $("#song-price-sugg").html(`Sugerowana cena: ${res.price_code}`);
                            }
                        });
                    }else{
                        if(!dont_clear_fields){
                            $("#title").val("");
                            $("#artist").val("");
                            $("#link").val("");
                            $("#genre_id").val("");
                            $("#wishes").val("").trigger("change");
                            $("#song-price-sugg").html("");
                        }
                    }
                }
                $(document).ready(function(){
                    song_fields(true);
                    $("#song_id").change(function(){ song_fields() });
                });
            </script>
            @endif
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
            <x-select name="quest_type" label="Rodzaj zlecenia" :small="true" :options="$questTypes" :required="true" value="{{ $request->quest_type_id }}" />
            <div id="song-price-sugg"></div>
            <div id="special-prices-warning"></div>
            <x-input type="text" name="price_code" label="Kod wyceny" :hint="$prices" value="{{ $request->price_code }}" />
            <div id="price-summary" class="hint-table">
                <div class="positions"></div>
                <hr />
                <div class="summary"><span>Razem:</span><span>0 zł</span></div>
            </div>
            <div id="delayed-payments-summary" class="hint-table">
                <div class="positions"></div>
                <hr />
                <div class="summary"></div>
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
                            quoting: true
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

                            checkMonthlyPaymentLimit(res[0]);
                        }
                    });
                }
            }
            function checkMonthlyPaymentLimit(price){
                const positions_list = $("#delayed-payments-summary .positions");
                const sum_row = $("#delayed-payments-summary .summary");

                $.ajax({
                    url: "{{ url('monthly_payment_limit') }}",
                    type: "post",
                    data: {
                        _token: '{{ csrf_token() }}',
                        amount: price,
                    },
                    success: function(res){
                        let when_to_ask = "";
                        switch(res.when_to_ask){
                            case 0: when_to_ask = "<span class='success'>od razu</span>"; break;
                            case 1: when_to_ask = "<span class='warning'>w przyszłym miesiącu</span>"; break;
                            default: when_to_ask = "<span class='error'>za dwa miesiące</span>";
                        }
                        let content = ``;
                        content += `<span>Saturacja</span><span>${res.saturation[0]} zł • ${res.saturation[1]} zł • ${res.saturation[2]} zł</span>`;
                        positions_list.html(content);
                        sum_row.html(`<span>Kiedy można brać?</span><span>${when_to_ask}</span>`);
                    }
                });
            }
            $(document).ready(function(){
                calcPriceNow();
                $("#price_code").change(function (e) { calcPriceNow() });
            });
            </script>
            @if ($request->client?->budget && in_array($request->status_id, [1, 5, 6]))
            <span class="{{ $request->client->budget >= $request->price ? 'success' : 'warning' }}">
                <i class="fa-solid fa-sack-dollar"></i>
                Budżet w wysokości <b>{{ as_pln($request->client->budget) }}</b> automatycznie
                <br>
                pokryje
                @if ($request->client->budget >= $request->price)
                całą kwotę zlecenia
                @else
                część kwoty zlecenia
                @endif
            </span>
            @endif
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $request->deadline?->format('Y-m-d') }}" />
            <x-input type="date" name="delayed_payment" label="Opóźnienie wpłaty" value="{{ $request->delayed_payment?->format('Y-m-d') }}" />
        </section>

        @if (in_array($request->status_id, [1, 6, 96]))
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
        <x-input type="TEXT" name="comment" label="Komentarz do zmiany" />
        <input type="hidden" name="id" value="{{ $request->id }}" />
        <input type="hidden" name="intent" value="{{ in_array($request->status_id, [4, 5, 7, 8]) ? 'review' : 'change' }}" />
        @if (in_array($request->status_id, [1, 6, 96])) <x-button label="Poproś o doprecyzowanie" icon="95" name="new_status" value="95" action="submit" /> @endif
        @if (in_array($request->status_id, [95])) <x-button label="Klient odpowiada" icon="96" name="new_status" value="96" action="submit" /> @endif
        @if (in_array($request->status_id, [1, 6, 96])) <x-button label="Popraw i oddaj do wyceny" icon="5" name="new_status" value="5" action="submit" /> @endif
        @if (in_array($request->status_id, [1, 6, 96])) <x-button label="Nie podejmę się" icon="4" name="new_status" value="4" :danger="true" action="submit" /> @endif
        @if (in_array($request->status_id, [5])) <x-button label="Klient zatwierdza" icon="9" action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}" /> @endif
        @if (in_array($request->status_id, [5])) <x-button label="Klient chce poprawki" name="new_status" value="6" icon="6" action="submit" /> @endif
        @if (in_array($request->status_id, [5, 95])) <x-button label="Klient odrzuca" name="new_status" value="8" icon="8" :danger="true" action="submit" /> @endif
        @if (in_array($request->status_id, [4, 7, 8])) <x-button label="Klient odnawia" icon="26" name="new_status" value="1" action="submit" /> @endif
    </div>
</form>

<script>
$(document).ready(function(){
$("#client_id").select2();
$("#song_id").select2();
});
</script>
@endsection
