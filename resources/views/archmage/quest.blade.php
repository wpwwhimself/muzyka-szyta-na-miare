@extends('layouts.app', ["title" => $quest->song->title." | $quest->id"])

@section('content')
@foreach (["success", "error"] as $status)
@if (session($status))
    <x-alert :status="$status" />
@endif
@endforeach

<div class="input-container">
    <h1>Szczegóły zlecenia</h1>
    
    <x-phase-indicator :status-id="$quest->status_id" />
    
    <div id="quest-box">
        <section class="input-group">
            <h2><i class="fa-solid fa-compact-disc"></i> Szczegóły utworu</h2>
            <x-input type="text" name="" label="ID utworu" value="{{ $quest->song->id }}" :disabled="true" :small="true" />
            <x-input type="text" name="" label="Tytuł" value="{{ $quest->song->title }}" :disabled="true" />
            <x-input type="text" name="" label="Wykonawca" value="{{ $quest->song->artist }}" :disabled="true" />
            <x-link-interpreter :raw="$quest->song->link" />
            <x-input type="TEXT" name="wishes" label="Życzenia" value="{{ $quest->song->notes }}" :disabled="true" />
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
            <x-input type="checkbox" name="paid" label="Opłacono" value="{{ $quest->paid }}" :disabled="true" />
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $quest->deadline }}" :disabled="true" />
            <x-input type="date" name="hard_deadline" label="Termin narzucony przez klienta" value="{{ $quest->hard_deadline }}" :disabled="true" />
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-file-waveform"></i> Pliki</h2>
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-comment"></i> Komunikacja</h2>
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            @forelse ($history as $item)
            {{ $item->date }}
            @empty
                
            @endforelse
        </section>
    </div>

    <div id="step-1" class="flexright">
        <button class="submit hover-lift">
            <i class="fa-solid fa-angles-right"></i> Oddaj do recenzji
        </button>
    </div>
</div>

@endsection
