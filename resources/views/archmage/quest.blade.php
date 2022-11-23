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
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            <x-quest-history :quest="$quest" />
        </section>
    </div>

    <form action="{{ route('mod-quest-back') }}" method="POST">
        <script>
        $(document).ready(function(){
            $('button[value={{ $quest->status_id }}]').hide();
        });
        </script>
        <div class="flexright">
            @csrf
            <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu" />
            <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
            <x-button action="submit" name="status_id" icon="11" value="11" label="Zuruck" />
            <x-button action="submit" name="status_id" icon="12" value="12" label="Rozpocznij prace" />
            <x-button action="submit" name="status_id" icon="15" value="15" label="Oddaj do recenzji" />
            @if (!quest_paid($quest->id, $quest->price))
            <x-button action="submit" name="status_id" icon="32" value="32" label="Opłać" :danger="true" />
            @endif
        </div>
    </form>
</div>

@endsection
