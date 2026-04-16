@extends('layouts.app')
@section("title", $request->full_title)
@section("subtitle", "Zapytanie")

@section('content')

@if (sumWarnings($warnings) && !in_array($request->status_id, STATUSES_WAITING_FOR_ME()))
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
        <x-shipyard.ui.connection-input :model="$request" connection-name="quest_type" dummy />

        @foreach ([
            "title",
            "artist",
            "link",
            "wishes",
            "hard_deadline",
        ] as $field_name)
            <x-shipyard.ui.field-input :model="$request" :field-name="$field_name" dummy />
            @if ($field_name == "link")
            <x-link-interpreter :raw="$request->$field_name" />
            @endif
        @endforeach

        @if ($request->status_id === 5)
        <div class="flex right center">
            <x-shipyard.ui.button
                label="Poproś o zmiany do utworu"
                icon="reply"
                action="none"
                onclick="openModal('restatus-with-comment', {
                    model: 'request',
                    id: '{{ $request->id }}',
                    newStatus: 6,
                    changedBy: {{ Auth::id() ?? 'null' }},
                })"
                class="tertiary"
            />
        </div>
        @endif
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
            @if (in_array($request->status_id, [1, 6]))
            <p class="yellowed-out">poniższa wycena może być nieaktualna – poczekaj na odpowiedź</p>
            @endif

            <div class="flex down">
                <x-shipyard.app.card title="Płatność" icon="cash">
                    <div class="standard">
                        <x-re_quests.price-summary :model="$request" />
                    </div>
                    <div class="priority hidden">
                        @php
                        [
                            "price" => $priority_price,
                            "positions" => $positions,
                            "override" => $override,
                            "labels" => $labels,
                            "minimal_price" => $minimalPrice,
                        ] = \App\Http\Controllers\StatsController::runPriceCalc($request->price_code.'z', $request->client_id, true);
                        [
                            "saturation" => $saturation,
                            "when_to_ask" => $when_to_ask,
                            "limit_corrected" => $limit_corrected,
                        ] = \App\Http\Controllers\StatsController::runMonthlyPaymentLimit($priority_price);
                        $priority_delayed_payment = ($when_to_ask > 0)
                            ? \Carbon\Carbon::today()->addMonthsNoOverflow($when_to_ask)->firstOfMonth()
                            : null;
                        @endphp
                        <x-re_quests.price-summary :price="$priority_price" :positions="$positions" :override="$override" :labels="$labels" :minimal-price="$minimalPrice" />
                    </div>

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

                    @if ($request->delayed_payment)
                    <p class="accent danger">
                        Z uwagi na limity przyjmowanych przeze mnie wpłat z racji prowadzenia działalności nierejestrowanej,
                        <b>proszę o dokonanie wpłaty nie wcześniej niż {{ $request->delayed_payment->format('d.m.Y') }}</b>.
                    </p>
                    @endif
                    @if ($priority_delayed_payment)
                    <p class="accent danger priority hidden">
                        Z uwagi na limity przyjmowanych przeze mnie wpłat z racji prowadzenia działalności nierejestrowanej,
                        <b>proszę o dokonanie wpłaty nie wcześniej niż {{ $priority_delayed_payment->format('d.m.Y') }}</b>.
                    </p>
                    @endif

                    <p>Opłaty zlecenia będzie można dokonać na 2 sposoby:</p>
                    <ul>
                        <li>przelew na numer konta,</li>
                        <li>płatność BLIKiem na numer telefonu.</li>
                    </ul>
                    <p>Poprawki do zlecenia są zawarte w cenie, chyba że będą wiązać się z dużym zakresem zmian lub zostaną zgłoszone później niż miesiąc po zaakceptowaniu dostarczonych plików.</p>
                </x-shipyard.app.card>

                @if ($request->deadline)
                <x-shipyard.app.card title="Termin realizacji" icon="calendar">
                    <div class="standard">
                        <x-shipyard.ui.field-input :model="$request" field-name="deadline" dummy />
                    </div>
                    <div class="priority hidden">
                        <x-shipyard.ui.input type="dummy-date"
                            name="priority_deadline"
                            label="Przyspieszony termin realizacji"
                            :icon="model_field_icon('requests', 'deadline')"
                            :value="get_next_working_day()"
                        />
                    </div>
                    <p>
                        Termin oddania jest liczony do podanego dnia włącznie.
                        Są duże szanse, że uda mi się wykonać zlecenie szybciej,
                        ale to jest najpóźniejszy dzień.
                    </p>

                    @if ($request->status_id === 5)
                    <div class="flex right center">
                        <x-shipyard.ui.button
                            label="Poproś o szybszą realizację"
                            icon="calendar"
                            action="none"
                            onclick="togglePriority()"
                            class="tertiary standard"
                        />
                        <x-shipyard.ui.button
                            label="Wróć do poprzedniej wyceny"
                            icon="calendar"
                            action="none"
                            onclick="togglePriority()"
                            class="tertiary priority hidden"
                        />
                    </div>
                    @endif
                </x-shipyard.app.card>
                @endif

                @if ($request->price && $request->status_id == 5)
                <x-shipyard.app.card title="Pobieranie plików" icon="download">
                    <p>Pliki będą dostępne z poziomu tej strony internetowej.</p>
                    @if ($request->delayed_payment)
                    <p class="accent danger">
                        Po zaakceptowaniu zlecenia dostęp do plików (kiedy tylko się pojawią) zostanie przyznany automatycznie.
                    </p>
                    @endif
                </x-shipyard.app.card>
                @endif
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

    <div class="flex right center middle">
        <x-shipyard.ui.button
            label="Kliknij tutaj, aby potwierdzić warunki zlecenia"
            :icon="\App\Models\Status::find(9)->icon"
            action="none"
            onclick="confirmRequest()"
            class="major primary"
        />
        <x-shipyard.ui.button
            label="...lub tutaj, aby zrezygnować"
            :icon="\App\Models\Status::find(8)->icon"
            action="none"
            onclick="openModal('restatus-with-comment', {
                model: 'request',
                id: '{{ $request->id }}',
                newStatus: 8,
                changedBy: {{ Auth::id() ?? 'null' }},
            })"
            class="tertiary"
        />
    </div>
    @endif
