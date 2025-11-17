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
            icon="chevron-left"
            label="Wróć do listy"
            :action="route('requests')"
        />
        @endauth
    </x-slot:actions>

    @if (sumWarnings($warnings))
    <div class="flex right center middle accent danger">
        <h1><x-shipyard.app.icon name="alert" /></h1>

        <div>
            <h1>Jest kilka rzeczy, z którymi musisz się koniecznie zapoznać!</h1>
            <span>Najedź kursorem na ikony, aby dowiedzieć się więcej</span>
        </div>
    </div>
    @endif

    <x-phase-indicator :status-id="$request->status_id" />

    @if ($request->quest_id)
    <div class="flex right center middle">
        <h2>Zapytanie zostało przyjęte i jest przepisane na zlecenie <span class="mono">{{ $request->quest_id }}</span></h2>
        <x-shipyard.ui.button
            label="Przejdź do zlecenia"
            :icon="model_icon('quests')"
            :action="route('quest', ['id' => $request->quest_id])"
            class="primary"
        />
    </div>
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
                <x-shipyard.ui.field-input :model="$request" :field-name="$field_name" dummy />
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

                @if ($request->user?->notes->budget && in_array($request->status_id, [5, 6]))
                <span class="accent {{ $request->user->notes->budget >= $request->price ? 'success' : 'danger' }}">
                    <x-shipyard.app.icon name="safe-square" />
                    Budżet w wysokości <b>{{ as_pln($request->user->notes->budget) }}</b> automatycznie
                    <br>
                    pokryje
                    @if ($request->user->notes->budget >= $request->price)
                    całą kwotę zlecenia
                    @else
                    część kwoty zlecenia
                    @endif
                </span>
                @endif
            @endif

            @if ($request->hard_deadline)
            <x-shipyard.ui.field-input :model="$request" field-name="hard_deadline" dummy />
            @endif
            @if ($request->deadline)
            <x-shipyard.ui.field-input :model="$request" field-name="deadline" dummy />
            @endif

            @if ($request->price && $request->status_id == 5)
                @if ($request->deadline)
                <p>
                    Termin oddania jest liczony do podanego dnia włącznie.
                    Są duże szanse, że uda mi się wykonać zlecenie szybciej,
                    ale to jest najpóźniejszy dzień.
                </p>
                @endif
                <p>Opłaty zlecenia będzie można dokonać na 2 sposoby:</p>
                <ul>
                    <li>przelew na numer konta,</li>
                    <li>płatność BLIKiem na numer telefonu.</li>
                </ul>
                <p>Pliki będą dostępne z poziomu tej strony internetowej.</p>
            @endif

            @if ($request->delayed_payment)
            <div class="accent danger flex right spread middle">
                <x-shipyard.ui.field-input :model="$request" field-name="delayed_payment" dummy />
                <x-warning>
                    Z uwagi na limity przyjmowanych przeze mnie wpłat z racji prowadzenia działalności nierejestrowanej,
                    <b>proszę o dokonanie wpłaty po {{ $request->delayed_payment->format('d.m.Y') }}</b>.
                    Po zaakceptowaniu zlecenia dostęp do plików (kiedy tylko się pojawią) zostanie przyznany automatycznie.
                </x-warning>
            </div>
            @endif
        </x-extendo-block>
    </div>

    @if (in_array($request->status_id, [4, 7, 8]))
    <x-tutorial>
        Zapytanie zostało zamknięte, ale nadal możesz je przywrócić w celu ponownego złożenia zamówienia.
    </x-tutorial>
    @endif
    <div id="phases">
        <input type="hidden" name="id" value="{{ $request->id }}" />
        <input type="hidden" name="intent" value="review" />

        @if($request->status_id != 5)
        <div class="flex right center">
            @if (in_array($request->status_id, [95]))
            <x-shipyard.ui.button
                label="Odpowiedz"
                icon="reply-all"
                action="none"
                onclick="openModal('restatus-with-comment', {
                    model: 'request',
                    id: '{{ $request->id }}',
                    newStatus: 96,
                    changedBy: {{ Auth::id() ?? 'null' }},
                })"
                class="tertiary"
            />
            <x-shipyard.ui.button
                label="Zrezygnuj ze zlecenia"
                icon="clipboard-remove"
                action="none"
                onclick="openModal('restatus-with-comment', {
                    model: 'request',
                    id: '{{ $request->id }}',
                    newStatus: 8,
                    changedBy: {{ Auth::id() ?? 'null' }},
                })"
                class="tertiary"
            />
            @endif
            @if (in_array($request->status_id, [4, 7, 8]))
            <x-shipyard.ui.button
                label="Odnów"
                icon="star"
                action="none"
                onclick="openModal('restatus-with-comment', {
                    model: 'request',
                    id: '{{ $request->id }}',
                    newStatus: 1,
                    changedBy: {{ Auth::id() ?? 'null' }},
                })"
                class="tertiary"
            />
            @endif
        </div>

        @else
        <div class="backdropped rounded padded flex down center">

            <div id="opinion-1" class="flex down center middle">
                <h2>Czy odpowiada Ci powyższa wycena?</h2>
                <div>
                    <x-shipyard.ui.button label="Tak" icon="check"
                        action="none"
                        onclick="goToConfirm2()"
                    />
                    <x-shipyard.ui.button label="Nie" icon="close"
                        action="none"
                        onclick="goToReject()"
                        class="tertiary"
                    />
                </div>
            </div>
            <div id="opinion-2" class="flex down center middle hidden">
                <h2>Co chciał{{ client_polonize($request->client_name)["kobieta"] ? "a" : "" }}byś zmienić?</h2>
                <div>
                    <x-shipyard.ui.button
                        onclick="expandReject(this)"
                        data-optbc="link"
                        label="Link do nagrania"
                        :icon="model_icon('songs')"
                        action="none"
                        class="tertiary"
                    />
                    <x-shipyard.ui.button
                        onclick="expandReject(this)"
                        data-optbc="wishes"
                        label="Życzenia"
                        :icon="model('requests')::getFields()['wishes']['icon']"
                        action="none"
                        class="tertiary"
                    />
                    <x-shipyard.ui.button
                        onclick="expandReject(this)"
                        data-optbc="deadline"
                        label="Czas oczekiwania"
                        :icon="model('requests')::getFields()['deadline']['icon']"
                        action="none"
                        class="tertiary"
                    />
                    <x-shipyard.ui.button
                        onclick="expandReject(this)"
                        data-optbc="nothing"
                        label="Nic, rezygnuję"
                        icon="clipboard-remove"
                        action="none"
                        class="tertiary"
                    />
                </div>
                <input type="hidden" name="optbc">
                <div id="opinion-inputs" class="flex down hidden">
                    <x-shipyard.ui.input type="text"
                        name="opinion_link"
                        label="Podaj nowy link do nagrania"
                        :value="$request->link"
                        data-opinion-role="link"
                    />
                    <x-shipyard.ui.input type="TEXT"
                        name="opinion_wishes"
                        label="Podaj nowe życzenia"
                        :value="$request->wishes"
                        data-opinion-role="wishes"
                    />
                    <div class="priority" data-opinion-role="deadline">
                        <p>W trybie priorytetowym jestem w stanie wykonać zlecenie poza kolejnością; wiąże się to jednak z podwyższoną ceną.</p>
                        <div class="flex right center">
                            <x-shipyard.ui.input type="date" name="new-deadline-date" label="Nowy termin, do kiedy (włącznie) oddam pliki" :value="get_next_working_day()" :disabled="true" />
                            <x-shipyard.ui.input type="text" name="new-deadline-price" label="Nowa cena zlecenia" :value="as_pln(\App\Http\Controllers\StatsController::runPriceCalc($request->price_code.'z', $request->client_id, true)['price'])" :disabled="true" />
                        </div>
                    </div>
                    <x-shipyard.ui.input
                        data-opinion-role="link wishes nothing"
                        type="TEXT"
                        name="comment"
                        label="Komentarz (opcjonalne)"
                    />

                    <x-shipyard.ui.button data-opinion-role="link wishes"
                        action="submit"
                        icon="clipboard-alert"
                        name="new_status" value="6"
                        label="Oddaj do ponownej wyceny"
                        class="primary"
                    />
                    <x-shipyard.ui.button data-opinion-role="nothing" action="submit"
                        icon="clipboard-remove"
                        name="new_status" value="8"
                        label="Zrezygnuj z zapytania"
                        class="primary"
                    />
                    <x-shipyard.ui.button data-opinion-role="deadline" :action="route('request-final', ['id' => $request->id, 'status' => 9, 'with_priority' => true])"
                        icon="clipboard-check"
                        label="Zaakceptuj nową wycenę"
                        class="danger"
                    />
                </div>
            </div>
            <div id="opinion-3" class="flex down center middle hidden">
                <h2>Na pewno? Termin realizacji też?</h2>
                @if ($request->delayed_payment)
                <p class="yellowed-out">To, że musisz zapłacić później, też?</p>
                @endif
                <div>
                    <x-shipyard.ui.button
                        label="Tak"
                        icon="clipboard-check"
                        :action="route('request-final', ['id' => $request->id, 'status' => 9])"
                        class="primary"
                    />
                    <x-shipyard.ui.button
                        label="Nie"
                        icon="close"
                        action="none"
                        class="tertiary"
                    />
                </div>
            </div>
        </div>
        @endif
    </div>

    <x-quest-history :quest="$request" :extended="in_array($request->status_id, [5, 95])" />
