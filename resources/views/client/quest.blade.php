@extends('layouts.app', ["title" => ($quest->song->title ?? "bez tytułu")." | $quest->id"])

@section('content')
<div class="input-container">
    <h1>Szczegóły zlecenia</h1>
    
    <x-phase-indicator :status-id="$quest->status_id" />
    
    <div id="quest-box" class="flex-right">
        <section class="input-group">
            <h2><i class="fa-solid fa-compact-disc"></i> Szczegóły utworu</h2>
            <x-input type="text" name="" label="ID utworu" value="{{ $quest->song->id }}" :disabled="true" :small="true" />
            <x-input type="text" name="" label="Tytuł" value="{{ $quest->song->title }}" :disabled="true" />
            <x-input type="text" name="" label="Wykonawca" value="{{ $quest->song->artist }}" :disabled="true" />
            <x-link-interpreter :raw="$quest->song->link" />
            <x-input type="TEXT" name="wishes" label="Życzenia" value="{{ $quest->song->notes }}" :disabled="true" />
        </section>
        <section class="input-group">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
            <div id="price-summary" class="hint-table">
                <div class="positions"></div>
                <hr />
                <div class="summary"><span>Razem:</span><span>0 zł</span></div>
            </div>
            <script>
            function calcPriceNow(){
                const labels = "{{ $quest->price_code_override }}";
                const client_id = "{{ $quest->client_id }}";
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
                            client_id: client_id
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
            <x-input type="checkbox" name="paid" label="Opłacono" value="{{ $quest->paid }}" :disabled="true" />
            <div class="tutorial">
                <p><i class="fa-solid fa-circle-question"></i> Opłaty projektu możesz dokonać na 2 sposoby:</p>
                <ul>
                    <li>na numer konta <b>53 1090 1607 0000 0001 1633 2919</b><br>
                        (w tytule ID zlecenia),</li>
                    <li>BLIKiem na numer telefonu <b>530 268 000</b>.</li>
                </ul>
                <p>Nie jest ona wymagana do przeglądania plików,<br>
                    ale będzie potrzebna do ich pobrania.</p>
            </div>
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $quest->deadline }}" :disabled="true" />
            @if ($quest->hard_deadline)
            <x-input type="date" name="hard_deadline" label="Twój termin wykonania" value="{{ $quest->hard_deadline }}" :disabled="true" />
            @endif
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-file-waveform"></i> Pliki</h2>

            {{-- jeśli pusty --}}
            <p class="tutorial">
                <i class="fa-solid fa-circle-question"></i>
                Tutaj pojawią się pliki związane<br>
                z przygotowywanym dla Ciebie zleceniem.<br>
                Będzie możliwość ich przejrzenia i odsłuchania,<br>
                a po dokonaniu wpłaty – również pobrania.
            </p>
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            <x-quest-history :quest="$quest" />
        </section>
    </div>

    <form action="{{ route('mod-quest-back') }}" method="POST" id="phases">
        <script>
        $(document).ready(function(){
            $('#phases button').hide();
            const whatCanBeSeen = {
                11: [],
                12: [],
                13: [],
                15: [16, 18, 19],
                16: [],
                18: [],
                19: [26],
                26: []
            }
            if(whatCanBeSeen[{{ $quest->status_id }}].length == 0){
                $(`textarea[name='comment']`).parent().hide();
            }
            for(button of whatCanBeSeen[{{ $quest->status_id }}]){
                $(`button[value=${button}]`).show();
            }
            $('button[value={{ $quest->status_id }}]').hide();
        });
        </script>
        <div class="flexright">
            @csrf
            <p class="tutorial">
                <i class="fa-solid fa-circle-question"></i>
                Jeśli nie podoba Ci się to, co dla Ciebie przygotowałem, w polu poniżej możesz napisać, co dokładnie.
                Ta informacja będzie widoczna i na jej podstawie będę mógł wprowadzić poprawki.
            </p>
            <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu" />
            <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
            <x-button action="submit" name="status_id" icon="12" value="12" label="Rozpocznij prace" />
            <x-button action="submit" name="status_id" icon="13" value="13" label="Zawieś prace" />
            <x-button action="submit" name="status_id" icon="15" value="15" label="Oddaj do recenzji" />
            <x-button action="submit" name="status_id" icon="16" value="16" label="Recenzja negatywna" />
            <x-button action="submit" name="status_id" icon="18" value="18" label="Odrzuć" :danger="true" />
            <x-button action="submit" name="status_id" icon="19" value="19" label="Zaakceptuj"  />
            <x-button action="submit" name="status_id" icon="26" value="26" label="Powróć" />
        </div>
    </form>
</div>

@endsection
