@extends('layouts.app', [
    "title" => ($quest->song->title ?? "bez tytułu")." | $quest->id"
])

@section('content')

<x-phase-indicator :status-id="$quest->status_id" />

<form action="{{ route('mod-quest-back') }}" method="POST" id="phases" class="archmage-quest-phases">
    <div class="flexright">
        @csrf
        <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu" />
        <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
        @foreach ([
            ["Wprowadzenie/odrzucenie zmian", 11, [21]],
            ["Rozpocznij", 12, [11, 13, 14, 16, 26, 96]],
            ["Oddaj", 15, [11, 12, 13, 14, 16, 26, 96]],
            ["Doprecyzuj", 95, [11, 12, 13, 14, 16, 26, 96]],
            ["Klient odpowiada", 96, [95]],
            ["Zawieś", 13, [12, 14, 16, 96]],
            ["Klient akceptuje", $quest->files_ready ? 19 : 14, [15, 31, 96]],
            ["Klient cofa", 16, [15]],
            ["Klient odrzuca", 18, [11, 12, 13, 14, 15, 16, 31, 21, 95, 96]],
            ["Kient przywraca", 26, [17, 18, 19]],
            ["Kient prosi o zmiany", 21, [11]],
            ["Wygaś", 17, [13, 15]],
            ["Popraw ostatni komentarz", $quest->status_id, [$quest->status_id]],
        ] as [$label, $status_id, $show_on_statuses])
            @if (in_array($quest->status_id, $show_on_statuses))
            @php $nomail = (!$quest->client->email && in_array($status_id, [15, 95])) @endphp
            <x-button action="submit"
                name="status_id"
                :icon="$status_id"
                :value="$status_id"
                :label="''"
                :pop="$label"
                :class="$nomail ? 'warning' : ''"
                :small="$status_id == $quest->status_id"
                /> @endif
        @endforeach
    </div>
</form>

