@extends('layouts.app', compact("title"))

@section('content')
    <form method="post" action={{ route("add-request-back") }}>
        @csrf
        <h1>Dodaj nowe zapytanie</h1>
        <div id="quest-box" class="flex-right">
            <section class="input-group">
                <h2>
                    <i class="fa-solid fa-user"></i>
                    Dane klienta
                    <a href="#/" @popper(szczegóły klienta) target="_blank" id="client_info"><i class="fa-solid fa-up-right-from-square"></i></a>
                </h2>
                <x-select name="client_id" label="Istniejący klient" :options="$clients" :empty-option="true" :small="true" />
                <x-input type="text" name="client_name" label="Nazwisko/Nazwa" :autofocus="true" :required="true" />
                <x-input type="email" name="email" label="Adres e-mail" :small="true" />
                <x-input type="tel" name="phone" label="Numer telefonu" :small="true" />
                <x-input type="text" name="other_medium" label="Inna forma kontaktu" :small="true" />
                <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" placeholder="email" />
                <script>
                function client_fields(){
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
                                $("#client_info").attr("href", "{{ route('clients') }}" + `?search=${$("#client_id").val()}`).show();
                            }
                        });
                    }else{
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
                $(document).ready(function(){
                    client_fields();
                    $("#client_id").change(function(){ client_fields() });
                });
                </script>
            </section>

            <section class="input-group">
                <h2><i class="fa-solid fa-cart-flatbed"></i> Dane utworu</h2>
                <x-select name="song_id" label="Istniejący utwór" :options="$songs" :empty-option="true" :small="true" />
                <x-input type="text" name="title" label="Tytuł utworu" />
                <x-input type="text" name="artist" label="Wykonawca" />
                <x-input type="text" name="link" label="Link do nagrania" :small="true" />
                <x-select name="genre_id" label="Gatunek" :options="$genres" :small="true" :empty-option="true" :required="true" />
                <x-input type="TEXT" name="wishes" label="Życzenia dot. koncepcji utworu (np. budowa, aranżacja)" />
                    <x-button label="Wpisz domyślne życzenia" icon="circle-question" action="#/" id="default-wishes-button" :small="true" />
                <x-input type="TEXT" name="wishes_quest" label="Życzenia techniczne (np. liczba partii, transpozycja)" />
                <x-input type="date" name="hard_deadline" label="Termin klienta" />

                <script>
                function song_fields(){
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
                        $("#title").val("");
                        $("#artist").val("");
                        $("#link").val("");
                        $("#genre_id").val("");
                        $("#song-price-sugg").html("");
                    }
                }
                $(document).ready(function(){
                    song_fields();
                    $("#song_id").change(function(){ song_fields() });
                });
                </script>
            </section>

            <section class="input-group">
                <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
                <x-select name="quest_type" label="Rodzaj zlecenia" :options="$questTypes" :small="true" :required="true" />
                <div id="song-price-sugg"></div>
                <div id="special-prices-warning"></div>
                <x-input type="text" name="price_code" label="Kod wyceny" :hint="$prices" :required="false" />
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
                                for(line of res.positions){
                                    content += `<span>${line[0]}</span><span>${line[1]}</span>`;
                                }
                                positions_list.html(content);
                                sum_row.html(`<span>Razem:</span><span>${res.price} zł${res.minimal_price ? " (cena minimalna)" : ""}</span>`);
                                if(res.override) positions_list.addClass("overridden");
                                    else positions_list.removeClass("overridden");

                                checkMonthlyPaymentLimit(res.price);
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
                <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" />
                <x-input type="date" name="delayed_payment" label="Opóźnienie wpłaty" />
            </section>

            <section>
                <h2><i class="fa-solid fa-calendar-days"></i> Grafik</h2>
                <x-calendar />
            </section>
        </div>
        <x-input type="TEXT" name="comment" label="Komentarz do zmiany" />
        <input type="hidden" name="intent" value="new" />
        <div class="flexright">
            <x-button
                label="Dodaj do listy" icon="1" name="new_status" value="1"
                action="submit"
                />
            <x-button
                label="Oddaj do wyceny" icon="5" name="new_status" value="5"
                action="submit"
                />
        </div>
    </form>

    <script>
    $(document).ready(function(){
    $("#client_id").select2();
    $("#song_id").select2();
    });
    </script>
@endsection
