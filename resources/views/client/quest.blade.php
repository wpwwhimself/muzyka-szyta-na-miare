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
    @case(14)
        Obecny etap prac został przez Ciebie przyjęty. Wkrótce dostarczę dalszą część materiałów.
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
    @case(21)
        W tym zleceniu została zgłoszona chęć wprowadzenia zmian. Wkrótce je zweryfikuję i wprowadzę odpowiednie poprawki.
        @break
    @case(26)
        Twoje zlecenie zostało przywrócone – w najbliższym czasie skontaktuję się z Tobą z nowymi plikami lub też zmianami w wycenie.
        @break
    @case(31)
        Wycena dla tego zlecenia musiała zostać zmieniona. Aby prace mogły postępować dalej, musisz je zaakceptować.
        @break
    @case(95)
        Potrzebuję dodatkowych informacji na temat tego zlecenia. Odpowiedz na moje pytania za pomocą przycisku poniżej.
        @break
    @case(96)
        Komentarz został przesłany. Odniosę się do niego i przygotuję coś nowego wkrótce.
        @break
@endswitch
</p>

<x-a :href="route('quests')" icon="angles-left">Wróć do listy</x-a>

@if (sumWarnings($warnings))
<h1 class="warning">
    <i class="fas fa-triangle-exclamation fa-fade"></i>
    Jest kilka rzeczy, z którymi musisz się koniecznie zapoznać!
</h1>
@endif

<x-phase-indicator :status-id="$quest->status_id" />