<div class="flex-down spaced">
    <x-extendo-block key="quest"
        header-icon="compact-disc"
        title="Utwór"
        :subtitle="$quest->song_id . ' // ' . $quest->song->full_title"
        :extended="in_array($quest->status_id, [11, 12])"
    >
        <x-extendo-section title="Rodzaj">
            <x-quest-type
                :id="$quest->song->type->id"
                :label="$quest->song->type->type"
                :fa-symbol="$quest->song->type->fa_symbol"
            />
        </x-extendo-section>
        <x-extendo-section title="Akcje">
            <x-a :href="route('songs', ['search' => $quest->song_id])">Szczegóły</x-a>
            <x-a :href="route('song-edit', ['id' => $quest->song_id])">Edytuj</x-a>
        </x-extendo-section>
        <x-input type="text" name="title" label="Tytuł" value="{{ $quest->song->title }}" />
        <x-input type="text" name="artist" label="Wykonawca" value="{{ $quest->song->artist }}" />
        <div>
            <x-input type="text" name="link" label="Linki" value="{{ $quest->song->link }}" :small="true" />
            <x-link-interpreter :raw="$quest->song->link" />
        </div>
        <x-input type="text" name="genre" label="Gatunek" value="{{ $quest->song->genre?->name }}" :small="true" :disabled="true" />
        <x-input type="TEXT" name="notes" label="Życzenia dotyczące utworu" value="{{ $quest->song->notes }}" />
        <x-input type="TEXT" name="wishes" label="Życzenia dotyczące zlecenia" value="{{ $quest->wishes }}" />

        <script>
        $(document).ready(() => {
            $("#title, #artist, #link, #genre, #notes").on("change", function(){
                $.ajax({
                    headers: {"Accept": "application/json", "Content-Type": "application/json"},
                    url: `/api/songs/{{ $quest->song->id }}/single`,
                    type: "PATCH",
                    data: JSON.stringify({key: $(this).attr("id"), value: $(this).val()}),
                })
            })
            $("#wishes").on("change", function(){
                $.ajax({
                    headers: {"Accept": "application/json", "Content-Type": "application/json"},
                    url: `/api/quests/{{ $quest->id }}/single`,
                    type: "PATCH",
                    data: JSON.stringify({key: $(this).attr("id"), value: $(this).val()}),
                })
            })
        })
        </script>
    </x-extendo-block>

    @if($quest->status_id == 12)
    <div class="grid-2">
        <x-song-work-time-log :quest="$quest" :extended="true" />
        <x-quest-history :quest="$quest" :extended="true" />
    </div>
    @elseif (in_array($quest->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
    <x-quest-history :quest="$quest" :extended="true" />
    @endif

    <x-extendo-block key="files"
        header-icon="file-waveform"
        title="Pliki"
        :extended="!in_array($quest->status_id, [11])"
        :warning="$warnings['files']"
        scissors
    >
        <x-extendo-section title="Wgrywanie">
            @unless(Auth::id() === 0)
            <x-a :href="route('files-upload-for-quest', ['quest_id' => $quest->id])" icon="plus" target="_blank">Wgraj</x-a>
            <x-a
                href="https://hydromancer.xaa.pl:2083/cpsess4257804942/frontend/paper_lantern/filemanager/upload-ajax.html?file=&fileop=&dir={{ storage_path() }}%2Fapp%2Fsafe%2F{{ $quest->song_id }}&dirop=&charset=&file_charset=&baseurl=&basedir="
                target="_blank"
            >
                Dodaj pliki ręcznie przez cPanel
            </x-a>
            <x-a :href="route('files-add-from-existing-safe', ['song_id' => $quest->song_id])" icon="recycle" target="_blank">Dodaj istniejące</x-a>
            @endunless

            <x-extendo-section title="Chmura">
                @if ($quest->client->external_drive)
                <x-a href="{{ $quest->client->external_drive }}" _target="blank">Link</x-a>
                <form action="{{ route('quest-files-external-update') }}" method="post" class="flex-right center">
                    @csrf
                    <input type="hidden" name="quest_id" value="{{ $quest->id }}" />

                    @if ($quest->has_files_on_external_drive)
                    <span><i class="fas fa-cloud success"></i> Posiada pliki</span>
                    <x-button action="submit" label="Zmień" icon="cloud-arrow-down" name="external" value="0" :small="true" />
                    @else
                    <span><i class="fas fa-cloud-bolt error"></i> Brak plików</span>
                    <x-button action="submit" label="Zmień" icon="cloud-arrow-up" name="external" value="1" :small="true" />
                    @endif
                </form>
                @endif
            </x-extendo-section>

            <x-extendo-section title="Widoczność">
                <span>
                    @if ($quest->paid || can_download_files($quest->client_id, $quest->id))
                        <i class="success fa-solid fa-check"></i> Uprawniony do pobierania
                    @elseif ($quest->client->can_see_files)
                        <i class="warning fa-solid fa-eye"></i> Widzi podglądy
                    @else
                        <i class="error fa-solid fa-xmark"></i> Nic nie widzi
                    @endif
                </span>
                @unless ($quest->files_ready)
                <form action="{{ route('quest-files-ready-update') }}" method="post" class="flex-right center">
                    @csrf
                    <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                    <x-button action="submit" label="Wszystko wgrane" icon="file-circle-check" name="ready" value="1" :small="true" />
                </form>
                @endunless
            </x-extendo-section>
        </x-extendo-section>

        <x-files.list :grouped-files="$files" :editable="true" />
    </x-extendo-block>

    <div class="grid-2">
        <x-extendo-block key="client"
            header-icon="user"
            title="Klient"
            :subtitle="implode(' // ', [
                $quest->client->id,
                $quest->client->client_name,
            ])"
        >
            <x-input type="text" name="" label="Nazwisko" value="{{ _ct_($quest->client->client_name) }}" :disabled="true" />
            <x-input type="text" name="" label="Preferencja kontaktowa" value="{{ _ct_($quest->client->contact_preference) }}" :small="true" :disabled="true" />
            <x-input type="text" name="" label="Hasło do konta" value="{{ _ct_($quest->client->user->password) }}" :small="true" :disabled="true" />
            <x-input type="text" name="" label="Wybredność" value="{{ round($quest->client->pickiness * 100) }}%" :small="true" :disabled="true" class="{{ $quest->client->pickiness > 1 ? 'error' : 'success' }}" />

            <div>
                <x-button action="{{ route('clients', ['search' => $quest->client_id]) }}" icon="user" label="Szczegóły" small />
                <x-button action="{{ route('quests', ['client' => $quest->client_id]) }}" icon="boxes" label="Zlecenia" small />
            </div>
        </x-extendo-block>

        <x-extendo-block key="quote"
            header-icon="sack-dollar"
            title="Wycena"
            :subtitle="implode(' // ', array_filter([
                _c_(as_pln($quest->price)),
                'do '.$quest->deadline->format('d.m.Y'),
                $quest->paid ? '🟢' : ($quest->payments_sum > 0 ? '🟡' : null)
            ], fn($val) => !is_null($val)))"
            :warning="$warnings['quote']"
            :extended="!$quest->paid"
        >
            <div>
                <div id="price-summary" class="hint-table">
                    <div class="positions"></div>
                    <hr />
                    <div class="summary"><span>Razem:</span><span>0 zł</span></div>
                </div>
                <script>
                function calcPriceNow(){
                    const labels = document.querySelector("#price_code_override").value;
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
                    $("#price_code_override").change(function (e) { calcPriceNow() });
                });
                </script>
            </div>

            <x-extendo-section title="Wpłaty">
                <progress id="payments" value="{{ $quest->paid ? $quest->price : $quest->payments_sum }}" max="{{ $quest->price }}"></progress>
                @php arr_to_list(array_merge(
                    ["Opłacono" => _c_(as_pln($quest->paid ? $quest->price : $quest->payments_sum))],
                    !$quest->paid ? ["Pozostało" => _c_(as_pln($quest->price - $quest->payments_sum))] : [],
                )) @endphp
                @unless ($quest->paid)
                <form action="{{ route("mod-quest-back") }}" method="post" id="quest-pay">
                    @csrf
                    <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                    <x-input type="number" name="comment" label="Kwota" step="0.01" :small="true" value="{{ $quest->price - $quest->payments_sum }}" />
                    <x-button action="submit" name="status_id" icon="32" value="32" label="Opłać" :small="true" />
                </form>
                @endunless
            </x-extendo-section>

            <form action="{{ route("quest-quote-update") }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $quest->id }}" />
                <x-input type="text" name="price_code_override" label="Kod wyceny" value="{{ $quest->price_code_override }}" :hint="$prices" />

                <x-input type="date" name="deadline" label="Do kiedy (włącznie) oddam pliki" value="{{ $quest->deadline?->format('Y-m-d') }}" />
                @if ($quest->hard_deadline)
                <x-input type="date" name="hard_deadline" label="Termin narzucony przez klienta" value="{{ $quest->hard_deadline?->format('Y-m-d') }}" :disabled="true" />
                @endif
                <x-input type="date" name="delayed_payment" label="Opóźnienie wpłaty" value="{{ $quest->delayed_payment?->format('Y-m-d') }}" />
                <div class="flexright"><x-button id="price-mod-trigger" label="Popraw wycenę" icon="pen" action="#/" :small="true" /></div>
                <div id="price-mod-box" style="display: none">
                    <x-input type="text" name="reason" label="Powód zmiany (Z uwagi na...)" :small="true" :required="true" />
                    <div class="flexright"><x-button label="Zatwierdź" icon="check" action="submit" :small="true" :danger="true" /></div>
                </div>
                <script>
                $(document).ready(() => {
                    $("#price-mod-trigger").click(() => {
                        $("#price-mod-trigger").hide();
                        $("#price-mod-box").show();
                        $("#reason").focus();
                    });
                });
                </script>
            </form>

            <x-extendo-section title="Dokumenty">
                <table>
                    <thead>
                        <tr>
                            <th>Numer</th>
                            <th>Kwota (zlec./całk.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quest->allInvoices as $invoice)
                        <tr>
                            <td>
                                <a href="{{ route('invoice', ['id' => $invoice->id]) }}">
                                    <i class="fa-solid fa-{{ $invoice->visible ? 'file-invoice' : 'eye-slash' }}"></i>
                                    {{ $invoice->fullCode }}
                                </a>
                            </td>
                            <td>
                                {{ _c_(as_pln($invoice->quests->filter(fn($q) => $q->id == $quest->id)->first()->pivot->amount)) }} / {{ _c_(as_pln($invoice->amount)) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan=2>
                                <span class="grayed-out">Brak</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <x-button action="{{ route('invoices') }}?fillfor={{ $quest->client_id }}&quest={{ $quest->id }}" name="" icon="plus" label="Dodaj" :small="true" />
            </x-extendo-section>

            <x-extendo-section title="Koszty">
                <table>
                    <thead>
                        <tr>
                            <th>Kategoria</th>
                            <th>Kwota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quest->song->costs as $cost)
                        <tr>
                            <td>{{ $cost->type->name }}</td>
                            <td>{{ _c_(as_pln($cost->amount)) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan=2><span class="grayed-out">Brak kosztów</span></td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Suma:</th>
                            <th>{{ _c_(as_pln($quest->song->costs?->sum("amount"))) }}</th>
                        </tr>
                    </tfoot>
                </table>
                <x-button action="{{ route('costs') }}" name="" icon="money-bill-wave" label="Koszty" :small="true" />
            </x-extendo-section>
        </x-extendo-block>
    </div>

    @unless (in_array($quest->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
    <x-quest-history :quest="$quest" :extended="true" />
    @endunless
</div>

@endsection
