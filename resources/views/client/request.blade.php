@extends('layouts.app', ["title" => ($request->title ?? "bez tytułu") . " | $title"])

@section('content')
<p class="tutorial"><i class="fa-solid fa-circle-question"></i>
@switch($request->status_id)
    @case(1)
        Twoje zapytanie zostało wysłane. W&nbsp;najbliższym czasie (może nawet jutro) odniosę się do niego i&nbsp;przygotuję odpowiednią wycenę. Zostaniesz o&nbsp;tym poinformowany w&nbsp;wybrany przez Ciebie sposób.</p>
        @break
    @case(4)
        Nie podejmę się wykonania tego zlecenia. Prawdopodobnie jest ono dla mnie niewykonalne.
        @break
    @case(5)
        Wyceniłem Twoje zapytanie. Możesz potwierdzić przedstawione warunki lub – jeśli się z nimi nie zgadzasz – przesłać mi do ponownej wyceny z opisem, co się nie zgadza.
        Ostatecznie możesz zupełnie odrzucić warunki.
        @break
    @case(6)
        Twoje poprawki zostały przekazane. Odniosę się do nich i przedstawię poprawioną wycenę.
        @break
    @case(7)
        Termin ważności wyceny minął. Jeśli nadal chcesz zrealizować to zlecenie, kliknij przycisk poniżej.
        @break
    @case(8)
        Ta wycena została przez Ciebie odrzucona. Coś musiało pójść nie tak lub coś Ci się nie spodobało.
        @break
    @case(9)
        Zapytanie zostało przyjęte. Utworzyłem zlecenie, do którego link znajdziesz poniżej.
        @break
    @case(95)
        Potrzebuję dodatkowych informacji na temat tego zapytania. Odpowiedz na moje pytania za pomocą przycisku poniżej.
        @break
    @case(96)
        Odpowiedź została wysłana. Odniosę się do nich i przedstawię wycenę.
        @break
@endswitch
</p>

<form method="POST" action="{{ route("mod-request-back") }}">
    @csrf
    <h1>Szczegóły zapytania</h1>
    <x-phase-indicator :status-id="$request->status_id" />

    @if ($request->quest_id)
    <h2>
        Zlecenie przepisane z numerem {{ $request->quest_id }}
        <x-a href="{{ route('quest', ['id' => $request->quest_id]) }}">Przejdź do zlecenia</x-a>
    </h2>
    @endif

    <div id="quest-box" class="flex-right">
        <section class="input-group">
            <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
            <x-select name="quest_type" label="Rodzaj zlecenia" :small="true" :options="$questTypes" :required="true" value="{{ $request->quest_type_id }}" :disabled="true" />
            <x-input type="text" name="title" label="Tytuł utworu" value="{{ $request->title }}" :disabled="true" />
            <x-input type="text" name="artist" label="Wykonawca" value="{{ $request->artist }}" :disabled="true" />
            <x-input type="url" name="link" label="Link do nagrania" :small="true" value="{{ $request->link }}" :disabled="true" />
            <x-link-interpreter :raw="$request->link" />
            <x-input type="TEXT" name="wishes" label="Życzenia dot. koncepcji utworu (np. budowa, aranżacja)" value="{{ $request->wishes }}" :disabled="true" />
            <x-input type="TEXT" name="wishes_quest" label="Życzenia techniczne (np. liczba partii, transpozycja)" value="{{ $request->wishes_quest }}" :disabled="true" />
            @if ($request->hard_deadline)
            <x-input type="date" name="hard_deadline" label="Twój termin wykonania" value="{{ $request->hard_deadline?->format('Y-m-d') }}" :disabled="true" />
            @endif
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
            @if (!$request->price)
            <p class="yellowed-out"><i class="fa-solid fa-hourglass-half fa-fade"></i> pojawi się w ciągu najbliższych dni</p>
            @endif
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
            @if ($request->deadline)
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $request->deadline?->format('Y-m-d') }}" :disabled="true" />
            @endif
            @if ($request->price && $request->status_id == 5)
            <div class="tutorial">
                @if ($request->deadline)
                <p><i class="fa-solid fa-circle-question"></i> Termin oddania jest liczony do podanego dnia włącznie.</p>
                @endif
                <p><i class="fa-solid fa-circle-question"></i> Opłaty projektu możesz dokonać na 2 sposoby:</p>
                <ul>
                    <li>na numer konta <b>58 1090 1607 0000 0001 5333 1539</b><br>
                        (w tytule ID zlecenia, zostanie ono przyznane po akceptacji),</li>
                    <li>BLIKiem na numer telefonu <b>530 268 000</b>.</li>
                </ul>
                <p>
                    Jest ona potrzebna do pobierania plików,<br>
                    chyba, że jesteś np. stałym klientem
                </p>
                <p><i class="fa-solid fa-circle-question"></i> Pliki będą dostępne z poziomu tej strony internetowej.</p>
            </div>
            @if ($request->delayed_payment)
                <p class="yellowed-out">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Z uwagi na limity przyjmowanych przeze mnie wpłat z racji prowadzenia działalności nierejestrowanej,
                    <b>proszę o dokonanie wpłaty po {{ $request->delayed_payment->format('d.m.Y') }}</b>.
                    Po zaakceptowaniu zlecenia dostęp do plików (kiedy tylko się pojawią) zostanie przyznany automatycznie.
                </p>
            @endif
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            <x-quest-history :quest="$request" />
        </section>
    </div>
    @if ($request->status_id == 5)
    <p class="tutorial">
        <i class="fa-solid fa-circle-question"></i>
        Za pomocą poniższych przycisków możesz zaakceptować warunki zlecenia lub,
        jeśli coś Ci się nie podoba w przygotowanej przeze mnie wycenie, poprosić o wprowadzenie zmian.
        Instrukcje do tego celu możesz umieścić w oknie, które pojawi się po wybraniu jednej z poniższych opcji.
        Ta informacja będzie widoczna i na jej podstawie będę mógł wprowadzić poprawki.
    </p>
    @elseif (in_array($request->status_id, [4, 7, 8]))
    <p class="tutorial">
        <i class="fa-solid fa-circle-question"></i>
        Zapytanie zostało zamknięte, ale nadal możesz je przywrócić w celu ponownego złożenia zamówienia.
    </p>
    @endif
    <div id="phases">
        <div class="flexright">
            <input type="hidden" name="id" value="{{ $request->id }}" />
            <input type="hidden" name="intent" value="review" />
            @if (in_array($request->status_id, [95])) <x-button action="#/" statuschanger="96" icon="96" label="Odpowiedz" /> @endif
            @if (in_array($request->status_id, [5])) <x-button label="Potwierdź" statuschanger="9" icon="9" action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}" /> @endif
            @if (in_array($request->status_id, [5])) <x-button action="#/" statuschanger="6" icon="6" label="Poproś o ponowną wycenę" /> @endif
            @if (in_array($request->status_id, [5, 95])) <x-button action="#/" statuschanger="8" icon="8" label="Zrezygnuj ze zlecenia" /> @endif
            @if (in_array($request->status_id, [4, 7, 8])) <x-button action="#/" statuschanger="1" icon="1" label="Odnów" /> @endif
        </div>
        <div id="statuschanger">
            @if (in_array($request->status_id, [4, 5, 7, 8, 95]))
            <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu"
                placeholder="Tutaj wpisz swój komentarz..."
                />
            @endif
            <x-button action="submit" name="new_status" icon="paper-plane" value="5" label="Wyślij" :danger="true" />
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
            });
        });
        </script>
    </div>
</form>
@endsection
