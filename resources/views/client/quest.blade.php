@extends('layouts.app', ["title" => ($quest->song->title ?? "bez tytułu")." | $quest->id"])

@section('content')
<p class="tutorial"><i class="fa-solid fa-circle-question"></i>
@switch($quest->status_id)
    @case(11)
        Twoje zlecenie zostało przyjęte. Wkrótce rozpocznę nad nim pracę.
        @break
    @case(12)
        Dobre wyczucie, właśnie prowadzę prace nad Twoim zleceniem. W ciągu kolejnych godzin możesz spodziewać się wiadomości na temat postępów.
        @break
    @case(13)
        Prace nad zleceniem zostały zawieszone. Nadal mogę do niego wrócić, ale na razie leży odłożony i czeka na swój czas.
        @break
    @case(15)
        Do Twojego zlecenia zostały dodane nowe pliki. Poniżej możesz je przeglądać i wyrazić swoją opinię na ich temat.
        @break
    @case(16)
        Twoje uwagi zostały przekazane. Odniosę się do nich i przygotuję coś nowego wkrótce.
        @break
    @case(17)
        Twoje zlecenie wygasło z powodu zbyt powolnych postępów.
        @break
    @case(18)
        Zlecenie zostało przez Ciebie odrzucone. Coś musiało pójść nie tak lub coś Ci się nie spodobało.
        @break
    @case(19)
        Zlecenie zostało przez Ciebie przyjęte bez zarzutów. Cieszę się, że mogłem coś dla Ciebie przygotować i polecam się do dalszych zleceń.
        @break
    @case(26)
        Twoje zlecenie zostało przywrócone – w najbliższym czasie skontaktuję się z Tobą z nowymi plikami lub też zmianami w wycenie.
        @break
    @case(95)
        Potrzebuję dodatkowych informacji na temat tego zlecenia. Odpowiedz na moje pytania za pomocą przycisku poniżej.
        @break
    @case(96)
        Komentarz został przesłany. Odniosę się do niego i przygotuję coś nowego wkrótce.
        @break
@endswitch
</p>

