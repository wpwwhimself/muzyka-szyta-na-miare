@extends('layouts.app')
@section("title", $quest->song->full_title)
@section("subtitle", "Zlecenie")

@section('content')

<x-shipyard.app.form method="POST" :action="route('mod-quest-back')">
    <x-phase-indicator :status-id="$quest->status_id" />

    <div class="archmage-quest-phases flex right center middle">
        <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu" />
        <input type="hidden" name="quest_id" value="{{ $quest->id }}" />

        @foreach ([
            ["Wprowadzenie/odrzucenie zmian", 11, [21]],
            ["Rozpocznij", 12, [11, 13, 14, 16, 26, 96]],
            ["Oddaj", 15, [11, 12, 13, 14, 16, 26, 96]],
            ["Doprecyzuj", 95, [11, 12, 13, 14, 16, 26, 96]],
            ["Klient odpowiada", 96, [95]],
            ["Zawieś", 13, [11, 12, 14, 16, 96]],
            ["Klient akceptuje", $quest->files_ready ? 19 : 14, [15, 31, 96]],
            ["Klient cofa", 16, [15]],
            ["Klient odrzuca", 18, [11, 12, 13, 14, 15, 16, 31, 21, 95, 96]],
            ["Kient przywraca", 26, [17, 18, 19]],
            ["Kient prosi o zmiany", 21, [11]],
            ["Wygaś", 17, [13, 15]],
            ["Popraw ostatni komentarz", $quest->status_id, [$quest->status_id]],
        ] as [$label, $status_id, $show_on_statuses])
            @if (in_array($quest->status_id, $show_on_statuses))
            @php
            $nomail = (!$quest->user->notes->email && in_array($status_id, [15, 95]));
            $new_status = \App\Models\Status::find(abs($status_id));
            @endphp
            <x-shipyard.ui.button action="submit"
                name="status_id"
                :icon="$new_status->icon"
                :value="$status_id"
                :pop="$label"
                :class="$nomail ? 'warning' : ''"
            />
            @endif
        @endforeach
    </div>
</x-shipyard.app.form>

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-extendo-block key="quest"
        :header-icon="model_icon('users')"
        title="Utwór"
        :subtitle="$quest->song_id . ' // ' . $quest->song->full_title"
        :extended="in_array($quest->status_id, [11, 12])"
    >
        @php $song = $quest->song; @endphp

        <div class="flex right center middle">
            <x-quest-type :type="$song->type" />
            <x-a :href="route('songs', ['search' => $quest->song_id])">Szczegóły</x-a>
            <x-a :href="route('song-edit', ['id' => $quest->song_id])">Edytuj</x-a>
        </div>

        <x-shipyard.ui.connection-input :model="$song" connection-name="genre" />

        @foreach ([
            "title",
            "artist",
            "link",
            "notes",
        ] as $field_name)
            <x-shipyard.ui.field-input :model="$quest->song" :field-name="$field_name" />
            @if ($field_name == "link")
            <x-link-interpreter :raw="$quest->song->$field_name" />
            @endif
        @endforeach

        <x-input type="TEXT" name="wishes" label="Życzenia dotyczące zlecenia" value="{{ $quest->wishes }}" />

        <x-extendo-section title="Rolka">
            <div class="flex right middle">
                <x-input type="checkbox" name="has_recorded_reel" label="Nagrałem się" :value="$quest->song->has_recorded_reel" />
                <x-input type="checkbox" name="has_original_mv" label="Jest teledysk" :value="$quest->song->has_original_mv" />
            </div>
        </x-extendo-section>
    </x-extendo-block>

    @if($quest->status_id == 12)
    <div class="grid" style="--col-count: 2;">
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
            <x-a :href="route('files-upload-by-entity', ['entity_name' => 'quest', 'id' => $quest->id])" icon="plus" target="_blank" onclick="primeReload();">Wgraj</x-a>
            <x-a :href="route('files-add-from-existing-safe', ['song_id' => $quest->song_id])" icon="recycle" target="_blank" onclick="primeReload();">Dodaj istniejące</x-a>
            <script>
            function primeReload() {
                window.onfocus = function () { location.reload(true) }
            }
            </script>
            @endunless

            <x-extendo-section title="Chmura">
                @if ($quest->user->notes->external_drive)
                <x-a href="{{ $quest->user->notes->external_drive }}" _target="blank">Link</x-a>
                <form action="{{ route('quest-files-external-update') }}" method="post" class="flex right center">
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
                    @if (can_download_files($quest->client_id, $quest->id))
                        <i class="success fa-solid fa-check"></i> Uprawniony do pobierania
                    @elseif ($quest->user->notes->can_see_files)
                        <i class="warning fa-solid fa-eye"></i> Widzi podglądy
                    @else
                        <i class="error fa-solid fa-xmark"></i> Nic nie widzi
                    @endif
                </span>

                <form action="{{ route('quest-files-ready-update') }}" method="post" class="flex right center">
                    @csrf
                    <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                    <x-button action="submit" :label="!$quest->files_ready ? 'Wszystko wgrane' : 'Jednak nie'"
                        icon="file-circle-check" name="ready" value="{{ !$quest->files_ready }}" :small="true" />
                </form>
            </x-extendo-section>
        </x-extendo-section>

        <x-files.list :grouped-files="$files" :editable="true" :highlight-for-client-id="$quest->client_id" :can-download-files="true" />
    </x-extendo-block>

        <x-extendo-block key="client"
            header-icon="user"
            title="Klient"
            :subtitle="$quest->client"
        >
            <x-input type="text" name="" label="Nazwisko" value="{{ _ct_($quest->user->notes->client_name) }}" :disabled="true" />
            <x-input type="text" name="" label="Preferencja kontaktowa" value="{{ _ct_($quest->user->notes->contact_preference) }}" :small="true" :disabled="true" />
            <x-input type="text" name="" label="Hasło do konta" value="{{ _ct_($quest->user->notes->password) }}" :small="true" :disabled="true" />
            <x-input type="text" name="" label="Wybredność" value="{{ round($quest->user->notes->pickiness * 100) }}%" :small="true" :disabled="true" class="{{ $quest->user->notes->pickiness > 1 ? 'error' : 'success' }}" />

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
                'do '.$quest->deadline?->format('d.m.Y'),
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
                </script>
                <script defer>
                calcPriceNow();
                $("#price_code_override").change(function (e) { calcPriceNow() });
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

            {{-- <form action="{{ route("quest-quote-update") }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $quest->id }}" />
                <x-input type="text" name="price_code_override" label="Kod wyceny" value="{{ $quest->price_code_override }}" :hint="$prices" />

                <x-input type="date" name="deadline" label="Do kiedy (włącznie) oddam pliki" value="{{ $quest->deadline?->format('Y-m-d') }}" />
                @if ($quest->hard_deadline)
                <x-input type="date" name="hard_deadline" label="Termin narzucony przez klienta" value="{{ $quest->hard_deadline?->format('Y-m-d') }}" :disabled="true" />
                @endif
                <x-input type="date" name="delayed_payment" label="Opóźnienie wpłaty" value="{{ $quest->delayed_payment?->format('Y-m-d') }}" />
                <div class="flexright"><x-button id="price-mod-trigger" label="Popraw wycenę" icon="pen" action="none" :small="true" /></div>
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
            </form> --}}

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

    @unless (in_array($quest->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
    <x-quest-history :quest="$quest" :extended="true" />
    @endunless
</div>

@endsection
