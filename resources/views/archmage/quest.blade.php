@extends('layouts.app', [
    "title" => "[".$quest->song->id."] ".($quest->song->title ?? "bez tytułu")." | $quest->id"
])

@section('content')
<div class="input-container">
    <h1>Szczegóły zlecenia</h1>

    <x-phase-indicator :status-id="$quest->status_id" />

    <form action="{{ route('mod-quest-back') }}" method="POST" id="phases" class="archmage-quest-phases">
        <div class="flexright">
            @csrf
            <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu" />
            <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
            @foreach ([
                ["Rozpocznij", 12, [11, 13, 16, 26, 96]],
                ["Oddaj", 15, [11, 12, 13, 16, 26, 96]],
                ["Doprecyzuj", 95, [11, 12, 13, 16, 26, 96]],
                ["Klient odpowiada", 96, [95]],
                ["Zawieś", 13, [12, 16, 96]],
                ["Klient akceptuje", 19, [15, 96]],
                ["Klient cofa", 16, [15]],
                ["Klient odrzuca", 18, [11, 12, 13, 15, 16, 95, 96]],
                ["Kient przywraca", 26, [17, 18, 19]],
                ["Wygaś", 17, [13, 15]],
            ] as [$label, $status_id, $show_on_statuses])
                @if (in_array($quest->status_id, $show_on_statuses))
                @php $nomail = (!$quest->client->email && in_array($status_id, [15, 95])) @endphp
                <x-button action="submit"
                    name="status_id"
                    :icon="$status_id"
                    :value="$status_id"
                    :label="$label"
                    :class="$nomail ? 'warning' : ''"
                    /> @endif
            @endforeach
        </div>
    </form>

    <div id="quest-box" class="flex-right">
        <section class="input-group">
            <h2>
                <i class="fa-solid fa-compact-disc"></i>
                Utwór
                <a target="_blank" href="{{ route('songs', ['search' => $quest->song_id]) }}" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a>
            </h2>
            <form action="{{ route("quest-song-update") }}" method="post">
                @csrf
                <div id="quest-song-id">
                    <x-quest-type
                        :id="$quest->song->type->id ?? 0"
                        :label="$quest->song->type->type ?? 'nie zdefiniowano'"
                        :fa-symbol="$quest->song->type->fa_symbol ?? 'fa-circle-question'"
                        />
                    <x-input type="text" name="" label="ID utworu" value="{{ $quest->song->id }}" :disabled="true" :small="true" />
                    <input type="hidden" name="id" value="{{ $quest->song->id }}" />
                </div>
                <x-input type="text" name="title" label="Tytuł" value="{{ $quest->song->title }}" />
                <x-input type="text" name="artist" label="Wykonawca" value="{{ $quest->song->artist }}" />
                <x-input type="text" name="link" label="Linki" value="{{ $quest->song->link }}" :small="true" />
                <x-link-interpreter :raw="$quest->song->link" />
                <x-input type="text" name="genre" label="Gatunek" value="{{ $quest->song->genre->name }}" :small="true" :disabled="true" />
                <x-input type="TEXT" name="wishes" label="Życzenia dotyczące utworu" value="{{ $quest->song->notes }}" />
                <div class="flexright"><x-button label="Popraw utwór" icon="pen" action="submit" :small="true" /></div>
            </form>
            <form action="{{ route("quest-wishes-update") }}" method="post">
                @csrf
                <input type="hidden" name="id" value="{{ $quest->id }}"></input>
                <x-input type="TEXT" name="wishes_quest" label="Życzenia dotyczące zlecenia" value="{{ $quest->wishes }}" />
                <div class="flexright"><x-button label="Popraw zlecenie" icon="pen" action="submit" :small="true" /></div>
            </form>
            <h2>
                <i class="fa-solid fa-user"></i>
                Klient
                <a target="_blank" href="{{ route('clients', ['search' => $quest->client_id]) }}"><i class="fa-solid fa-up-right-from-square"></i></a>
                <a target="_blank" href="{{ route('quests', ['client' => $quest->client_id]) }}"><i class="fa-solid fa-boxes"></i></a>
            </h2>
            <x-input type="text" name="" label="Nazwisko" value="{{ _ct_($quest->client->client_name) }}" :disabled="true" />
            <x-input type="text" name="" label="Preferencja kontaktowa" value="{{ _ct_($quest->client->contact_preference) }}" :small="true" :disabled="true" />
            <x-input type="text" name="" label="Hasło do konta" value="{{ _ct_($quest->client->user->password) }}" :small="true" :disabled="true" />
            <x-input type="text" name="" label="Wybredność" value="{{ round($quest->client->pickiness * 100) }}%" :small="true" :disabled="true" class="{{ $quest->client->pickiness > 1 ? 'error' : 'success' }}" />
        </section>
        <section class="input-group">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>

            @if(in_array($quest->status_id, [16, 26]) && $quest->changes->get(1)->date->diffInDays() >= 30)
            <div class="error">
                <i class="fa-solid fa-stopwatch"></i>
                Ostatnia zmiana padła {{ $quest->changes->get(1)->date->diffForHumans() }}.<br>
                Uwzględnij to w wycenie.
            </div>
            @endif

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
                <progress id="payments" value="{{ $quest->paid ? $quest->price : $quest->payments->sum("comment") }}" max="{{ $quest->price }}"></progress>
                <label for="payments">
                    Opłacono: {{ _c_(as_pln($quest->paid ? $quest->price : $quest->payments->sum("comment"))) }}
                    @unless ($quest->paid)
                    •
                    Pozostało: {{ _c_(as_pln($quest->price - $quest->payments->sum("comment"))) }}
                    @endunless
                </label>
                <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $quest->deadline?->format('Y-m-d') }}" />
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
            @unless ($quest->paid)
            <form action="{{ route("mod-quest-back") }}" method="post" class="sc-line" id="quest-pay">
                @csrf
                <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                <x-button action="submit" name="status_id" icon="32" value="32" label="Opłać" :small="true" />
                <x-input type="number" name="comment" label="Kwota" step="0.01" :small="true" value="{{ $quest->price - $quest->payments->sum('comment') }}" />
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
                            {{ as_pln($invoice->quests->filter(fn($q) => $q->id == $quest->id)->first()->pivot->amount) }} / {{ as_pln($invoice->amount) }}
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
        </section>

        <div class="flex-down @if($quest->status_id == 12) first @endif">
            <section id="stats-log">
                <x-song-work-time-log :quest="$quest" />
            </section>

            <section class="input-group sc-line">
                <x-sc-scissors />
                <h2>
                    <i class="fa-solid fa-file-waveform"></i>
                    Pliki
                    @if ($quest->paid || can_download_files($quest->client_id, $quest->id))
                        <i class="success fa-solid fa-check" @popper(Uprawniony do pobierania)></i>
                    @elseif ($quest->client->can_see_files)
                        <i class="warning fa-solid fa-eye" @popper(Widzi podglądy)></i>
                    @else
                        <i class="error fa-solid fa-xmark" @popper(Klient nic nie widzi)></i>
                    @endif
                </h2>

                @unless(Auth::id() === 0)
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
                @endunless

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
                                action="#/" label="" icon="note-sticky"
                                value='{{ pathinfo($ver_bots[0], PATHINFO_FILENAME) }}'
                                />
                            <div class="ver_desc">
                                {{ isset($desc[$ver_super][$ver_main][$ver_sub]) ? Illuminate\Mail\Markdown::parse(Storage::get($desc[$ver_super][$ver_main][$ver_sub])) : "" }}
                            </div>
                            <div class="file-container-c">
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
                                $("#ver_desc_form textarea").text(res);
                            },
                            complete: function(){
                                $("#ver_desc_form textarea").focus();
                            }
                        });
                    });
                });
                </script>

                @if($quest->quest_type_letter == "P")
                <h2>
                    <i class="fa-solid fa-bullhorn"></i>
                    Podgląd dla ludu
                </h2>

                @if($quest->song->has_showcase_file)
                <audio controls><source src="{{ route('showcase-file-show', ['id' => $quest->song->id]) }}?{{ time() }}" type="audio/ogg" /></audio>
                @else
                <span class="grayed-out">Brak showcase'u</span>
                @endif

                <form id="showcase-file-form" method="post" action="{{ route('showcase-file-upload') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="showcase_file" />
                    <input type="hidden" name="id" value="{{ $quest->song_id }}" />
                    <x-button action="#/" id="showcase-file-button" :small="true"
                        :icon="$quest->song->has_showcase_file ? 'pencil' : 'plus'"
                        label="{{ $quest->song->has_showcase_file ? 'Podmień' : 'Dodaj' }} plik"
                        />
                    <script>
                    const button = $("#showcase-file-button");
                    const file_input = $("input[name='showcase_file']");
                    button.click(() => file_input.trigger("click"));
                    file_input.change(() => $("#showcase-file-form").submit());
                    </script>
                </form>
                @endif

            </section>
        </div>

        <section class="input-group @if($quest->status_id == 12) first @endif">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            <x-quest-history :quest="$quest" />
        </section>
    </div>
</div>

@endsection
