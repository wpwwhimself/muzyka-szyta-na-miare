@extends('layouts.app', ["title" => ($quest->song->title ?? "bez tytułu")." | $quest->id"])

@section('content')

<x-a :href="route('quests')" icon="angles-left">Wróć do listy</x-a>

@if (sumWarnings($warnings))
<h1 class="warning">
    <i class="fas fa-triangle-exclamation fa-fade"></i>
    Jest kilka rzeczy, z którymi musisz się koniecznie zapoznać!
</h1>
@endif

<x-phase-indicator :status-id="$quest->status_id" />

<div class="flex down">
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
        :extended="true"
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

        <x-extendo-section>
            @unless ($quest->paid)
            <x-tutorial>
                <p>Opłaty projektu możesz dokonać na 2 sposoby:</p>
                <ul>
                    <li>na numer konta <b>58 1090 1607 0000 0001 5333 1539</b><br>
                        (w tytule wpisz <i>{{ $quest->id }}</i>)</li>
                    <li>BLIKiem na numer telefonu <b>530 268 000</b>.</li>
                </ul>
                <p>
                    Jest ona potrzebna do pobierania plików,<br>
                    chyba, że jesteś np. stałym klientem
                </p>
            </x-tutorial>
            @if ($quest->delayed_payment)
            <x-warning>
                Z uwagi na limity przyjmowanych przeze mnie wpłat,
                <b>proszę o dokonanie wpłaty po {{ $quest->delayed_payment->format('d.m.Y') }}</b>.
                Po zaakceptowaniu zlecenia dostęp do plików
                zostanie przyznany automatycznie.
            </x-warning>
            @endif
            @endunless
        </x-extendo-section>

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
        @if (Auth::user()->notes->can_see_files)
        <x-files.list :grouped-files="$files" :can-download-files="can_download_files(Auth::id(), $quest->id)" />
        @endif

        @if ($quest->status_id == 15 && !$quest->files_ready)
        <p class="yellowed-out">
            To nie są jeszcze wszystkie pliki.<br>
            Dalsze prace po akceptacji tego etapu.
        </p>
        @endif

        @if (empty($files))
        <x-tutorial>
            Tutaj pojawią się pliki związane
            z przygotowywanym dla Ciebie zleceniem.
            Po dokonaniu wpłaty będzie możliwość
            ich pobrania lub odsłuchania.
        </x-tutorial>
            @if (in_array($quest->status_id, [19]))
            <p class="yellowed-out">
                Sejf został usunięty.<br>
                Przywróć zlecenie przyciskiem poniżej<br>
                i poproś o ponowne wgranie
            </p>
            @endif
        @endif

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
        <x-tutorial>
            Za pomocą poniższych przycisków możesz przyjąć zlecenie lub,
            jeśli coś Ci się nie podoba w przygotowanych przeze mnie materiałach, poprosić o przygotowanie poprawek.
            Instrukcje do tego celu możesz umieścić w oknie, które pojawi się po wybraniu jednej z poniższych opcji.
            Ta informacja będzie widoczna i na jej podstawie będę mógł wprowadzić poprawki.
        </x-tutorial>
        @elseif ($quest->status_id == 19)
        <x-tutorial>
            Zlecenie zostało przez Ciebie zamknięte, ale nadal możesz je przywrócić w celu wprowadzenia kolejnych zmian.
        </x-tutorial>
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
        <x-tutorial>
            W historii zlecenia pojawi się Twój komentarz.
        </x-tutorial>
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
