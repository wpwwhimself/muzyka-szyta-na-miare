@extends('layouts.app')
@section("title", $request->full_title)
@section("subtitle", "Zapytanie")

@section('content')

@php
$fields = $request::getFields();
@endphp

<x-shipyard.app.form method="POST" :action="route('mod-request-back')">
    <x-slot:actions>
        @auth
        <x-shipyard.ui.button
            icon="angles-left"
            label="Wróć do listy"
            :action="route('requests')"
        />
        @endauth
    </x-slot:actions>

    <h1>
        @if (sumWarnings($warnings))
        <x-warning>
            Jest kilka rzeczy, z którymi musisz się koniecznie zapoznać!
        </x-warning>
        @endif
    </h1>
    <x-phase-indicator :status-id="$request->status_id" />

    @if ($request->quest_id)
    <h2>
        Zapytanie zostało przyjęte i jest przepisane na zlecenie {{ $request->quest_id }}
        <x-shipyard.ui.button
            label="Przejdź do zlecenia"
            :icon="model_icon('quests')"
            :action="route('quest', ['id' => $request->quest_id])"
            class="primary"
        />
    </h2>
    @endif

    <div class="grid but-mobile-down" style="--col-count: 2;">
        <x-extendo-block key="meta"
            :header-icon="model_icon('songs')"
            title="Dane zlecenia"
            subtitle="Jaki utwór mam przygotować?"
            :extended="true"
        >
            @foreach ([
                "title",
                "artist",
                "link",
                "wishes",
                "wishes_quest",
            ] as $field_name)
                <x-shipyard.ui.field-input :model="$request" :field-name="$field_name" />
                @if ($field_name == "link")
                <x-link-interpreter :raw="$request->$field_name" />
                @endif
            @endforeach
        </x-extendo-block>

        <x-extendo-block key="quote"
            :header-icon="model_icon('prices')"
            title="Wycena"
            subtitle="Na jakich warunkach go przygotuję?"
            :warning="$warnings['quote']"
            :extended="true"
        >
            @if (!$request->price)
            <p class="yellowed-out">pojawi się w ciągu najbliższych dni</p>
            @else

                @if ($request->status_id == 1)
                <p class="yellowed-out">poniższa wycena może być nieaktualna – poczekaj na odpowiedź</p>
                @endif

                <x-re_quests.price-summary :model="$request" />

                @if ($request->client?->budget && in_array($request->status_id, [5, 6]))
                <span class="{{ $request->client->budget >= $request->price ? 'success' : 'warning' }}">
                    <i class="fa-solid fa-sack-dollar"></i>
                    Budżet w wysokości <b>{{ as_pln($request->client->budget) }}</b> automatycznie
                    <br>
                    pokryje
                    @if ($request->client->budget >= $request->price)
                    całą kwotę zlecenia
                    @else
                    część kwoty zlecenia
                    @endif
                </span>
                @endif
            @endif

            @if ($request->hard_deadline)
            <x-shipyard.ui.field-input :model="$request" field-name="hard_deadline" />
            @endif
            @if ($request->deadline)
            <x-shipyard.ui.field-input :model="$request" field-name="deadline" />
            @endif

            <x-extendo-section>
                @if ($request->price && $request->status_id == 5)
                    @if ($request->deadline)
                    <p>
                        Termin oddania jest liczony do podanego dnia włącznie.
                        Są duże szanse, że uda mi się wykonać zlecenie szybciej,
                        ale to jest najpóźniejszy dzień.
                    </p>
                    @endif
                    <p>Opłaty projektu będzie można dokonać na 2 sposoby:</p>
                    <ul>
                        <li>na numer konta,</li>
                        <li>BLIKiem na numer telefonu.</li>
                    </ul>
                    <p>Pliki będą dostępne z poziomu tej strony internetowej.</p>
                @endif
                @if ($request->delayed_payment)
                <x-warning>
                    Z uwagi na limity przyjmowanych przeze mnie wpłat z racji prowadzenia działalności nierejestrowanej,
                    <b>proszę o dokonanie wpłaty po {{ $request->delayed_payment->format('d.m.Y') }}</b>.
                    Po zaakceptowaniu zlecenia dostęp do plików (kiedy tylko się pojawią) zostanie przyznany automatycznie.
                </x-warning>
                @endif
            </x-extendo-section>
        </x-extendo-block>
    </div>

    <x-quest-history :quest="$request" :extended="in_array($request->status_id, [5, 95])" />
    @if (in_array($request->status_id, [4, 7, 8]))
    <x-tutorial>
        Zapytanie zostało zamknięte, ale nadal możesz je przywrócić w celu ponownego złożenia zamówienia.
    </x-tutorial>
    @endif
    <div id="phases">
        <input type="hidden" name="id" value="{{ $request->id }}" />
        <input type="hidden" name="intent" value="review" />
        @if($request->status_id != 5)
        <div class="flexright">
            @if (in_array($request->status_id, [95])) <x-button action="#/" statuschanger="96" icon="96" label="Odpowiedz" /> @endif
            @if (in_array($request->status_id, [5])) <x-button label="Potwierdź" statuschanger="9" icon="9" action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}" /> @endif
            @if (in_array($request->status_id, [5])) <x-button action="#/" statuschanger="6" icon="6" label="Poproś o ponowną wycenę" /> @endif
            @if (in_array($request->status_id, [5, 95])) <x-button action="#/" statuschanger="8" icon="8" label="Zrezygnuj ze zlecenia" /> @endif
            @if (in_array($request->status_id, [4, 7, 8])) <x-button action="#/" statuschanger="1" icon="1" label="Odnów" /> @endif
        </div>
        @else
        <div id="opinion-1">
            <h2>Czy odpowiada Ci powyższa wycena?</h2>
            <div>
                <x-button label="Tak" icon="check" action="none" onclick="goToConfirm2()" />
                <x-button label="Nie" icon="times" action="none" onclick="goToReject()" />
            </div>
        </div>
        <div id="opinion-2" class="hidden">
            <h2>Co chciał{{ client_polonize($request->client_name)["kobieta"] ? "a" : "" }}byś zmienić?</h2>
            <div>
                <x-button onclick="expandReject(this)" optbc="link" label="Link do nagrania" icon="compact-disc" action="#/" :small="true" />
                <x-button onclick="expandReject(this)" optbc="wishes" label="Życzenia" icon="note-sticky" action="#/" :small="true" />
                <x-button onclick="expandReject(this)" optbc="deadline" label="Czas oczekiwania" icon="clock" action="#/" :small="true" />
                <x-button onclick="expandReject(this)" optbc="nothing" label="Nic, rezygnuję" icon="8" action="#/" :small="true" />
            </div>
            <input type="hidden" name="optbc">
            <div id="opinion-inputs" class="flex down hidden">
                <x-input type="text" name="opinion_link" label="Podaj nowy link do nagrania" :value="$request->link" />
                <x-input type="TEXT" name="opinion_wishes" label="Podaj nowe życzenia" :value="$request->wishes" />
                <div class="priority" for="opinion_deadline">
                    <p>W trybie priorytetowym jestem w stanie wykonać zlecenie poza kolejnością; wiąże się to jednak z podwyższoną ceną.</p>
                    <div class="flex right center">
                        <x-input type="date" name="new-deadline-date" label="Nowy termin, do kiedy (włącznie) oddam pliki" :value="get_next_working_day()->format('Y-m-d')" :disabled="true" />
                        <x-input type="text" name="new-deadline-price" label="Nowa cena zlecenia" :value="as_pln(price_calc($request->price_code.'z', $request->client_id, true)['price'])" :disabled="true" />
                    </div>
                </div>
                <x-input for="opinion_link opinion_wishes opinion_nothing" type="TEXT" name="comment" label="Komentarz (opcjonalne)" />
                <x-button for="opinion_link opinion_wishes" action="submit" icon="6" name="new_status" value="6" label="Oddaj do ponownej wyceny" />
                <x-button for="opinion_nothing" action="submit" icon="8" name="new_status" value="8" label="Zrezygnuj z zapytania" />
                <x-button for="opinion_deadline" action="{{ route('request-final', ['id' => $request->id, 'status' => 9, 'with_priority' => true]) }}" icon="9" label="Zaakceptuj nową wycenę" :danger="true" />
            </div>
        </div>
        <div id="opinion-3" class="hidden">
            <h2>Na pewno? Termin realizacji też?</h2>
            @if ($request->delayed_payment)
            <p class="yellowed-out">To, że musisz zapłacić później, też?</p>
            @endif
            <div>
                <x-button label="Tak" icon="9" action="{{ route('request-final', ['id' => $request->id, 'status' => 9]) }}" />
                <x-button label="Nie" icon="times" action="#/" />
            </div>
        </div>
        <script defer>
        function goToReject() {
            document.querySelector("#opinion-1 a.ghost").classList.remove("ghost");
            document.querySelector(`#opinion-1 a:first`).classList.add("ghost");

            document.querySelector("#opinion-2").classList.remove("hidden");
            document.querySelector("#opinion-3").classList.add("hidden");
        });

        function expandReject(btn) {
            let optbc = btn.dataset.optbc;
            document.querySelector("input[name='optbc']").val(optbc);

            document.querySelector("#opinion-2 a[optbc]").addClass("ghost");
            document.querySelector(`#opinion-2 a[optbc='${optbc}']`).removeClass("ghost");

            document.querySelector("#opinion-2 #opinion-inputs [for^='opinion_']").addClass("hidden");
            document.querySelector(`#opinion-2 #opinion-inputs [for~='opinion_${optbc}']`).removeClass("hidden");
            document.querySelector("#opinion-2 #opinion-inputs").removeClass("hidden");
            document.querySelector("#opinion-2 #opinion-submit").removeClass("hidden");
        });

        document.querySelector("#opinion-1 a:first").click(function(){
            document.querySelector("#opinion-1 a.ghost").removeClass("ghost");
            document.querySelector(`#opinion-1 a:last`).addClass("ghost");

            document.querySelector("#opinion-2").addClass("hidden");
            document.querySelector("#opinion-3").removeClass("hidden");
        });
        </script>
        @endif
    </div>
</x-shipyard.app.form>
@endsection
