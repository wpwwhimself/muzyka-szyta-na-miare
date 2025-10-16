@extends('layouts.app')
@section("title", ($request->title ?? "bez tytułu"))
@section("subtitle", "Zapytanie")

@section('content')

@php
$fields = $request::getFields();
@endphp

<x-shipyard.app.form method="POST" :action="route('mod-request-back')">
    <x-slot:actions>
        <x-a href="{{ route('add-request', [
            'client' => $request->client_id,
            'client_new' => implode('*', [
                $request->client_name,
                $request->email,
                $request->phone,
                $request->other_medium,
                $request->contact_preference,
            ])
        ]) }}" icon="plus">Dodaj kolejne</x-a>
    </x-slot:actions>

    <x-phase-indicator :status-id="$request->status_id" />

    <div id="phases" class="archmage-quest-phases flex right center middle">
        @if ($request->status_id != 9) <x-input type="TEXT" name="comment" label="Komentarz do zmiany" /> @endif
        <input type="hidden" name="id" value="{{ $request->id }}" />
        <input type="hidden" name="intent" value="{{ in_array($request->status_id, [4, 5, 7, 8, 95]) ? 'review' : 'change' }}" />

        @foreach ([
            ["Uzupełnij", 1, [1]],
            ["Oddaj", 5, [1, 6, 96]],
            ["Doprecyzuj", 95, [1, 6, 96]],
            ["Klient odpowiada", 96, [95]],
            ["Odmów", 4, [1, 6, 96]],
            ["Klient przyjmuje", 9, [5]],
            ["Klient przyjmuje pilnie", -9, [5]],
            ["Klient chce poprawki", 6, [5]],
            ["Klient odrzuca", 8, [5, 95]],
            ["Klient odnawia", 1, [4, 7, 8]],
        ] as [$label, $status_id, $show_on_statuses])
            @if (in_array($request->status_id, $show_on_statuses))
            @php
            $new_status = \App\Models\Status::find(abs($status_id));
            @endphp
            <x-button :action="abs($status_id) == 9 ? route('request-final', ['id' => $request->id, 'status' => 9, 'with_priority' => $status_id < 0]) : 'submit'"
                name="new_status"
                :icon="$new_status->icon"
                :value="$status_id"
                :pop="$label"
                :class="$status_id < 0 ? 'priority' : ''"
                />
            @endif
        @endforeach
    </div>

    @if ($request->quest_id)
    <h3>Zlecenie przepisane z numerem <a href="{{ $request->quest->link_to }}">{{ $request->quest_id }}</a></h3>
    @endif

    <div class="grid but-mobile-down" style="--col-count: 2;">
        <x-extendo-block key="client"
            :header-icon="model_icon('users')"
            title="Dane klienta"
            :subtitle="$request->client_name"
            :extended="in_array($request->status_id, [1])"
        >
            <span>Powiązanie z klientem: {{ $request->user ?? "brak" }}</span>
            <div class="flex right middle">
                @if ($request->client_id)
                <x-button
                    :action="route('clients', ['search' => $request->client_id])"
                    :icon="model_icon('users')"
                    label="Szczegóły"
                />
                <x-button
                    :action="route('quests', ['client' => $request->client_id])"
                    :icon="model_icon('quests')"
                    label="Zlecenia"
                />

                @else
                <x-shipyard.ui.button
                    icon="link"
                    label="Przypisz klienta"
                    action="none"
                    onclick="openModal('select-user-to-request', {
                        request_id: '{{ $request->id }}',
                        query: '{{ $request->client_name }}',
                    })"
                    class="tertiary"
                />

                @endif
            </div>

            @foreach ([
                "client_name",
                "email",
                "phone",
                "other_medium",
            ] as $field_name)
            <x-shipyard.ui.field-input :model="$request" :field-name="$field_name" />
            @endforeach
        </x-extendo-block>

        @if (in_array($request->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
        <x-quest-history :quest="$request" :extended="true" />
        @endif

        <x-extendo-block key="song"
            :header-icon="model_icon('songs')"
            title="Dane utworu"
            :subtitle="$request->full_title"
            :extended="in_array($request->status_id, [1, 6, 96])"
            :warning="$warnings['song']"
        >
            <span>Powiązanie z utworem: {{ $request->song ?? "brak" }}</span>
            <div class="flex right middle">
                @if ($request->song_id)
                <x-button
                    :action="route('songs', ['search' => $request->song_id])"
                    :icon="model_icon('songs')"
                    label="Szczegóły"
                />

                @else
                <x-shipyard.ui.button
                    icon="link"
                    label="Przypisz utwór"
                    action="none"
                    onclick="openModal('select-song-to-request', {
                        request_id: '{{ $request->id }}',
                        query: '{{ $request->title }}',
                    })"
                    class="tertiary"
                />

                @endif
            </div>

            @foreach ([
                "title",
                "artist",
                "link",
                "wishes",
                "wishes_quest",
                "hard_deadline",
            ] as $field_name)
                <x-shipyard.ui.field-input :model="$request" :field-name="$field_name" />
                @if ($field_name == "link")
                <x-link-interpreter :raw="$request->$field_name" />
                @endif
            @endforeach
            <x-shipyard.ui.connection-input :model="$request" connection-name="genre" />
        </x-extendo-block>

        <x-extendo-block key="quote"
            :header-icon="model_icon('prices')"
            title="Wycena"
            :extended="true"
        >
            <x-shipyard.ui.connection-input :model="$request" connection-name="quest_type" />

            <div>
                <div id="song-price-sugg"></div>
                <div id="special-prices-warning"></div>
                <x-input type="text" name="price_code" label="Kod wyceny" value="{{ $request->price_code }}" />
                <div id="price-summary" class="hint-table">
                    <div class="positions"></div>
                    <hr />
                    <div class="summary"><span>Razem:</span><span>0 zł</span></div>
                </div>
            </div>

            <div>
                <div id="delayed-payments-summary" class="hint-table">
                    <div class="positions"></div>
                    <hr />
                    <div class="summary"></div>
                </div>
                <script>
                function calcPriceNow(){
                    const labels = $("#price_code").val();
                    const client_id = $("#client_id").val();
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
                                client_id: client_id,
                                quoting: true
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

                                checkMonthlyPaymentLimit(res.price);
                            }
                        });
                    }
                }
                function checkMonthlyPaymentLimit(price){
                    const positions_list = $("#delayed-payments-summary .positions");
                    const sum_row = $("#delayed-payments-summary .summary");

                    $.ajax({
                        url: "/api/monthly_payment_limit",
                        type: "post",
                        data: {
                            _token: '{{ csrf_token() }}',
                            amount: price,
                        },
                        success: function(res){
                            let when_to_ask = "";
                            switch(res.when_to_ask){
                                case 0: when_to_ask = "<span class='success'>od razu</span>"; break;
                                case 1: when_to_ask = "<span class='warning'>w przyszłym miesiącu</span>"; break;
                                default: when_to_ask = `<span class='error'>za ${res.when_to_ask} mc</span>`;
                            }
                            let content = ``;
                            content += `<span>Saturacja</span><span>${res.saturation[0]} zł • ${res.saturation[1]} zł • ${res.saturation[2]} zł</span>`;
                            positions_list.html(content);
                            sum_row.html(`<span>Kiedy można brać?</span><span>${when_to_ask}</span>`);

                            let delayed_payment;
                            if(res.when_to_ask == 0){
                                delayed_payment = undefined;
                            }else{
                                let today = new Date();
                                delayed_payment = (new Date(today.getFullYear(), today.getMonth() + res.when_to_ask, 1));
                                delayed_payment = `${delayed_payment.getFullYear()}-${(delayed_payment.getMonth() + 1).toString().padStart(2, 0)}-${delayed_payment.getDate().toString().padStart(2, 0)}`;
                            }
                            document.getElementById("delayed_payment").value = delayed_payment;
                        }
                    });
                }
                </script>
                <script defer>
                calcPriceNow();
                $("#price_code").change(function (e) { calcPriceNow() });
                </script>
                @if ($request->client?->budget && in_array($request->status_id, [1, 5, 6]))
                <span class="{{ $request->client->budget >= $request->price ? 'success' : 'warning' }}">
                    <i class="fa-solid fa-sack-dollar"></i>
                    Budżet w wysokości <b>{{ _c_(as_pln($request->client->budget)) }}</b> automatycznie
                    <br>
                    pokryje
                    @if ($request->client->budget >= $request->price)
                    całą kwotę zlecenia
                    @else
                    część kwoty zlecenia
                    @endif
                </span>
                @endif
                <x-input type="date" name="delayed_payment" label="Opóźnienie wpłaty" value="{{ $request->delayed_payment?->format('Y-m-d') }}" />
            </div>

            <div>
                @if (in_array($request->status_id, [1, 6, 96])) <div class="folding"><x-calendar /></div> @endif
                <x-input type="date" name="deadline" label="Do kiedy (włącznie) oddam pliki" value="{{ $request->deadline?->format('Y-m-d') }}" />
            </div>
        </x-extendo-block>

        @unless (in_array($request->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
        <x-quest-history :quest="$request" :extended="true" />
        @endunless
    </div>
</x-shipyard.app.form>

@endsection
