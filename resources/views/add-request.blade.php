@extends('layouts.app', compact("title"))

@section('content')
    <form method="post" action={{ route("add-request-back") }}>
        @csrf
        <h1>Dodaj nowe zapytanie</h1>
        <div class="grid-3">
            <section class="input-group">
                <h2><i class="fa-solid fa-user"></i> Dane klienta</h2>
                @if (Auth::id() != 1)
                <x-input type="text" name="client_name" label="Nazwisko/Nazwa" :autofocus="true" :required="true" :disabled="true" value="{{ Auth::user()->client->client_name }}" />
                <x-input type="email" name="email" label="Adres e-mail" :disabled="true" value="{{ Auth::user()->client->email }}" />
                <x-input type="tel" name="phone" label="Numer telefonu" :disabled="true" value="{{ Auth::user()->client->phone }}" />
                <x-input type="text" name="other_medium" label="Inna forma kontaktu" :disabled="true" value="{{ Auth::user()->client->other_medium }}" />
                <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" placeholder="email" :disabled="true" value="{{ Auth::user()->client->contact_preference }}" />
                @else
                <x-input type="text" name="client_name" label="Nazwisko/Nazwa" :autofocus="true" :required="true" />
                <x-input type="email" name="email" label="Adres e-mail" />
                <x-input type="tel" name="phone" label="Numer telefonu" />
                <x-input type="text" name="other_medium" label="Inna forma kontaktu" />
                <x-input type="text" name="contact_preference" label="Preferencja kontaktowa" placeholder="email" />
                @endif
            </section>
            <section class="input-group">
                <h2><i class="fa-solid fa-cart-flatbed"></i> Dane zlecenia</h2>
                <x-select name="quest_type" label="Rodzaj zlecenia" :options="$questTypes" :required="true" />
                <x-input type="text" name="title" label="Tytuł utworu" />
                <x-input type="text" name="artist" label="Oryginalny wykonawca" />
                <x-input type="text" name="cover_artist" label="Coverujący" />
                <x-input type="url" name="link" label="Link do nagrania" />
                <x-input type="TEXT" name="wishes" label="Życzenia" />
            </section>
            <section class="input-group">
                <h2><i class="fa-solid fa-sack-dollar"></i> Wycena</h2>
                @if (Auth::id() == 1)
                <x-input type="text" name="price" label="Wycena (kod lub kwota)" :hint="$prices" />
                <div id="price-summary">
                    <div class="positions"></div>
                    <hr />
                    <div class="summary"><span>Razem:</span><span>0 zł</span></div>
                </div>
                <script>
                function calcPriceNow(){
                    const labels = $("#price").val();
                    const positions_list = $("#price-summary .positions");
                    if(labels == "") positions_list.html(`<p class="grayed-out">podaj kategorie wyceny</p>`);
                    else{
                        $.ajax({
                            url: "/price_calc",
                            type: "post",
                            data: {
                                _token: '{{ csrf_token() }}',
                                labels: labels,
                                price_schema: "B",
                                veteran_discount: 0
                            },
                            success: function(res){
                                let content = ``;
                                for(line of res[1]){
                                    content += `<span>${line[0]}</span><span>${line[1]}</span>`;
                                }
                                positions_list.html(content);
                            }
                        });
                    }
                }
                $(document).ready(function(){
                    calcPriceNow();
                    $("#price").change(function (e) { calcPriceNow() });
                });
                </script>
                @endif
                <x-input type="date" name="deadline" label="Termin wykonania" />
                @if (Auth::id() == 1)
                <x-input type="checkbox" name="hard_deadline" label="Termin narzucony przez klienta" />
                @endif
            </section>
        </div>
        <button type="submit" class="hover-lift">
            <i class="fa-solid fa-check"></i> Dodaj
        </button>
    </form>
@endsection
