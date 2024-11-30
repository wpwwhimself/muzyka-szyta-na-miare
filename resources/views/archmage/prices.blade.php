@extends('layouts.app', compact("title"))

@section('content')

<x-section id="prices" title="Cenniki" icon="barcode">
    <table>
        <thead>
            <tr>
                <th>Usługa</th>
                <th>Kod</th>
                @for ($letter = "A"; $letter != chr(ord(CURRENT_PRICING()) + 1); $letter = chr(ord($letter) + 1))
                <th>Cena {{ $letter }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ([
                "1" => "Podkłady muzyczne",
                "2" => "Nuty",
                "3" => "Nagrania",
                null => "Pozostałe"
            ] as $i => $header)
                <tr>
                    <td colspan={{ ord(CURRENT_PRICING()) - ord("A") + 3 }}>
                        <h2>{{ $header }}</h2>
                    </td>
                </tr>
                @foreach ($prices->where("quest_type_id", $i) as $price)
                <tr>
                    <td>{{ $price->service }}</td>
                    <td>{{ $price->indicator }}</td>
                    @for ($letter = "A"; $letter != chr(ord(CURRENT_PRICING()) + 1); $letter = chr(ord($letter) + 1))
                        @if ($price->operation == "+")
                        <td>{{ _c_(as_pln($price->{"price_".strtolower($letter)})) }}</td>
                        @else
                        <td>{{ _c_($price->{"price_".strtolower($letter)} * 100) }}%</td>
                        @endif
                    @endfor
                </tr>
                @endforeach
                @if ($header == "Nuty")
                <tr class="ghost">
                    <td colspan={{ ord(CURRENT_PRICING()) - ord("A") + 3 }}>
                        <i class="fa-solid fa-info-circle"></i>
                        Nuty przygotowywane razem z podkładem muzycznym lub nagraniem są wyceniane o połowę taniej.
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</x-section>

<div class="grid-2">
    <x-section title="Uwagi" icon="circle-exclamation">
        <ul>
            <li>
                Dla każdego typu zlecenia jest wyznaczona <b>cena minimalna</b>. Obecnie jest to:
                <ul>
                    @foreach ($minimal_prices as $label => $price)
                    <li>{{ $label }}: {{ _c_(as_pln($price)) }}</li>
                    @endforeach
                </ul>
            </li>
            <li>
                <i>Przygotowanie filmu</i> w przypadku nagrania siebie z instrumentem
                wyceniane jest do 2 filmów (czyli 6 instrumentów = qqq).
            </li>
        </ul>
    </x-section>

    <x-section title="Szybka wycena" icon="sack-dollar">
        <div class="flex-right center">
            <div>
                <x-select name="client_id" label="Klient" :options="$clients" />
                <div id="song-price-sugg"></div>
                <div id="special-prices-warning"></div>
                <x-input type="text" name="price_code" label="Kod wyceny" />
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
                $(document).ready(function(){
                    calcPriceNow();
                    $("#price_code").change(function (e) { calcPriceNow() });
                });
                </script>
            </div>
        </div>
    </x-section>
</div>

<script defer>
$("#client_id").select2();
</script>

@endsection
