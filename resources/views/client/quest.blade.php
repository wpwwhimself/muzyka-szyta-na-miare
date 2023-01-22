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
@endswitch
</p>

<div class="input-container">
    <h1>Szczegóły zlecenia</h1>

    <x-phase-indicator :status-id="$quest->status_id" />

    <div id="quest-box" class="flex-right">
        <section class="input-group">
            <h2><i class="fa-solid fa-compact-disc"></i> Szczegóły utworu</h2>
            <x-input type="text" name="" label="Rodzaj zlecenia" value="{{ song_quest_type($quest->song_id)->type }}" :disabled="true" :small="true" />
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
            <progress id="payments" value="{{ $quest->payments->sum("comment") }}" max="{{ $quest->price }}"></progress>
            <label for="payments">
                Opłacono: {{ $quest->payments->sum("comment") }} zł
                @unless ($quest->paid)
                •
                Pozostało: {{ $quest->price - $quest->payments->sum("comment") }} zł
                @endunless
            </label>
            @unless ($quest->paid)
            <div class="tutorial">
                <p><i class="fa-solid fa-circle-question"></i> Opłaty projektu możesz dokonać na 2 sposoby:</p>
                <ul>
                    <li>na numer konta <b>53 1090 1607 0000 0001 1633 2919</b><br>
                        (w tytule ID zlecenia, tj. <i>{{ $quest->id }}</i>),</li>
                    <li>BLIKiem na numer telefonu <b>530 268 000</b>.</li>
                </ul>
                <p>
                    Jest ona potrzebna do pobierania plików,<br>
                    chyba, że jesteś np. stałym klientem
                </p>
            </div>
            @endunless
            @if ($quest->deadline)
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $quest->deadline?->format('Y-m-d') }}" :disabled="true" />
            @endif
            @if ($quest->hard_deadline)
            <x-input type="date" name="hard_deadline" label="Twój termin wykonania" value="{{ $quest->hard_deadline?->format('Y-m-d') }}" :disabled="true" />
            @endif
        </section>

        <section class="input-group sc-line">
            <x-sc-scissors />
            <h2><i class="fa-solid fa-file-waveform"></i> Pliki</h2>

            @forelse ($files as $ver_super => $ver_mains)
                @foreach ($ver_mains as $ver_main => $ver_subs)
                <div class="file-container-a">
                    <h3>{{ $ver_super }}={{ $ver_main }}</h3>
                    @foreach ($ver_subs as $ver_sub => $ver_bots)
                    <div class="file-container-b">
                        <h4>
                            {{ $ver_sub }}
                            <small class="ghost">{{ date("Y-m-d H:i", $last_mod[$ver_main][$ver_sub]) }}</small>
                        </h4>
                        <div class="ver_desc">
                            {{ isset($desc[$ver_main][$ver_sub]) ? Illuminate\Mail\Markdown::parse(Storage::get($desc[$ver_main][$ver_sub])) : "" }}
                        </div>
                        <div class="file-container-c">
                        @if ($quest->paid || can_see_files($quest->client_id))
                            @foreach ($ver_bots as $file)
                                @if (pathinfo($file)['extension'] == "mp4")
                                <video controls><source src="{{ route('safe-show', ["id" => $quest->id, "filename" => basename($file)]) }}" type="video/mpeg" /></video>
                                @elseif (pathinfo($file)['extension'] == "mp3")
                                <audio controls><source src="{{ route('safe-show', ["id" => $quest->id, "filename" => basename($file)]) }}" type="audio/mpeg" /></audio>
                                @endif
                            @endforeach
                            @if (can_download_files($quest->client_id))
                                @foreach ($ver_bots as $file)
                                    @unless (pathinfo($file, PATHINFO_EXTENSION) == "md")
                                    <x-file-tile :id="$quest->id" :file="$file" />
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
                @endforeach
            @empty
            <p class="grayed-out">Brak plików</p>
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
                Miej jednak na uwadze, że jeśli zmiany będą duże lub długo po terminie, mogę zmienić wycenę zlecenia.
            </p>
            @endif
            <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
            @if (in_array($quest->status_id, [15])) <x-button action="#phases" statuschanger="19" icon="19" label="Zaakceptuj"  /> @endif
            @if (in_array($quest->status_id, [15])) <x-button action="#phases" statuschanger="16" icon="16" label="Poproś o poprawki" /> @endif
            @if (in_array($quest->status_id, [11, 12, 13, 15])) <x-button action="#phases" statuschanger="18" icon="18" label="Zrezygnuj ze zlecenia" /> @endif
            @if (in_array($quest->status_id, [18, 19])) <x-button action="#phases" statuschanger="26" icon="26" label="Przywróć zlecenie" /> @endif
        </div>
        <div id="statuschanger">
            @if (in_array($quest->status_id, [15, 18, 19]))
            <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu"
                placeholder="Tutaj wpisz swój komentarz..."
                />
            @endif
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
                for(i of [19, 16, 18, 26]){
                    if(i == status) continue;
                    $(`a[statuschanger="${i}"]`).addClass("ghost");
                }
            });
        });
        </script>
    </form>
</div>

@endsection