<div class="input-container">
    <h1>Szczegóły zlecenia</h1>

    <x-phase-indicator :status-id="$quest->status_id" />

    <div id="quest-box" class="flex-right">
        <section class="input-group">
            <h2><i class="fa-solid fa-compact-disc"></i> Szczegóły utworu</h2>
            <x-input type="text" name="" label="Rodzaj zlecenia" value="{{ $quest->song->type->type }}" :disabled="true" :small="true" />
            <x-input type="text" name="" label="Tytuł" value="{{ $quest->song->title }}" :disabled="true" />
            <x-input type="text" name="" label="Wykonawca" value="{{ $quest->song->artist }}" :disabled="true" />
            <x-link-interpreter :raw="$quest->song->link" />
            @if ($quest->song->notes)
            <x-input type="TEXT" name="wishes" label="Życzenia dot. koncepcji utworu (np. budowa, aranżacja)" value="{{ $quest->song->notes }}" :disabled="true" />
            @endif
            @if ($quest->wishes)
            <x-input type="TEXT" name="wishes_quest" label="Życzenia techniczne (np. liczba partii, transpozycja)" value="{{ $quest->wishes }}" :disabled="true" />
            @endif
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
                $("#price_code").change(function (e) { calcPriceNow() });
            });
            </script>
            <progress id="payments" value="{{ $quest->payments->sum("comment") }}" max="{{ $quest->price }}"></progress>
            <label for="payments">
                Opłacono: {{ as_pln($quest->payments->sum("comment")) }}
                @unless ($quest->paid)
                •
                Pozostało: {{ as_pln($quest->price - $quest->payments->sum("comment")) }}
                @endunless
            </label>
            @unless ($quest->paid)
            <div class="tutorial">
                <p><i class="fa-solid fa-circle-question"></i> Opłaty projektu możesz dokonać na 2 sposoby:</p>
                <ul>
                    <li>na numer konta <b>58 1090 1607 0000 0001 5333 1539</b><br>
                        (w tytule ID zlecenia, tj. <i>{{ $quest->id }}</i>),</li>
                    <li>BLIKiem na numer telefonu <b>530 268 000</b>.</li>
                </ul>
                <p>
                    Jest ona potrzebna do pobierania plików,<br>
                    chyba, że jesteś np. stałym klientem
                </p>
            </div>
            @if ($quest->delayed_payment)
            <p class="yellowed-out">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Z uwagi na limity przyjmowanych przeze mnie wpłat,<br>
                <b>proszę o dokonanie wpłaty po {{ $quest->delayed_payment->format('d.m.Y') }}</b>.<br>
                Po zaakceptowaniu zlecenia dostęp do plików<br>
                zostanie przyznany automatycznie.
            </p>
            @endif
            @endunless
            @if ($quest->deadline)
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $quest->deadline?->format('Y-m-d') }}" :disabled="true" />
            @endif
            @if ($quest->hard_deadline)
            <x-input type="date" name="hard_deadline" label="Twój termin wykonania" value="{{ $quest->hard_deadline?->format('Y-m-d') }}" :disabled="true" />
            @endif

            @if (count($quest->visibleInvoices))
                <h2>
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    Dokumenty
                </h2>
                @forelse($quest->visibleInvoices as $invoice)
                <x-button action="{{ route('invoice', ['id' => $invoice->id]) }}"
                    icon="file-invoice" label="{{ $invoice->fullCode }}" :small="true"
                    />
                @empty
                <p class="grayed-out">Brak</p>
                @endforelse
            @endif
        </section>

        <section class="input-group sc-line">
            <x-sc-scissors />
            <h2><i class="fa-solid fa-file-waveform"></i> Pliki</h2>

            @forelse ($files as $ver_super => $ver_mains)
                <h3 class="pre-file-container-a">{{ $ver_super }}</h3>
                @foreach ($ver_mains as $ver_main => $ver_subs)
                @php
                    $ids = [];
                    preg_match("/^\d{1,3}/", $ver_main, $ids);
                @endphp
                @if(($ids[0] ?? Auth::id()) == Auth::id())
                <div class="file-container-a">
                    <h4>
                        <small>wariant:</small>
                        {{ $ver_main }}
                    </h4>
                    @foreach ($ver_subs as $ver_sub => $ver_bots)
                    @php list($ver_sub_name, $tags) = file_name_and_tags($ver_sub); @endphp
                    <div class="file-container-b">
                        <h5>
                            @foreach ($tags as $tag) <x-file-tag :tag="$tag" /> @endforeach
                            {{ $ver_sub_name }}
                            <small class="ghost" {{ Popper::pop($last_mod[$ver_main][$ver_sub]) }}>
                                {{ $last_mod[$ver_main][$ver_sub]->diffForHumans() }}
                            </small>
                        </h5>
                        <div class="ver_desc">
                            {{ isset($desc[$ver_super][$ver_main][$ver_sub]) ? Illuminate\Mail\Markdown::parse(Storage::get($desc[$ver_super][$ver_main][$ver_sub])) : "" }}
                        </div>
                        <div class="file-container-c">
                        @if ($quest->paid || $quest->client->can_see_files)
                            @php usort($ver_bots, "file_order") @endphp
                            @foreach ($ver_bots as $file)
                                @if (pathinfo($file)['extension'] == "mp4")
                                <video controls><source src="{{ route('safe-show', ["id" => $quest->song->id, "filename" => basename($file)]) }}" /></video>
                                    @break
                                @elseif (pathinfo($file)['extension'] == "mp3")
                                <audio controls><source src="{{ route('safe-show', ["id" => $quest->song->id, "filename" => basename($file)]) }}" type="audio/mpeg" /></audio>
                                    @break
                                @elseif (pathinfo($file)['extension'] == "ogg")
                                <audio controls><source src="{{ route('safe-show', ["id" => $quest->song->id, "filename" => basename($file)]) }}" type="audio/ogg" /></audio>
                                    @break
                                @endif
                            @endforeach
                            @if ($quest->paid || can_download_files($quest->client_id, $quest->id))
                                @foreach ($ver_bots as $file)
                                    @unless (pathinfo($file, PATHINFO_EXTENSION) == "md")
                                    <x-file-tile :id="$quest->song->id" :file="$file" />
                                    @endunless
                                @endforeach
                            @endif
                        @else
                            <p class="grayed-out">Opłać zlecenie, aby otrzymać dostęp</p>
                        @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                @endforeach
            @empty
            <p class="grayed-out">Brak plików</p>
            @if (in_array($quest->status_id, [19]))
            <p class="yellowed-out">
                Przywróć zlecenie przyciskiem poniżej<br>
                i poproś o ponowne wgranie
            </p>
            @endif
            <p class="tutorial">
                <i class="fa-solid fa-circle-question"></i>
                Tutaj pojawią się pliki związane<br>
                z przygotowywanym dla Ciebie zleceniem.<br>
                Po dokonaniu wpłaty będzie możliwość<br>
                ich pobrania lub odsłuchania.
            </p>
            @endforelse
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            <x-quest-history :quest="$quest" />
        </section>
    </div>

    <form action="{{ route('mod-quest-back') }}" method="POST" id="phases">
        @csrf
        <div class="flexright">
            @if (in_array($quest->status_id, [15]))
            <p class="tutorial">
                <i class="fa-solid fa-circle-question"></i>
                Za pomocą poniższych przycisków możesz przyjąć zlecenie lub,
                jeśli coś Ci się nie podoba w przygotowanych przeze mnie materiałach, poprosić o przygotowanie poprawek.
                Instrukcje do tego celu możesz umieścić w oknie, które pojawi się po wybraniu jednej z poniższych opcji.
                Ta informacja będzie widoczna i na jej podstawie będę mógł wprowadzić poprawki.
            </p>
            @elseif ($quest->status_id == 19)
            <p class="tutorial">
                <i class="fa-solid fa-circle-question"></i>
                Zlecenie zostało przez Ciebie zamknięte, ale nadal możesz je przywrócić w celu wprowadzenia kolejnych zmian.
            </p>
                @if ($quest->changes->get(1)->date->diffInDays() >= 30)
                <p class="error" style="text-align: left;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Ostatnia zmiana padła {{ $quest->changes->get(1)->date->diffForHumans() }}.
                    Zażądanie poprawek może wiązać się z dopłatą.
                    Zobacz <a href="{{ route('prices') }}">cennik</a> po więcej informacji.
                </p>
                @endif
            @endif
            <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
            @if (in_array($quest->status_id, [16, 26, 96]))
            <x-button action="#/" statuschanger="{{ $quest->status_id }}" is-follow-up="1" icon="{{ $quest->status_id }}" label="Popraw ostatni komentarz" />
            @endif
            @if (in_array($quest->status_id, [95])) <x-button action="#/" statuschanger="96" icon="96" label="Odpowiedz" /> @endif
            @if (in_array($quest->status_id, [15, 95])) <x-button action="#/" statuschanger="19" icon="19" label="Zaakceptuj i zakończ"  /> @endif
            @if (in_array($quest->status_id, [15])) <x-button action="#/" statuschanger="16" icon="16" label="Poproś o poprawki" /> @endif
            @if (!in_array($quest->status_id, [18, 19])) <x-button action="#/" statuschanger="18" icon="18" label="Zrezygnuj ze zlecenia" /> @endif
            @if (in_array($quest->status_id, [18, 19])) <x-button action="#/" statuschanger="26" icon="26" label="Przywróć zlecenie" /> @endif
        </div>
        <div id="statuschanger">
            {{-- @if (in_array($quest->status_id, [15, 18, 19, 95])) --}}
            <p class="tutorial">
                <i class="fa-solid fa-circle-question"></i>
                W historii zlecenia pojawi się wpis podobny do tego poniżej. Możesz teraz dopisać dodatkowy komentarz.
            </p>
            <div class="history-position p-18">
                <span>
                    <span class="client-name ghost">{{ $quest->client->client_name }}</span>
                    <br>
                    <i class="fa-solid fa-pencil"></i> Zmiana statusu zlecenia
                    <x-input type="TEXT" name="comment" label=""
                        placeholder="Tutaj wpisz swój komentarz..."
                        />
                </span>
                <span>{!! str_replace(" ", "<br>", \Carbon\Carbon::now()->format("Y-m-d XX:XX:XX")) !!}</span>
            </div>
            {{-- @endif --}}
            <x-button action="submit" name="status_id" icon="paper-plane" value="15" label="Wyślij" :danger="true" />
        </div>
        <script>
        $(document).ready(function(){
            $("#statuschanger").hide();

            $("a[statuschanger]").click(function(){
                /*wyczyść możliwe ghosty*/
                $("a[statuschanger].ghost").removeClass("ghost");

                let status = $(this).attr("statuschanger");
                $(`#phases button[type="submit"]`).val(status);
                $("#statuschanger").show();
                for(i of [19, 16, 18, 26, 96]){
                    if(i == status) continue;
                    $(`a[statuschanger="${i}"]`).addClass("ghost");
                }

                $("#statuschanger .history-position").removeClass((index, className) => className.match(/p-\d*/).join(" ")).addClass("p-"+status);

                const comment_field = document.querySelector("#statuschanger #comment");
                if($(this).attr("is-follow-up")){
                    const last_comment = $(`#quest-history .history-position.p-${status}:first .qh-comment`).text().trim();
                    comment_field.innerHTML = last_comment;
                }else{
                    comment_field.innerHTML = "";
                }
                comment_field.scrollIntoView({behavior: "smooth"});
                comment_field.focus();
            });
        });
        </script>
    </form>
</div>

@endsection
