@extends('layouts.app', [
    "title" => ($quest->song->title ?? "bez tytułu")." | $quest->id"
])

@section('content')
<div class="input-container">
    <h1>Szczegóły zlecenia</h1>

    <x-phase-indicator :status-id="$quest->status_id" />

    <div id="quest-box" class="flex-right">
        <section class="input-group">
            <h2>
                <i class="fa-solid fa-compact-disc"></i>
                Utwór
                <a href="{{ route('songs') }}#song{{ $quest->song_id }}" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a>
            </h2>
            <form action="{{ route("quest-song-update") }}" method="post">
                @csrf
                <div id="quest-song-id">
                    <x-quest-type
                        :id="song_quest_type($quest->song_id)->id ?? 0"
                        :label="song_quest_type($quest->song_id)->type ?? 'nie zdefiniowano'"
                        :fa-symbol="song_quest_type($quest->song_id)->fa_symbol ?? 'fa-circle-question'"
                        />
                    <x-input type="text" name="" label="ID utworu" value="{{ $quest->song->id }}" :disabled="true" :small="true" />
                    <input type="hidden" name="id" value="{{ $quest->song->id }}" />
                </div>
                <x-input type="text" name="title" label="Tytuł" value="{{ $quest->song->title }}" />
                <x-input type="text" name="artist" label="Wykonawca" value="{{ $quest->song->artist }}" />
                <x-input type="text" name="link" label="Linki" value="{{ $quest->song->link }}" :small="true" />
                <x-link-interpreter :raw="$quest->song->link" />
                <x-input type="text" name="genre" label="Gatunek" value="{{ $quest->song->genre->name }}" :small="true" :disabled="true" />
                <x-input type="TEXT" name="wishes" label="Życzenia dot. koncepcji utworu (np. budowa, aranżacja)" value="{{ $quest->song->notes }}" />
                <div class="flexright"><x-button label="Popraw utwór" icon="pen" action="submit" :small="true" /></div>
            </form>
            <form action="{{ route("quest-wishes-update") }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $quest->id }}"></input>
                <x-input type="TEXT" name="wishes_quest" label="Życzenia techniczne (np. liczba partii, transpozycja)" value="{{ $quest->wishes }}" />
                <div class="flexright"><x-button label="Popraw zlecenie" icon="pen" action="submit" :small="true" /></div>
            </form>
            <h2>
                <i class="fa-solid fa-user"></i>
                Klient
                <a href="{{ route('clients') }}#client{{ $quest->client_id }}"><i class="fa-solid fa-up-right-from-square"></i></a>
                <a href="{{ route('quests', ['client_id' => $quest->client_id]) }}"><i class="fa-solid fa-boxes"></i></a>
            </h2>
            <x-input type="text" name="" label="Nazwisko" value="{{ $quest->client->client_name }}" :disabled="true" />
            <x-input type="text" name="" label="Preferencja kontaktowa" value="{{ $quest->client->contact_preference }}" :small="true" :disabled="true" />
        </section>
        <section class="input-group">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
            <form action="{{ route("quest-quote-update") }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $quest->id }}" />
                <x-input type="text" name="price_code_override" label="Kod wyceny" value="{{ $quest->price_code_override }}" :hint="$prices" />
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
                    $("#price_code_override").change(function (e) { calcPriceNow() });
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
                <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $quest->deadline?->format('Y-m-d') }}" />
                @if ($quest->hard_deadline)
                <x-input type="date" name="hard_deadline" label="Termin narzucony przez klienta" value="{{ $quest->hard_deadline?->format('Y-m-d') }}" :disabled="true" />
                @endif
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
                    });
                });
                </script>
            </form>
            @unless ($quest->paid)
            <form action="{{ route("mod-quest-back") }}" method="post" class="sc-line" id="quest-pay">
                @csrf
                <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                <x-button action="submit" name="status_id" icon="32" value="32" label="Opłać" :small="true" />
                <x-input type="number" name="comment" label="Kwota" :small="true" value="{{ $quest->price - $quest->payments->sum('comment') }}" />
            </form>
            @endunless

            <h2>
                <i class="fa-solid fa-file-invoice-dollar"></i>
                Dokumenty
            </h2>
            <table>
                <thead>
                    <tr>
                        <th>Numer</th>
                        <th>Kwota</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quest->allInvoices as $invoice)
                    <tr>
                        <td>
                            <a href="{{ route('invoice', ['id' => $invoice->id]) }}">
                                <i class="fa-solid fa-{{ $invoice->visible ? 'file-invoice' : 'eye-slash' }}"></i>
                                {{ $invoice->fullCode() }}
                            </a>
                        </td>
                        <td>
                            {{ number_format($invoice->amount, 2, ",", " ") }} zł
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
            <form action="{{ route('invoice-add') }}" method="post">
                @csrf
                <x-button action="#/" id="new_invoice_button" label="Nowy" icon="plus" :small="true" />
                <div id="payer_details" class="sc-line">
                    <x-input type="text" name="payer_name" value="{{ $quest->client->client_name }}" label="Nazwa płatnika" />
                    <x-input type="text" name="payer_title" value="" label="Tytuł płatnika" :small="true" />
                    <x-input type="TEXT" name="payer_address" value="" label="Adres" />
                    <x-input type="text" name="payer_nip" value="" label="NIP" :small="true" />
                    <x-input type="text" name="payer_regon" value="" label="REGON" :small="true" />
                    <x-input type="text" name="payer_email" value="{{ $quest->client->email }}" label="E-mail" :small="true" />
                    <x-input type="text" name="payer_phone" value="{{ $quest->client->phone }}" label="Telefon" :small="true" />
                    <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                    <x-button action="submit" label="Dodaj" icon="check" :small="true" />
                </div>
                <script>
                $(document).ready(() => {
                    $("#payer_details").hide();
                    $("#new_invoice_button").click(() => {
                        $("#payer_details").show();
                        $("#new_invoice_button").hide();
                    });
                });
                </script>
            </form>
        </section>

        <section id="stats-log">
            <h2><i class="fa-solid fa-snowplow"></i> Log tworzenia</h2>
            <table>
                <thead>
                    <tr>
                        <th>Etap</th>
                        <th colspan="2">Czas</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($workhistory as $entry)
                    <tr @if($entry->now_working) class="active" @endif>
                        <td>
                            {{ DB::table("statuses")->find($entry->status_id)->status_symbol }}
                            {{ DB::table("statuses")->find($entry->status_id)->status_name }}
                        </td>
                        <td>
                            <a class="log-delete" href="{{ route('work-clock-remove', ['status_id' => $entry->status_id, 'song_id' => $entry->song_id]) }}">
                                <i class="fa-solid fa-trash" @popper(usuń wpis)></i>
                            </a>
                        </td>
                        <td>
                            @if ($entry->now_working) <i class="fa-solid fa-gear fa-spin" @popper(zegar tyka)></i> @endif
                            {{ $entry->time_spent }}
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan=3 class="grayed-out">
                        Prace jeszcze nie zaczęte
                    </td>
                </tr>
                @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th>Razem</th>
                        <th colspan="2">
                        {{ gmdate("H:i:s", DB::table("song_work_times")
                                ->where("song_id", $quest->song_id)
                                ->sum(DB::raw("TIME_TO_SEC(time_spent)"))) }}
                        </th>
                    </tr>
                </tfoot>
                </tbody>
            </table>

            @if ($quest->status_id == 12)
            <form method="POST" action="{{ route("work-clock") }}" id="stats-buttons" class="flex-right">
                @csrf
                <input type="hidden" name="song_id" value="{{ $quest->song_id }}" />
                @foreach ($stats_statuses as $option)
                <x-button
                    label="{{-- $option->status_name --}}" icon="{{ $option->id }}"
                    action="submit" value="{{ $option->id }}" name="status_id"
                    :small="true" :pop="$option->status_name"
                    />
                @endforeach
                <x-button
                    label="stop" icon="circle-pause"
                    action="submit" value="13" name="status_id"
                    :small="true"
                    />
            </form>
            @endif
        </section>

        <section class="input-group sc-line">
            <x-sc-scissors />
            <h2>
                <i class="fa-solid fa-file-waveform"></i>
                Pliki
                @if ($quest->paid || can_download_files($quest->client_id))
                    <i class="success fa-solid fa-check" @popper(Uprawniony do pobierania)></i>
                @elseif (can_see_files($quest->client_id))
                    <i class="warning fa-solid fa-eye" @popper(Widzi podglądy)></i>
                @else
                    <i class="error fa-solid fa-xmark" @popper(Klient nic nie widzi)></i>
                @endif
            </h2>

            {{-- dropzone css --}}
            <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />

            <form class="" method="POST" action="{{ route('store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="col-md-12">
                        <div class="position-relative form-group">
                            <div class="needsclick dropzone" id="document-dropzone"></div>
                        </div>
                    </div>
                </div>
            </form>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
            <script>
                let uploadedDocumentMap = {};
                Dropzone.autoDiscover = false;
                let myDropzone = new Dropzone("div#document-dropzone",{
                    url: '{{ route('upload', ["id" => $quest->song_id]) }}',
                    autoProcessQueue: true,
                    uploadMultiple: true,
                    addRemoveLinks: true,
                    parallelUploads: 10,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    successmultiple: function(data, response) {
                        $.each(response['name'], function (key, val) {
                            $('form').append('<input type="hidden" name="images[]" value="' + val + '">');
                            uploadedDocumentMap[data[key].name] = val;
                        });
                    },
                    removedfile: function (file) {
                        file.previewElement.remove()
                        let name = '';
                        if (typeof file.file_name !== 'undefined') {
                            name = file.file_name;
                        } else {
                            name = uploadedDocumentMap[file.name];
                        }
                        $('form').find('input[name="images[]"][value="' + name + '"]').remove()
                    }
                });
            </script>
            {{-- dropzone end --}}

            {{-- fallback wgrywania plików przez cPanel --}}
            <a
                href="https://hydromancer.xaa.pl:2083/cpsess4257804942/frontend/paper_lantern/filemanager/upload-ajax.html?file=&fileop=&dir={{ storage_path() }}%2Fapp%2Fsafe%2F{{ $quest->song_id }}&dirop=&charset=&file_charset=&baseurl=&basedir="
                target="_blank"
                >
                Dodaj pliki ręcznie przez cPanel<br>
            </a>

            @forelse ($files as $ver_super => $ver_mains)
                @if (count($files) > 1)
                <h3 class="pre-file-container-a">{{ $ver_super }}</h3>
                @endif
                @foreach ($ver_mains as $ver_main => $ver_subs)
                <div class="file-container-a">
                    <h4>
                        <small>wariant:</small>
                        {{ $ver_main }}
                    </h4>
                    @foreach ($ver_subs as $ver_sub => $ver_bots)
                    <div class="file-container-b">
                        <h5>
                            {{ $ver_sub }}
                            <small class="ghost" {{ Popper::pop($last_mod[$ver_main][$ver_sub]) }}>
                                {{ $last_mod[$ver_main][$ver_sub]->diffForHumans() }}
                            </small>
                        </h5>
                        <x-button
                            action="#ver_desc_form" label="" icon="note-sticky"
                            value='{{ pathinfo($ver_bots[0], PATHINFO_FILENAME) }}'
                            />
                        <div class="ver_desc">
                            {{ isset($desc[$ver_super][$ver_main][$ver_sub]) ? Illuminate\Mail\Markdown::parse(Storage::get($desc[$ver_super][$ver_main][$ver_sub])) : "" }}
                        </div>
                        <div class="file-container-c">
                        @foreach ($ver_bots as $file)
                            @if (pathinfo($file)['extension'] == "mp4")
                            <video controls><source src="{{ route('safe-show', ["id" => $quest->song->id, "filename" => basename($file)]) }}" type="video/mpeg" /></video>
                            @elseif (pathinfo($file)['extension'] == "mp3")
                            <audio controls><source src="{{ route('safe-show', ["id" => $quest->song->id, "filename" => basename($file)]) }}" type="audio/mpeg" /></audio>
                            @endif
                        @endforeach
                        @foreach ($ver_bots as $file)
                            @unless (pathinfo($file, PATHINFO_EXTENSION) == "md")
                            <x-file-tile :id="$quest->song->id" :file="$file" />
                            @endunless
                        @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            @empty
            <p class="grayed-out">Brak plików</p>
            @endforelse

            <form id="ver_desc_form" action="{{ route('ver-desc-mod') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="ver" value="0" />
                <x-input type="TEXT" name="desc" label="Opis wersji XXX" />
                <x-button action="submit" label="Popraw opis" icon="pen-to-square" />
            </form>

            <script>
            $(document).ready(function(){
                $(".file-container-b .submit").click(function(){
                    const ver = $(this).attr("value");
                    $("#ver_desc_form").show();
                    $("#ver_desc_form label").text("Opis wersji " + ver);
                    $("#ver_desc_form input[name=ver]").val("{{ $quest->song->id }}/" + ver);
                    $.ajax({
                        url: "{{ url('get_ver_desc') }}",
                        type: "get",
                        data: {
                            _token: '{{ csrf_token() }}',
                            path: '/safe/{{ $quest->song->id }}/' + ver + '.md'
                        },
                        success: function(res){
                            $("#ver_desc_form textarea").text(res).focus();
                        }
                    });
                });
            });
            </script>
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            <x-quest-history :quest="$quest" />
        </section>
    </div>

    <form action="{{ route('mod-quest-back') }}" method="POST" id="phases">
        <div class="flexright">
            @csrf
            <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu" />
            <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
            @if (in_array($quest->status_id, [11, 13, 16, 26])) <x-button action="submit" name="status_id" icon="12" value="12" label="Rozpocznij prace" /> @endif
            @if (in_array($quest->status_id, [12, 16])) <x-button action="submit" name="status_id" icon="13" value="13" label="Zawieś prace" /> @endif
            @if (in_array($quest->status_id, [12, 13, 16, 26])) <x-button action="submit" name="status_id" icon="15" value="15" label="Oddaj do recenzji" /> @endif
            @if (in_array($quest->status_id, [15])) <x-button action="submit" name="status_id" icon="16" value="16" label="Klient cofa" /> @endif
            @if (in_array($quest->status_id, [11, 12, 13, 15, 16])) <x-button action="submit" name="status_id" icon="18" value="18" label="Klient odrzuca" :danger="true" /> @endif
            @if (in_array($quest->status_id, [15])) <x-button action="submit" name="status_id" icon="19" value="19" label="Klient akceptuje"  /> @endif
            @if (in_array($quest->status_id, [17, 18, 19])) <x-button action="submit" name="status_id" icon="26" value="26" label="Klient przywraca" /> @endif
            @if (in_array($quest->status_id, [13, 15])) <x-button action="submit" name="status_id" icon="17" value="17" label="Wygaś" /> @endif
        </div>
        <div class="flexright">
            @if ($quest->status_id != 15)
            <x-button
                label="Podgląd maila o zmianie" icon="comment-dots" id="mail-mod-prev"
                action="{{ route('mp-q', ['id' => $quest->id]) }}" target="_blank"
                :small="true"
                />
            @endif
            @if ($quest->paid)
            <x-button
                label="Podgląd maila o płatności" icon="comment-dollar" id="mail-paid-prev"
                action="{{ route('mp-q-p', ['id' => $quest->id]) }}" target="_blank"
                :small="true"
                />
            @endif
        </div>
    </form>
</div>

@endsection