</div>

<x-quest-history :quest="$request" :extended="false" />

<div class="flex right center middle">
    @auth
    <x-shipyard.ui.button
        icon="chevron-left"
        label="Wróć do listy"
        :action="route('requests')"
    />
    @endauth
</div>

@if ($request->status_id == 5)
<script>
function togglePriority() {
    document.querySelectorAll(".priority, .standard").forEach(el => {
        el.classList.toggle("hidden");
    });
}

function confirmRequest() {
    const prices = [
        {{ $request->price }},
        {{ $priority_price }},
    ];
    const deadlines = [
        `{{ $request->deadline->format('d.m.Y') }}`,
        `{{ get_next_working_day()->format('d.m.Y') }}`,
    ];
    const delayed_payments = [
        `{{ $request->delayed_payment?->format('d.m.Y') }}`,
        `{{ $priority_delayed_payment?->format('d.m.Y') }}`,
    ];
    const strings = {
        confirm_price: `{{ \App\Models\Request::confirmLabels()['confirm_price'] }}`,
        confirm_deadline: `{{ \App\Models\Request::confirmLabels()['confirm_deadline'] }}`,
        confirm_delayed_payment: `{{ \App\Models\Request::confirmLabels()['confirm_delayed_payment'] }}`,
    };

    const is_priority = !document.querySelector(".priority").classList.contains("hidden");

    openModal('confirm-quote', {
        id: '{{ $request->id }}',
        is_priority: is_priority ? 1 : 0,
    }, {
        confirm_price: {
            label: strings.confirm_price.replace('???', prices[is_priority ? 1 : 0]),
        },
        confirm_deadline: {
            label: strings.confirm_deadline.replace('???', deadlines[is_priority ? 1 : 0]),
        },
        confirm_delayed_payment: {
            hide: !delayed_payments[is_priority ? 1 : 0],
            label: strings.confirm_delayed_payment.replace('???', delayed_payments[is_priority ? 1 : 0]),
        },
    })
}
</script>
@endif

@endsection