</x-shipyard.app.form>

<script>
function goToReject() {
    document.querySelectorAll("#opinion-1 .button").forEach(btn => {
        btn.classList.toggle("ghost", btn.classList.contains("primary"));
    });

    document.querySelector("#opinion-2").classList.remove("hidden");
    document.querySelector("#opinion-3").classList.add("hidden");
}

function expandReject(btn) {
    let optbc = btn.dataset.optbc;
    document.querySelector("input[name='optbc']").value = optbc;

    document.querySelectorAll("#opinion-2 span[data-optbc]").forEach(btn => {
        btn.classList.toggle("ghost", btn.dataset.optbc !== optbc);
    })
    document.querySelectorAll("#opinion-2 #opinion-inputs [data-opinion-role]").forEach(btn => {
        btn.classList.toggle("hidden", !btn.dataset.opinionRole.includes(optbc));
    })
    document.querySelector("#opinion-2 #opinion-inputs").classList.remove("hidden");
}

function goToConfirm2() {
    document.querySelectorAll("#opinion-1 .button").forEach(btn => {
        btn.classList.toggle("ghost", !btn.classList.contains("primary"));
    });

    document.querySelector("#opinion-2").classList.add("hidden");
    document.querySelector("#opinion-3").classList.remove("hidden");
}
</script>
@endsection
