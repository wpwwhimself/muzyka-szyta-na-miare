@extends('layouts.app')
@section("title", ($request->title ?? "bez tytułu"))
@section("subtitle", "Zapytanie")

@section('content')

<x-shipyard.app.form method="POST" :action="route('mod-request-back')">
    <x-slot:actions>
        @auth
        <x-shipyard.ui.button
            icon="angles-left"
            label="Wróć do listy"
            :action="route('requests')"
        />
        @endauth
    </x-slot:actions>

    <h1>
        Szczegóły zapytania
        @if (sumWarnings($warnings))
        <x-warning>
            Jest kilka rzeczy, z którymi musisz się koniecznie zapoznać!
        </x-warning>
        @endif
    </h1>
    <x-phase-indicator :status-id="$request->status_id" />

    @if ($request->quest_id)
    <h2>
        Zlecenie przepisane z numerem {{ $request->quest_id }}
        <x-a href="{{ route('quest', ['id' => $request->quest_id]) }}">Przejdź do zlecenia</x-a>
    </h2>
    @endif

    <div class="flex down">
        <x-extendo-block key="meta"
            :header-icon="model_icon('songs')"
            title="Dane zlecenia"
            subtitle="Jaki utwór mam przygotować?"
            :extended="true"
        >
            <x-input type="text" name="title" label="Tytuł utworu" value="{{ $request->title }}" :disabled="true" />
            <x-input type="text" name="artist" label="Wykonawca" value="{{ $request->artist }}" :disabled="true" />
            <x-extendo-section title="Link do nagrania">
                <x-link-interpreter :raw="$request->link" />
            </x-extendo-section>
            <x-input type="TEXT" name="wishes" label="Życzenia dot. koncepcji utworu (np. budowa, aranżacja)" value="{{ $request->wishes }}" :disabled="true" />
            <x-input type="TEXT" name="wishes_quest" label="Życzenia techniczne (np. liczba partii, transpozycja)" value="{{ $request->wishes_quest }}" :disabled="true" />
        </x-extendo-block>

        <x-extendo-block key="quote"
            :header-icon="model_icon('prices')"
            title="Wycena"
            subtitle="Na jakich warunkach go przygotuję?"
            :warning="$warnings['quote']"
            :extended="true"
        >
            @if (!$request->price)
            <p class="yellowed-out"><i class="fa-solid fa-hourglass-half fa-fade"></i> pojawi się w ciągu najbliższych dni</p>
            @elseif ($request->price && $request->status_id == 1)
            <p class="yellowed-out"><i class="fa-solid fa-hourglass-half fa-fade"></i> poniższa wycena może być nieaktualna – poczekaj na odpowiedź</p>
            @endif

            <div>
                <div id="price-summary" class="hint-table">
                    <div class="positions"></div>
                    <hr />
                    <div class="summary"><span>Razem:</span><span>0 zł</span></div>
                </div>
                <script>
                function calcPriceNow(){
                    const labels = "{{ $request->price_code }}";
                    const client_id = {!! $request->client_id ?? "\"\"" !!};
                    const positions_list = $("#price-summary .positions");
                    const sum_row = $("#price-summary .summary");
                    if(labels == "") $("#price-summary").hide();
                    else{
                        $.ajax({
                            url: "/api/price_calc",
                            type: "post",
                            data: {
                                _token: '{{ csrf_token() }}',
                                labels: labels,
                                client_id: client_id
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
                            }
                        });
                    }
                }
                $(document).ready(function(){
                    calcPriceNow();
                });
                </script>
                @if ($request->client?->budget && in_array($request->status_id, [5, 6]))
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
            </div>

            <div>
                @if ($request->hard_deadline)
                <x-input type="date" name="hard_deadline" label="Twój termin wykonania" value="{{ $request->hard_deadline?->format('Y-m-d') }}" :disabled="true" />
                @endif
                @if ($request->deadline)
                <x-input type="date" name="deadline" label="Do kiedy (włącznie) ja oddam pliki" value="{{ $request->deadline?->format('Y-m-d') }}" :disabled="true" />
                @endif
            </div>

            <x-extendo-section>
                @if ($request->price && $request->status_id == 5)
                    @if ($request->deadline)
                    <p>
                        Termin oddania jest liczony do podanego dnia włącznie.
                        Są duże szanse, że uda mi się wykonać zlecenie szybciej,
                        ale to jest najpóźniejszy dzień.
                    </p>
                    @endif
                    <p>Opłaty projektu będzie można dokonać na 2 sposoby:</p>
                    <ul>
                        <li>na numer konta,</li>
                        <li>BLIKiem na numer telefonu.</li>
                    </ul>
                    <p>Pliki będą dostępne z poziomu tej strony internetowej.</p>
                @endif
                @if ($request->delayed_payment)
                <x-warning>
                    Z uwagi na limity przyjmowanych przeze mnie wpłat z racji prowadzenia działalności nierejestrowanej,
                    <b>proszę o dokonanie wpłaty po {{ $request->delayed_payment->format('d.m.Y') }}</b>.
                    Po zaakceptowaniu zlecenia dostęp do plików (kiedy tylko się pojawią) zostanie przyznany automatycznie.
                </x-warning>
                @endif
            </x-extendo-section>
        </x-extendo-block>

        <x-quest-history :quest="$request" :extended="in_array($request->status_id, [5, 95])" />
    </div>
    @if (in_array($request->status_id, [4, 7, 8]))
    <x-tutorial>
        Zapytanie zostało zamknięte, ale nadal możesz je przywrócić w celu ponownego złożenia zamówienia.
    </x-tutorial>
    @endif
    <div id="phases">
        <input type="hidden" name="id" value="{{ $request->id }}" />
        <input type="hidden" name="intent" value="review" />
        @if($request->status_id != 5)
        <div class="flexright">
            @if (in_array($request->status_id, [1, 6, 96]))
            <x-button action="#/" statuschanger="{{ $request->status_id }}" is-follow-up="1" icon="{{ $request->status_id }}" label="Popraw ostatni komentarz" />
            @endif
            @if (in_array($request->status_id, [95])) <x-button action="#/" statuschanger="96" icon="96" label="Odpowiedz" /> @endif
            @if (in_array($request->status_id, [5])) <x-button label="Potwierdź" statuschanger="9" icon="9" action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}" /> @endif
            @if (in_array($request->status_id, [5])) <x-button action="#/" statuschanger="6" icon="6" label="Poproś o ponowną wycenę" /> @endif
            @if (in_array($request->status_id, [5, 95])) <x-button action="#/" statuschanger="8" icon="8" label="Zrezygnuj ze zlecenia" /> @endif
            @if (in_array($request->status_id, [4, 7, 8])) <x-button action="#/" statuschanger="1" icon="1" label="Odnów" /> @endif
        </div>
        <div id="statuschanger">
            {{-- @if (in_array($request->status_id, [4, 5, 7, 8, 95])) --}}
            <x-tutorial>
                W historii zlecenia pojawi się Twój komentarz.
            </x-tutorial>
            <x-input type="TEXT" name="comment" label="" placeholder="Tutaj wpisz swój komentarz..." />
            {{-- @endif --}}
            <x-button action="submit" name="new_status" icon="paper-plane" value="5" label="Wyślij" />
        </div>
        <script>
        $(document).ready(function(){
            $("#statuschanger").hide();

            $("a[statuschanger]").click(function(){
                /*wyczyść możliwe ghosty*/
                $("a[statuschanger].ghost").removeClass("ghost");

                let status = $(this).attr("statuschanger"); if(status == 9) return;
                $(`#phases button[type="submit"]`).val(status);
                $("#statuschanger").show();
                for(i of [9, 6, 8, 96]){
                    if(i == status) continue;
                    $(`a[statuschanger="${i}"]`).addClass("ghost");
                }

                $("#statuschanger .history-position").removeClass((index, className) => className.match(/p-\d*/).join(" ")).addClass("p-"+status);

                const comment_field = document.querySelector("#statuschanger #comment");
                if($(this).attr("is-follow-up")){
                    const last_comment = $(`#quest-history .history-position .p-${status}:last`).attr("data-comment");
                    comment_field.innerHTML = last_comment;
                }else{
                    comment_field.innerHTML = "";
                }
                comment_field.scrollIntoView({behavior: "smooth"});
                comment_field.focus();
            });
        });
        </script>
        @else
        <div id="opinion-1">
            <h2>Czy odpowiada Ci powyższa wycena?</h2>
            <div>
                <x-button label="Tak" icon="check" action="none" onclick="goToConfirm2()" />
                <x-button label="Nie" icon="times" action="none" onclick="goToReject()" />
            </div>
        </div>
        <div id="opinion-2" class="hidden">
            <h2>Co chciał{{ client_polonize($request->client_name)["kobieta"] ? "a" : "" }}byś zmienić?</h2>
            <div>
                <x-button onclick="expandReject(this)" optbc="link" label="Link do nagrania" icon="compact-disc" action="#/" :small="true" />
                <x-button onclick="expandReject(this)" optbc="wishes" label="Życzenia" icon="note-sticky" action="#/" :small="true" />
                <x-button onclick="expandReject(this)" optbc="deadline" label="Czas oczekiwania" icon="clock" action="#/" :small="true" />
                <x-button onclick="expandReject(this)" optbc="nothing" label="Nic, rezygnuję" icon="8" action="#/" :small="true" />
            </div>
            <input type="hidden" name="optbc">
            <div id="opinion-inputs" class="flex down hidden">
                <x-input type="text" name="opinion_link" label="Podaj nowy link do nagrania" :value="$request->link" />
                <x-input type="TEXT" name="opinion_wishes" label="Podaj nowe życzenia" :value="$request->wishes" />
                <div class="priority" for="opinion_deadline">
                    <p>W trybie priorytetowym jestem w stanie wykonać zlecenie poza kolejnością; wiąże się to jednak z podwyższoną ceną.</p>
                    <div class="flex right center">
                        <x-input type="date" name="new-deadline-date" label="Nowy termin, do kiedy (włącznie) oddam pliki" :value="get_next_working_day()->format('Y-m-d')" :disabled="true" />
                        <x-input type="text" name="new-deadline-price" label="Nowa cena zlecenia" :value="as_pln(price_calc($request->price_code.'z', $request->client_id, true)['price'])" :disabled="true" />
                    </div>
                </div>
                <x-input for="opinion_link opinion_wishes opinion_nothing" type="TEXT" name="comment" label="Komentarz (opcjonalne)" />
                <x-button for="opinion_link opinion_wishes" action="submit" icon="6" name="new_status" value="6" label="Oddaj do ponownej wyceny" />
                <x-button for="opinion_nothing" action="submit" icon="8" name="new_status" value="8" label="Zrezygnuj z zapytania" />
                <x-button for="opinion_deadline" action="{{ route('request-final', ['id' => $request->id, 'status' => 9, 'with_priority' => true]) }}" icon="9" label="Zaakceptuj nową wycenę" :danger="true" />
            </div>
        </div>
        <div id="opinion-3" class="hidden">
            <h2>Na pewno? Termin realizacji też?</h2>
            @if ($request->delayed_payment)
            <p class="yellowed-out">To, że musisz zapłacić później, też?</p>
            @endif
            <div>
                <x-button label="Tak" icon="9" action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}" />
                <x-button label="Nie" icon="times" action="#/" />
            </div>
        </div>
        <script defer>
        function goToReject() {
            document.querySelector("#opinion-1 a.ghost").classList.remove("ghost");
            document.querySelector(`#opinion-1 a:first`).classList.add("ghost");

            document.querySelector("#opinion-2").classList.remove("hidden");
            document.querySelector("#opinion-3").classList.add("hidden");
        });

        function expandReject(btn) {
            let optbc = btn.dataset.optbc;
            document.querySelector("input[name='optbc']").val(optbc);

            document.querySelector("#opinion-2 a[optbc]").addClass("ghost");
            document.querySelector(`#opinion-2 a[optbc='${optbc}']`).removeClass("ghost");

            document.querySelector("#opinion-2 #opinion-inputs [for^='opinion_']").addClass("hidden");
            document.querySelector(`#opinion-2 #opinion-inputs [for~='opinion_${optbc}']`).removeClass("hidden");
            document.querySelector("#opinion-2 #opinion-inputs").removeClass("hidden");
            document.querySelector("#opinion-2 #opinion-submit").removeClass("hidden");
        });

        document.querySelector("#opinion-1 a:first").click(function(){
            document.querySelector("#opinion-1 a.ghost").removeClass("ghost");
            document.querySelector(`#opinion-1 a:last`).addClass("ghost");

            document.querySelector("#opinion-2").addClass("hidden");
            document.querySelector("#opinion-3").removeClass("hidden");
        });
        </script>
        @endif
    </div>
</x-shipyard.app.form>
@endsection
