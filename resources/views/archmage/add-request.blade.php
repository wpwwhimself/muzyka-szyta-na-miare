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
                    <a href="#/" @popper(zlecenia klienta) target="_blank" id="client_quests_list">
                        <i class="fa-solid fa-up-right-from-square"></i>
                    </a>
                </h2>
                <x-select name="client_id" label="Istniejący klient" :options="$clients" :empty-option="true" :small="true" />
                <x-input type="text" name="client_name" label="Nazwisko/Nazwa" :autofocus="true" :required="true" />
                <x-input type="email" name="email" label="Adres e-mail" />
                <x-input type="tel" name="phone" label="Numer telefonu" />
                <x-input type="text" name="other_medium" label="Inna forma kontaktu" />
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
                                $("input", $("#client_id").parent().parent()).prop("disabled", true);
                                $("#client_name").val(res.client_name);
                                $("#email").val(res.email);
                                $("#phone").val(res.phone);
                                $("#other_medium").val(res.other_medium);
                                $("#contact_preference").val(res.contact_preference);
                                $("#wishes").html(res.default_wishes);
                                if(res.special_prices != null){$("#special-prices-warning").html(`<i class="fa-solid fa-triangle-exclamation"></i> Klient ma specjalną wycenę:<br>${res.special_prices}`);}
                                $("#client_quests_list").attr("href", "{{ route('quests') }}/" + $("#client_id").val()).show();
                            }
                        });
                    }else{
                        $("input", $("#client_id").parent().parent()).prop("disabled", false);
                        $("#client_name").val("");
                        $("#email").val("");
                        $("#phone").val("");
                        $("#other_medium").val("");
                        $("#contact_preference").val("");
                        $("#wishes").html("");
                        $("#special-prices-warning").html("");
                        $("#client_quests_list").hide().attr("href", "");
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
                <x-select name="quest_type" label="Rodzaj zlecenia" :options="$questTypes" :required="true" :small="true" />
                <x-input type="text" name="title" label="Tytuł utworu" />
                <div id="song-summary" class="hint-table">
                    <h3><i class="fa-solid fa-compact-disc"></i> Sugestie</h3>
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Tytuł</th>
                                <th>Gatunek</th>
                                <th>Wycena</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody class="positions">
                        </tbody>
                    </table>
                </div>
                <x-input type="text" name="artist" label="Wykonawca" />
                <x-input type="text" name="link" label="Link do nagrania" :small="true" />
                <x-select name="genre_id" label="Gatunek" :options="$genres" :small="true" :empty-option="true" :required="true" />
                <x-input type="TEXT" name="wishes" label="Życzenia dot. koncepcji utworu (np. budowa, aranżacja)" />
                <x-input type="TEXT" name="wishes_quest" label="Życzenia techniczne (np. liczba partii, transpozycja)" />
                <x-input type="date" name="hard_deadline" label="Termin klienta" />

                <script>
                function ghostBind(val = null){
                    for(let id of ["quest_type", "title", "artist", "link", "genre_id", "wishes"]){
                        if (val){
                            $(`#${id}`).addClass("ghost");
                        }else{
                            $(`#${id}`).removeClass("ghost");
                        }
                    }
                }
                $(document).ready(function(){
                    $("#title").change(function (e) {
                        if(e.target.value.length >= 2){
                            $.ajax({
                                type: "get",
                                url: "/song_data",
                                data: { title: e.target.value },
                                success: function (res) {
                                    const positions_list = $("#song-summary .positions");
                                    res = JSON.parse(res);
                                    let content = ``;
                                    res.forEach(song => {
                                        content += `<tr>`;
                                        if(song.link?.indexOf(",") > -1) song.link = song.link.substring(0, song.link.indexOf(","));
                                        content += `<td><input type='radio' name='song_id' value='${song.id}' onchange='ghostBind("${song.id}")' /></td>`;
                                        content += `<td>${song.title}</td>`;
                                        content += `<td>${song.genre}</td>`;
                                        content += `<td id="#song_price_code">${song.price_code}</td>`;
                                        content += `<td>`;
                                            if(song.notes) content += `<span class='clickable' title='Uwagi:\n${song.notes}'>🚩</span>`;
                                            if(song.link) content += `<a href="${song.link}" target="_blank" title='Link do materiałów'>💽</a>`;
                                            content += `<a href="{{ route('songs') }}#song${song.id}" target="_blank" title='Utwór'>📝</a>`;
                                        content += `</td>`;
                                        content += `</tr>`;
                                    });
                                    content += `<tr><td><input type='radio' name='song_id' value='0' onchange='ghostBind()' checked /></td><td colspan=4>Nowa piosenka</td></tr>`
                                    positions_list.html(content);
                                    $("#song-summary").show();
                                }
                            });
                        }else{
                            $("#song-summary .positions").html("");
                            $("#song-summary").hide();
                        }
                    });
                });
                </script>
            </section>

            <section class="input-group">
                <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
                <div id="special-prices-warning"></div>
                <x-input type="text" name="price_code" label="Kod wyceny" :hint="$prices" :required="false" />
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
                            }
                        });
                    }
                }
                $(document).ready(function(){
                    calcPriceNow();
                    $("#price_code").change(function (e) { calcPriceNow() });
                });
                </script>
                <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" />
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
