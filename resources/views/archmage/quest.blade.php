@extends('layouts.app', [
    "title" => ($quest->song->title ?? "bez tytułu")." | $quest->id"
])

@section('content')
<div class="input-container">
    <h1>Szczegóły zlecenia</h1>

    <x-phase-indicator :status-id="$quest->status_id" />

    @if ($quest->status_id == 12)
    <div id="stats">
        <form method="POST" action="{{ route("work-clock") }}" id="stats-buttons" class="flex-right">
            @csrf
            <input type="hidden" name="song_id" value="{{ $quest->song_id }}" />
            @foreach ($stats_statuses as $option)
            <x-button
                label="{{ $option->status_name }}" icon="{{ $option->id }}"
                action="submit" value="{{ $option->id }}" name="status_id"
                />
            @endforeach
            <x-button
                label="stop" icon="circle-pause" :danger="true"
                action="submit" value="13" name="status_id"
                />
        </form>
        <section id="stats-log">
            <h2><i class="fa-solid fa-snowplow"></i> Log tworzenia</h2>
            <table>
                <thead>
                    <tr>
                        <th>Etap</th>
                        <th>Czas</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($workhistory as $entry)
                    <tr>
                        <td>
                            {{ DB::table("statuses")->find($entry->status_id)->status_symbol }}
                            {{ DB::table("statuses")->find($entry->status_id)->status_name }}
                            @if ($entry->now_working)
                            <i class="fa-solid fa-gear fa-spin" @popper(zegar tyka)></i>
                            @endif
                        </td>
                        <td>{{ $entry->time_spent }}</td>
                    </tr>
                @empty
                <tr>
                    <td colspan=2 class="grayed-out">
                        Prace jeszcze nie zaczęte
                    </td>
                </tr>
                @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th>Razem</th>
                        <th>
                        {{ gmdate("H:i:s", DB::table("song_work_times")
                                ->where("song_id", $quest->song_id)
                                ->sum(DB::raw("TIME_TO_SEC(time_spent)"))) }}
                        </th>
                    </tr>
                </tfoot>
                </tbody>
            </table>
        </section>
    </div>
    @endif

    <div id="quest-box" class="flex-right">
        <section class="input-group">
            <h2>
                <i class="fa-solid fa-compact-disc"></i>
                Utwór
                <a href="{{ route('songs') }}#song{{ $quest->song_id }}" target="_blank"><i class="fa-solid fa-up-right-from-square"></i></a>
            </h2>
            <x-input type="text" name="" label="Rodzaj zlecenia" value="{{ song_quest_type($quest->song_id)->type }}" :disabled="true" :small="true" />
            <x-input type="text" name="" label="Tytuł" value="{{ $quest->song->title }}" :disabled="true" />
            <x-input type="text" name="" label="Wykonawca" value="{{ $quest->song->artist }}" :disabled="true" />
            <x-link-interpreter :raw="$quest->song->link" />
            <x-input type="TEXT" name="wishes" label="Życzenia" value="{{ $quest->song->notes }}" :disabled="true" />

            <h2>
                <i class="fa-solid fa-user"></i>
                Klient
                <a href="{{ route('clients') }}#client{{ $quest->client_id }}"><i class="fa-solid fa-up-right-from-square"></i></a>
            </h2>
            <x-input type="text" name="" label="Nazwisko" value="{{ $quest->client->client_name }}" :disabled="true" />
            <x-input type="text" name="" label="Preferencja kontaktowa" value="{{ $quest->client->contact_preference }}" :small="true" :disabled="true" />
        </section>
        <section class="input-group">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
            <x-input type="text" name="price_code_override" label="Kod wyceny" value="{{ $quest->price_code_override }}" :hint="$prices" />
            <script>
            $(document).ready(function(){
                $("#price_code_override").change(function(){
                    $.ajax({
                        url: "{{ url('quest_price_update') }}",
                        type: "post",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: '{{ $quest->id }}',
                            code: $("#price_code_override").val()
                        },
                        success: function(){
                            location.reload();
                        }
                    })
                });
            });
            </script>
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
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $quest->deadline }}" :disabled="true" />
            @if ($quest->hard_deadline)
            <x-input type="date" name="hard_deadline" label="Termin narzucony przez klienta" value="{{ $quest->hard_deadline }}" :disabled="true" />
            @endif
        </section>

        <section class="input-group sc-line">
            <x-sc-scissors />
            <h2>
                <i class="fa-solid fa-file-waveform"></i>
                Pliki
                @if ($quest->paid || can_see_files($quest->client_id))
                    <i class="success fa-solid fa-check" @popper(Uprawniony do pobierania)></i>
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
                    url: '{{ route('upload', ["id" => $quest->id]) }}',
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
                        <x-button
                            action="#ver_desc_form" label="" icon="note-sticky"
                            value='{{ pathinfo($ver_bots[0], PATHINFO_FILENAME) }}'
                            />
                        <div class="ver_desc">
                            {{ isset($desc[$ver_main][$ver_sub]) ? Illuminate\Mail\Markdown::parse(Storage::get($desc[$ver_main][$ver_sub])) : "" }}
                        </div>
                        <div class="file-container-c">
                        @foreach ($ver_bots as $file)
                            @if (pathinfo($file)['extension'] == "mp4")
                            <video controls><source src="{{ route('safe-show', ["id" => $quest->id, "filename" => basename($file)]) }}" type="video/mpeg" /></video>
                            @elseif (pathinfo($file)['extension'] == "mp3")
                            <audio controls><source src="{{ route('safe-show', ["id" => $quest->id, "filename" => basename($file)]) }}" type="audio/mpeg" /></audio>
                            @endif
                        @endforeach
                        @foreach ($ver_bots as $file)
                            @unless (pathinfo($file, PATHINFO_EXTENSION) == "md")
                            <x-file-tile :id="$quest->id" :file="$file" />
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
                    $("#ver_desc_form input[name=ver]").val("{{ $quest->id }}/" + ver);
                    $.ajax({
                        url: "{{ url('get_ver_desc') }}",
                        type: "get",
                        data: {
                            _token: '{{ csrf_token() }}',
                            path: '/safe/{{ $quest->id }}/' + ver + '.md'
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
        <script>
        $(document).ready(function(){
            $('#phases button').hide();
            const whatCanBeSeen = {
                11: [12, 32],
                12: [13, 15, 32],
                13: [12, 15, 18, 17, 32],
                15: [16, 18, 19, 17, 32],
                16: [12, 18, 32],
                17: [26],
                18: [26],
                19: [26, 32],
                26: [12, 32]
            }
            if(whatCanBeSeen[{{ $quest->status_id }}].length == 0){
                $(`textarea[name='comment']`).parent().hide();
            }
            for(button of whatCanBeSeen[{{ $quest->status_id }}]){
                $(`button[value=${button}]`).show();
            }
            $('button[value={{ $quest->status_id }}]').hide();
            if(![15].includes({{ $quest->status_id }})){
                $("#mail-mod-prev").hide();
            }
            if({{ intval($quest->paid) }} == 0){
                $("#mail-paid-prev").hide();
            }
        });
        </script>
        <div class="flexright">
            @csrf
            <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu" />
            <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
            <x-button
                label="Podgląd maila o zmianie" icon="comment-dots" id="mail-mod-prev"
                action="{{ route('mp-q', ['id' => $quest->id]) }}" target="_blank"
                :small="true"
                />
            @if (App::environment() != "dev")
            <x-button action="submit" name="status_id" icon="11" value="11" label="Zuruck" />
            @endif
            <x-button action="submit" name="status_id" icon="12" value="12" label="Rozpocznij prace" />
            <x-button action="submit" name="status_id" icon="13" value="13" label="Zawieś prace" />
            <x-button action="submit" name="status_id" icon="15" value="15" label="Oddaj do recenzji" />
            <x-button action="submit" name="status_id" icon="16" value="16" label="Recenzja negatywna" />
            <x-button action="submit" name="status_id" icon="18" value="18" label="Odrzuć" :danger="true" />
            <x-button action="submit" name="status_id" icon="19" value="19" label="Zaakceptuj"  />
            <x-button action="submit" name="status_id" icon="26" value="26" label="Powróć" />
            <x-button action="submit" name="status_id" icon="17" value="17" label="Wygaś" />
            @if (!$quest->paid)
            <x-button action="submit" name="status_id" icon="32" value="32" label="Opłać" />
            @endif
            <x-button
                label="Podgląd maila o płatności" icon="comment-dollar" id="mail-paid-prev"
                action="{{ route('mp-q-p', ['id' => $quest->id]) }}" target="_blank"
                :small="true"
                />
        </div>
    </form>
</div>

@endsection