<div class="flex-down spaced">
    <x-extendo-block key="meta"
        header-icon="compact-disc"
        title="Szczegóły utworu"
        :subtitle="$quest->song->full_title"
        :extended="true"
    >
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
    </x-extendo-block>

    <x-extendo-block key="quote"
        header-icon="sack-dollar"
        title="Wycena"
        :subtitle="implode(' ', array_filter([
            'do zapłaty:',
            as_pln($quest->payment_remaining),
            $quest->delayed_payment_in_effect ? 'po '.$quest->delayed_payment->format('d.m.Y') : null,
            '//',
            'pliki do '.$quest->deadline?->format('d.m.Y'),
        ]))"
        :warning="$warnings['quote']"
        :extended="sumWarnings($warnings['quote'], true)"
    >
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
            $("#price_code").change(function (e) { calcPriceNow() });
        });
        </script>

        @if ($quest->deadline) <x-input type="date" name="deadline" label="Do kiedy (włącznie) oddam pliki" value="{{ $quest->deadline?->format('Y-m-d') }}" :disabled="true" /> @endif

        <x-extendo-section title="Wpłaty">
            <progress id="payments" value="{{ $quest->paid ? $quest->price : $quest->payments_sum }}" max="{{ $quest->price }}"></progress>
            @php arr_to_list(array_merge(
                ["Opłacono" => as_pln($quest->paid ? $quest->price : $quest->payments_sum)],
                !$quest->paid ? ["Pozostało" => as_pln($quest->price - $quest->payments_sum)] : [],
            )) @endphp
        </x-extendo-section>

        @unless ($quest->paid)
        <div class="tutorial">
            <p><i class="fa-solid fa-circle-question"></i> Opłaty projektu możesz dokonać na 2 sposoby:</p>
            <ul>
                <li>na numer konta <b>58 1090 1607 0000 0001 5333 1539</b><br>
                    (w tytule wpisz <i>{{ $quest->id }}</i>)</li>
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

        @if ($quest->hard_deadline) <x-input type="date" name="hard_deadline" label="Twój termin wykonania" value="{{ $quest->hard_deadline?->format('Y-m-d') }}" :disabled="true" /> @endif

        <x-extendo-section title="Faktury">
        @if (count($quest->visibleInvoices))
            @forelse($quest->visibleInvoices as $invoice)
            <x-button action="{{ route('invoice', ['id' => $invoice->id]) }}"
                icon="file-invoice" label="{{ $invoice->fullCode }}" :small="true"
                target="_blank"
                />
            @empty
            <p class="grayed-out">Brak</p>
            @endforelse
        @endif
        </x-extendo-section>
    </x-extendo-block>

    <x-extendo-block key="files"
        header-icon="file-waveform"
        title="Pliki"
        :extended="true"
        scissors
    >
        @forelse ($files as $ver_super => $ver_mains)
            @foreach ($ver_mains as $ver_main => $ver_subs)
            <x-extendo-section no-shrinking>
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
                <span class="ghost file-super">{{ $ver_super }}</span>
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
                            @elseif (in_array(pathinfo($file)['extension'], ["mp3", "ogg"]))
                            <x-file-player
                                :song-id="$quest->song->id"
                                :file="$file"
                                :type="pathinfo($file)['extension']"
                            />
                                @break
                            @elseif (pathinfo($file)['extension'] == "pdf")
                            <span class="ghost">Nie jestem w stanie<br>pokazać podglądu</span>
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
            </x-extendo-section>
            @endforeach
        @empty
        <p class="grayed-out">Brak plików</p>
        @if (in_array($quest->status_id, [19]))
        <p class="yellowed-out">
            Przywróć zlecenie przyciskiem poniżej<br>
            i poproś o ponowne wgranie
        </p>
        @endif
        @if ($quest->status_id == 15 && !$quest->files_ready)
        <p class="yellowed-out">
            To nie są jeszcze wszystkie pliki.<br>
            Dalsze prace po akceptacji tego etapu.
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

        <x-extendo-section title="Chmura">
        @if ($quest->has_files_on_external_drive)
            <span><i class="fas fa-cloud ghost"></i> W chmurze znajdują się pliki związane z tym zleceniem</span>
            <x-a :href="$quest->client->external_drive">Otwórz</x-a>
        @endif
        </x-extendo-section>
    </x-extendo-block>

    <x-quest-history :quest="$quest" />
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
            @if ($quest->history->get(1)->date->diffInDays() >= 30)
            <p class="error" style="text-align: left;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Ostatnia zmiana padła {{ $quest->history->get(1)->date->diffForHumans() }}.
                Zażądanie poprawek może wiązać się z dopłatą.
                Zobacz <a href="{{ route('prices') }}">cennik</a> po więcej informacji.
            </p>
            @endif
        @endif
        <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
        @if (in_array($quest->status_id, [11])) <x-button action="#/" statuschanger="21" icon="21" label="Poproś o zmiany" /> @endif
        @if (in_array($quest->status_id, [16, 21, 26, 96]))
        <x-button action="#/" statuschanger="{{ $quest->status_id }}" is-follow-up="1" icon="{{ $quest->status_id }}" label="Popraw ostatni komentarz" />
            @if ($quest->status_id == 21) <x-button action="#/" statuschanger="11" icon="11" label="Zrezygnuj ze zmian" /> @endif
        @endif
        @if (in_array($quest->status_id, [95])) <x-button action="#/" statuschanger="96" icon="96" label="Odpowiedz" /> @endif
        @if (in_array($quest->status_id, [15, 31, 95]))
            @if ($quest->files_ready)
            <x-button action="#/" statuschanger="19" icon="19" label="Zaakceptuj i zakończ"  />
            @else
            <x-button action="#/" statuschanger="14" icon="14" label="Zaakceptuj etap"  />
            @endif
        @endif
        @if (in_array($quest->status_id, [14, 15])) <x-button action="#/" statuschanger="16" icon="16" :label="$quest->files_ready ? 'Poproś o poprawki' : 'Poproś o poprawki w tym etapie'" /> @endif
        @if (!in_array($quest->status_id, [18, 19]))
            @if ($quest->completed_once)
                <x-button action="#/" statuschanger="19" icon="18" label="Zrezygnuj z dalszych zmian" />
            @else
                <x-button action="#/" statuschanger="18" icon="18" label="Zrezygnuj ze zlecenia" />
            @endif
        @endif
        @if (in_array($quest->status_id, [18, 19])) <x-button action="#/" statuschanger="26" icon="26" label="Przywróć zlecenie" /> @endif
    </div>
    <div id="statuschanger">
        {{-- @if (in_array($quest->status_id, [15, 18, 19, 95])) --}}
        <p class="tutorial">
            <i class="fa-solid fa-circle-question"></i>
            W historii zlecenia pojawi się Twój komentarz.
        </p>
        <div class="history-position p-18">
            <x-input type="TEXT" name="comment" label=""
                placeholder="Tutaj wpisz swój komentarz..."
                />
        </div>
        {{-- @endif --}}
        <x-button action="submit" name="status_id" icon="paper-plane" value="15" label="Wyślij" />
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
            for(i of [11, 14, 19, 16, 18, 21, 26, 96]){
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
</form>

@endsection
