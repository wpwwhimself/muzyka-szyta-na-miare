@extends('layouts.app', ["title" => ($quest->song->title ?? "bez tytułu")." | $quest->id"])

@section('content')
@foreach (["success", "error"] as $status)
@if (session($status))
    <x-alert :status="$status" />
@endif
@endforeach

<div class="input-container">
    <h1>Szczegóły zlecenia</h1>
    
    <x-phase-indicator :status-id="$quest->status_id" />
    
    @if ($quest->status_id == 12)
    <div id="stats">
        <div id="stats-buttons">
            @foreach ($stats_statuses as $option)
            <x-button
                label="{{ $option->status_name }}" icon="{{ $option->id }}"
                action="#"
                />
            @endforeach
            <x-button
                label="stop" icon="circle-pause" :danger="true"
                action="#"
                />
        </div>
        <section id="stats-log">
            <h2><i class="fa-solid fa-snowplow"></i> Historia tworzenia</h2>
            {{-- TODO historia tworzenia --}}
        </section>
    </div>
    @endif

    <div id="quest-box">
        <section class="input-group">
            <h2><i class="fa-solid fa-compact-disc"></i> Szczegóły utworu</h2>
            <x-input type="text" name="" label="ID utworu" value="{{ $quest->song->id }}" :disabled="true" :small="true" />
            <x-input type="text" name="" label="Tytuł" value="{{ $quest->song->title }}" :disabled="true" />
            <x-input type="text" name="" label="Wykonawca" value="{{ $quest->song->artist }}" :disabled="true" />
            <x-link-interpreter :raw="$quest->song->link" />
            <x-input type="text" name="genre_id" label="Gatunek" value="{{ $quest->song->genre->name }}" :disabled="true" :small="true" />
            <x-input type="TEXT" name="wishes" label="Życzenia" value="{{ $quest->song->notes }}" :disabled="true" />
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
                $("#price_code").change(function (e) { calcPriceNow() });
            });
            </script>
            <x-input type="checkbox" name="paid" label="Opłacono" value="{{ quest_paid($quest->id, $quest->price) }}" :disabled="true" />
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $quest->deadline }}" :disabled="true" />
            @if ($quest->hard_deadline)
            <x-input type="date" name="hard_deadline" label="Termin narzucony przez klienta" value="{{ $quest->hard_deadline }}" :disabled="true" />
            @endif
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-file-waveform"></i> Pliki</h2>
            <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data" class="file-adder">
                @csrf
                <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                <input type="file" name="files" label="Dodaj pliki" multiple />
                <x-button action="submit" label="Dodaj" icon="circle-plus" />
            </form>

            @forelse ($files as $ver_super => $ver_mains)
                @foreach ($ver_mains as $ver_main => $ver_subs)
                <div class="file-container-a">
                    <h3>{{ $ver_super }}-{{ $ver_main }}</h3>
                    @foreach ($ver_subs as $ver_sub => $ver_bots)
                    <div class="file-container-b">
                        <h4>
                            {{ $ver_sub }}
                            <small class="ghost">{{ date("Y-m-d H:i", $last_mod[$ver_main][$ver_sub]) }}</small>
                        </h4>
                        <div class="file-container-c">
                        @foreach ($ver_bots as $file)
                            @if (pathinfo($file)['extension'] == "mp4")
                            <video controls><source src="{{ route('safe-show', ["id" => $quest->id, "filename" => basename($file)]) }}" type="video/mpeg" /></video>
                            @elseif (pathinfo($file)['extension'] == "mp3")
                            <audio controls><source src="{{ route('safe-show', ["id" => $quest->id, "filename" => basename($file)]) }}" type="audio/mpeg" /></audio>
                            @endif
                        @endforeach
                        @if (quest_paid($quest->id, $quest->price)) @foreach ($ver_bots as $file)
                            <x-file-tile :id="$quest->id" :file="$file" />
                        @endforeach @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endforeach
            @empty
            <p class="grayed-out">Brak plików</p>
            @endforelse
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
                13: [12, 15, 32],
                15: [16, 18, 19, 32],
                16: [12, 18, 32],
                18: [32],
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
        });
        </script>
        <div class="flexright">
            @csrf
            <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu" />
            <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
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
            @if (!quest_paid($quest->id, $quest->price))
            <x-button action="submit" name="status_id" icon="32" value="32" label="Opłać" />
            @endif
        </div>
    </form>
</div>

@endsection
