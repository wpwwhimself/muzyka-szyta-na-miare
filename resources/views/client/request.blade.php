@extends('layouts.app', ["title" => "$request->title | $title"])

@section('content')
@foreach (["success", "error"] as $status)
@if (session($status))
    <x-alert :status="$status" />
@endif
@endforeach

<form method="POST" action="{{ route("mod-request-back") }}">
    @csrf
    <script>
    $(document).ready(function(){
        //disabling inputs if no change is allowed
        if([1, 6, 7, 8, 9].includes(parseInt($(".quest-phase").attr("status")))){
            $("button, .submit").hide();
            $("input, textarea, select").prop("disabled", true);
        };
    });
    </script>
    <h1>Szczegóły zapytania</h1>
    <x-phase-indicator :status-id="$request->status_id" />
    <div id="quest-box">
        <section class="input-group">
            <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
            <x-select name="quest_type" label="Rodzaj zlecenia" :small="true" :options="$questTypes" :required="true" value="{{ $request->quest_type_id }}" />
            <x-input type="text" name="title" label="Tytuł utworu" value="{{ $request->title }}" />
            <x-input type="text" name="artist" label="Wykonawca" value="{{ $request->artist }}" />
            <x-input type="url" name="link" label="Link do nagrania" :small="true" value="{{ $request->link }}" />
            <x-link-interpreter :raw="$request->link" />
            <x-input type="TEXT" name="wishes" label="Życzenia" value="{{ $request->wishes }}" />
            <x-input type="date" name="hard_deadline" label="Twój termin wykonania" value="{{ $request->hard_deadline }}" />
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
            @unless ($request->price)
            <p class="yellowed-out"><i class="fa-solid fa-hourglass-half fa-fade"></i> Wycena w toku</p>
            @endunless
            <div id="price-summary" class="hint-table">
                <div class="positions"></div>
                <hr />
                <div class="summary"><span>Razem:</span><span>0 zł</span></div>
            </div>
            <script>
            function calcPriceNow(){
                const labels = "{{ $request->price_code }}";
                const client_id = {!! $request->client_id ?? "\"\"" !!};
                const positions_list = $("#price-summary .positions");
                const sum_row = $("#price-summary .summary");
                if(labels == "") $("#price-summary").hide();
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
            });
            </script>
            @if ($request->deadline)
            <x-input type="date" name="deadline" label="Termin oddania pierwszej wersji" value="{{ $request->deadline }}" />
            @endif
        </section>

        <section class="input-group">
            <h2><i class="fa-solid fa-timeline"></i> Historia</h2>
            <x-quest-history :quest="$request" />
        </section>
    </div>
    <input type="hidden" name="modifying" value="{{ $request->id }}" />
    <input type="hidden" name="questioning" value="1" />
    <div class="flexright">
        <x-button
            label="Potwierdź" icon="9"
            action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}"
            />
        <x-button
            label="Popraw i zakwestionuj" icon="6"
            action="submit"
            />
        <x-button
            label="Odrzuć" icon="8" :danger="true"
            action="{{ route('request-final', ['id' => $request->id, 'status' => 8]) }}"
            />
    </div>
</form>
@endsection
