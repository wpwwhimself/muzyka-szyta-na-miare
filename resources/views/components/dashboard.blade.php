@props([
    "user",
    "data",
])

@if (is_archmage())
<div @class(["grid but-mobile-down", "stagger-contents" => setting("animations_mode") >= 1]) style="--col-count: 2;">

@if (count($data["patrons_adepts"]) > 0)
<x-shipyard::app.section id="patrons-adepts"
    title="Potencjalni patroni"
    icon="seal"
    style="grid-column: span 2;"
>
    <x-slot:actions>
        <x-shipyard::ui.button
            icon="chevron-double-right"
            label="Recenzje"
            target="_blank"
            action="https://www.facebook.com/muzykaszytanamiarepl/reviews"
        />
    </x-slot:actions>

    @foreach ($data["patrons_adepts"] as $patron)
    <x-shipyard::app.model.tile
        :model="$patron"
    >
        <x-slot:actions>
            <x-shipyard::ui.button
                icon="check"
                pop="Zatwierdź"
                :action="route('patron-mode', ['client_id' => $patron->id, 'level' => 2])"
                class="primary"
            />
            <x-shipyard::ui.button
                icon="cancel"
                pop="Zaniechaj"
                :action="route('patron-mode', ['client_id' => $patron->id, 'level' => 0])"
                class="primary"
            />
        </x-slot:actions>
    </x-shipyard::app.model.tile>
    @endforeach
</x-shipyard::app.section>
@endif

<x-shipyard::app.section
    title="Zapytania do wyceny"
    :icon="model_icon('requests')"
    style="grid-column: span 2;"
>
    <x-slot:actions>
        <x-shipyard::ui.button class="primary" :action="route('add-request')" icon="plus" label="Dodaj nowe" />
        <x-shipyard::ui.button :action="route('requests')" icon="chevron-double-right" label="Wszystkie" />
    </x-slot:actions>

    <x-shipyard::app.card id="dashboard-requests" />
</x-shipyard::app.section>

@if (count($data['showcases_missing']))
<x-shipyard::app.section title="Showcase'y do stworzenia"
    :icon="model_icon('showcases')"
    style="grid-column: span 2;"
    inner-class="flex down"
>
    @foreach ($data["showcases_missing"] as $quest)
    <x-shipyard::app.model.tile :model="$quest->song">
        <x-slot:actions>
            <x-shipyard::ui.button
                :action="route('admin.model.edit', ['model' => 'songs', 'id' => $quest->song_id])"
                icon="pencil"
                label="Edytuj"
            />
        </x-slot:actions>
    </x-shipyard::app.model.tile>
    @endforeach
</x-shipyard::app.section>
@endif

<x-shipyard::app.section
    title="Zlecenia w toku"
    :icon="model_icon('quests')"
    :extended="true"
>
    <x-shipyard::app.card id="dashboard-quests-ongoing" />
</x-shipyard::app.section>

<div class="flex down">
    <x-shipyard::app.section id="dashboard-requests"
        title="Grafik"
        icon="calendar"
        inner-class="flex down"
    >
        <x-slot:actions>
            <x-a href="{{ route('quests-calendar') }}">Wszystkie</x-a>
        </x-slot:actions>

        <x-calendar :click-days="false" :suggest="false" :with-today="true" />
    </x-shipyard::app.section>

    <x-shipyard::app.section
        title="Zlecenia czekające"
        icon="package-variant"
        :extended="false"
    >
        <x-shipyard::app.card id="dashboard-quests-waiting" />
    </x-shipyard::app.section>

    <x-shipyard::app.section id="recent"
        title="Ostatnie zmiany"
        icon="history"
        :extended="false"
    >
        <table>
            <thead>
                <tr>
                    <th>Zlecenie/Utwór</th>
                    <th>Klient</th>
                    <th>Status</th>
                    <th>Kiedy</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['recent'] as $change)
                <tr @class([
                    "ghost" => $change->re_quest?->status_id == $change->new_status_id,
                ])>
                    <td>
                        <a href="{{ route(($change->is_request) ? 'request' : 'quest', ['id' => $change->re_quest_id]) }}">
                            {{ (($change->is_request) ? $change->re_quest?->title : $change->re_quest?->song->title) ?? "utwór bez tytułu" }}
                        </a>
                        @unless ($change->is_request)
                        <small class="ghost">{{ $change->re_quest?->song->id }}</small>
                        @endunless
                    </td>
                    <td>
                    @if ($change->is_request)
                        @if ($change->re_quest?->user)
                            <a href="{{ route('admin.model.edit', ['model' => 'users', 'id' => $change->re_quest?->user?->id]) }}">{{ _ct_($change->re_quest?->user->display_name) }}</a>
                        @else
                            {{ _ct_($change->re_quest?->client_name) }}
                        @endif
                    @else
                        <a href="{{ route('admin.model.edit', ['model' => 'users', 'id' => $change->re_quest?->user->id]) }}">{{ _ct_($change->re_quest?->user->display_name) }}</a>
                    @endif
                    </td>
                    <td>
                        <x-phase-indicator-mini :status="$change->status" />

                        @if ($change->comment)
                        <span {{ Popper::pop($change->comment) }}>
                            <x-shipyard::app.icon name="comment" />
                        </span>
                        @endif
                    </td>
                    <td {{ Popper::pop($change->date) }}>
                        {{ $change->date->diffForHumans() }}
                    </td>
                </tr>
                @empty
                    <tr><td colspan=3 class="grayed-out">brak ostatnich zleceń</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-shipyard::app.section>

    <x-shipyard::app.section title="Raport sprzątacza" icon="broom">
        <x-slot:actions>
            @php
            $is_late = $data['janitor_date'] < now()->subDays(1);
            @endphp
            <strong @class(["accent error" => $is_late])>
                {{ $data['janitor_date']?->format("d.m") }}
                @if ($is_late)
                <x-shipyard::app.icon name="alert" />
                @endif
            </strong>
        </x-slot:actions>

        <table>
            <thead>
                <tr>
                    <th>Obiekt</th>
                    <th>Komentarz</th>
                    <th>Mail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['janitor_log'] as $i)
                <tr>
                    <td>
                        @if(is_object($i->subject))
                        <a href="{{ $i->subject->link_to }}">
                            @if($i->procedure === "re_quests")
                                <x-phase-indicator-mini :status="$i->subject->status" />
                                {{ $i->subject }}
                            @elseif($i->procedure === "safe")
                                <i class="fas fa-folder" @popper(Sejf)></i>
                                {{ $i->subject->title ?? "utwór bez tytułu" }}
                            @endif
                        </a>
                        @else
                        <span>{{ $i->subject }}</span>
                        @endif
                    </td>
                    <td>
                        @if(is_array($i->comment))
                        {{ $i->comment["comment"] }}
                        <x-phase-indicator-mini :status="$i->comment['status']" />
                        @else
                        {{ $i->comment }}
                        @endif
                    </td>
                    <td>
                        @switch($i->mailing)
                            @case(2)
                                <span class="accent success" @popper(mail wysłany)>
                                    <x-shipyard::app.icon name="email-fast" />
                                </span>
                                @break
                            @case(1)
                                <span class="accent danger" @popper(mail wysłany, ale wyślij wiadomość)>
                                    <x-shipyard::app.icon name="email-fast" />
                                </span>
                                @break
                            @case(0)
                                <span class="accent error" @popper(wyślij wiadomość)>
                                    <x-shipyard::app.icon name="email-off" />
                                </span>
                                @break
                        @endswitch
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan=5>
                        <span class="grayed-out">
                            <x-shipyard::app.icon name="bed" />
                            Sprzątacz dzisiaj śpi
                        </span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </x-shipyard::app.section>
</div>

</div>

<script defer>
const formdata_requests = new FormData();
formdata_requests.append("_token", "{{ csrf_token() }}");
formdata_requests.append("fltr[status][]", 1);
formdata_requests.append("fltr[status][]", 6);
formdata_requests.append("fltr[status][]", 96);

const formdata_quests_ongoing = new FormData();
formdata_quests_ongoing.append("_token", "{{ csrf_token() }}");
formdata_quests_ongoing.append("fltr[status][]", 11);
formdata_quests_ongoing.append("fltr[status][]", 12);
formdata_quests_ongoing.append("fltr[status][]", 13);
formdata_quests_ongoing.append("fltr[status][]", 14);
formdata_quests_ongoing.append("fltr[status][]", 16);
formdata_quests_ongoing.append("fltr[status][]", 26);
formdata_quests_ongoing.append("fltr[status][]", 96);
formdata_quests_ongoing.append("sort", "deadline");

const formdata_quests_waiting = new FormData();
formdata_quests_waiting.append("_token", "{{ csrf_token() }}");
formdata_quests_waiting.append("fltr[status][]", 15);
formdata_quests_waiting.append("fltr[status][]", 95);

let debounce_timer_1, debounce_timer_2, debounce_timer_3;
fetchComponent(
    `#dashboard-requests > .loader`,
    `{{ route('admin.model.list.filter', ['model' => 'requests']) }}`,
    { method: "POST", body: formdata_requests },
    [[`#dashboard-requests > .contents`, `html`]],
    (res) => { reapplyPopper(); },
    _debounce_timer = debounce_timer_1
);

fetchComponent(
    `#dashboard-quests-ongoing > .loader`,
    `{{ route('admin.model.list.filter', ['model' => 'quests']) }}`,
    { method: "POST", body: formdata_quests_ongoing },
    [[`#dashboard-quests-ongoing > .contents`, `html`]],
    (res) => { reapplyPopper(); },
    _debounce_timer = debounce_timer_2
);

fetchComponent(
    `#dashboard-quests-waiting > .loader`,
    `{{ route('admin.model.list.filter', ['model' => 'quests']) }}`,
    { method: "POST", body: formdata_quests_waiting },
    [[`#dashboard-quests-waiting > .contents`, `html`]],
    (res) => { reapplyPopper(); },
    _debounce_timer = debounce_timer_3
);
</script>

@else
<div @class(["grid but-mobile-down", "stagger-contents" => setting("animations_mode") >= 1]) style="--col-count: 2;">
    <x-shipyard::app.section
        title="Na tapecie"
        subtitle="Aktualne zlecenia i zapytania"
        :icon="model_icon('quests')"
        style="grid-column: span 2;"
    >
        <div class="flex down">
            @forelse (
                $data["quests_review"]
                    ->merge($data["quests_ongoing"])
                    ->merge($data["requests"])
            as $item)
                @if ($item instanceof \App\Models\Request)
                <x-requests.tile :request="$item" />
                @else
                <x-quests.tile :quest="$item" />
                @endif
            @empty
            <p class="grayed-out">brak aktywnych zleceń</p>
            @endforelse
        </div>
    </x-shipyard::app.section>

    <x-shipyard::app.section id="who-am-i"
        title="Moje statystyki"
        icon="chart-bar"
        @class(["sc-line"])
    >
        <table>
            <tr>
                <td>Ukończonych zleceń</td>
                <td>
                    {{ $data["quests_total"] }}
                    <x-shipyard::stats.counter :rank="$data['quests_total']" style="military" />
                </td>
            </tr>
            <tr>
                <td>Status klienta</td>
                <td>
                    @if (Auth::user()->trust == -1)
                    <span class="accent error"><x-shipyard::app.icon name="ninja" /></span> niezaufany
                    @elseif (Auth::user()->is_veteran)
                    <span><x-shipyard::app.icon name="shield-account" /></span> stały klient
                    @else
                    <span><x-shipyard::app.icon name="account" /></span> klient początkujący
                    (<i>pozostało zleceń: {{ setting("msznm_veteran_from") - $data["quests_total"] }}</i>)
                    @endif
                </td>
            </tr>
            @if (Auth::user()->is_patron)
            <tr>
                <td>Pomoc w reklamie</td>
                <td>odnotowana</td>
            </tr>
            @endif
            <tr>
                <td>Łącznie zniżek</td>
                <td>
                    {{
                        Auth::user()->special_prices ? "spersonalizowany cennik"
                        : (
                            (Auth::user()->is_veteran) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
                            +
                            (Auth::user()->is_patron) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
                        )*100 . "%"
                    }}
                </td>
            </tr>
        </table>

        @if (Auth::user()->trust == -1)
        <x-shipyard::app.h :lvl="2" icon="ninja" class="accent error">Jesteś na czarnej liście</x-shipyard::app.h>
        <p>
            Z powodu nieopłaconych przez bardzo długi czas zleceń, ograniczyłem możliwości korzystania ze strony.
            Do momentu ich opłacenia nie możesz przeglądać udostępnionych plików.
        </p>

        <x-shipyard::app.h :lvl="3" :icon="model_icon('quests')" class="accent error">Nieopłacone zlecenia</x-shipyard::app.h>
        <table>
            <thead>
                <tr>
                    <th>Tytuł</th>
                    <th>Kwota</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data["unpaids"] as $quest)
                <tr>
                    <td>
                        <a href="{{ route('quest', ['id' => $quest->id]) }}">
                        {{ $quest->song }}
                        </a>
                    </td>
                    <td>{{ as_pln($quest->payment_remaining) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if ($data["quests_total"] && !Auth::user()->is_patron && Auth::user()->helped_showcasing != 1)
        <x-shipyard::app.h :lvl="2" icon="seal" class="showcase-highlight">Oceń naszą współpracę</x-shipyard::app.h>
        <p>
            Recenzje pomagają mi pozyskiwać nowych klientów.
            Jeśli i Tobie przypadły do gustu efekty moich prac,
            możesz dać o tym znać innym i uzyskać <strong class="showcase-highlight">dodatkowe 5% zniżki na kolejne zlecenia</strong>!
        </p>

        <x-button
            label="Przejdź do mojego fanpage'a" icon="open-in-new" target="_blank"
            action="https://www.facebook.com/muzykaszytanamiarepl/reviews"
            />
        <p>
            Po wystawieniu opinii kliknij przycisk poniżej – wtedy sprawdzę opinię i przyznam zniżkę.
        </p>
        <x-shipyard::app.card icon="alert" class="accent danger">
            Zwróć uwagę, żeby widoczność posta była ustawiona na <strong>Wszyscy</strong>.
            Inaczej nie będę mógł stwierdzić, że faktycznie napisał{{ client_polonize(Auth::user()->display_name)['kobieta'] ? 'aś' : 'eś' }} opinię.
        </x-shipyard::app.card>

        <x-shipyard::ui.button
            label="Właśnie wystawił{{ client_polonize(Auth::user()->display_name)['kobieta'] ? 'am' : 'em' }} opinię" icon="signature-freehand"
            action="{{ route('patron-mode', ['client_id' => Auth::id(), 'level' => 1]) }}"
            class="primary"
        />
        @endif
    </x-shipyard::app.section>

    <x-shipyard::app.section id="dashboard-finances"
        title="Finanse"
        subtitle="Dane do przelewu i suma zobowiązań"
        :icon="model_icon('prices')"
        :extended="$data['unpaids']->sum('payment_remaining') > 0"
        inner-class="flex down"
    >
        @if (count($data["unpaids"]))
        <x-shipyard::app.card
            title="Zlecenia do opłacenia"
            :icon="model_icon('money-transactions')"
        >
            <table>
                <thead>
                    <tr>
                        <th>Zlecenie</th>
                        <th>ID zlecenia</th>
                        <th>Kwota</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['unpaids'] as $quest)
                    <tr>
                        <td>
                            <a href="{{ route('quest', ['id' => $quest->id]) }}">
                            {{ $quest->song }}
                            </a>
                        </td>
                        <td><code>{{ $quest->id }}</code></td>
                        <td>{{ as_pln($quest->payment_remaining) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2">Suma zobowiązań</th>
                        <th>{{ as_pln($data['unpaids']->sum('payment_remaining')) }}</th>
                    </tr>
                </tfoot>
            </table>
        </x-shipyard::app.card>
        @endif

        <x-shipyard::app.card
            title="Stan konta"
            icon="safe-square"
        >
            <p>Aktualny stan konta: <strong class="accent primary">{{ as_pln(Auth::user()->budget) }}</strong></p>
            <x-tutorial>
                Jeśli zdarzy Ci się wpłacić więcej, niż to było planowane, to odnotuję tę różnicę i wpiszę ją na poczet przyszlych zleceń.
            </x-tutorial>
        </x-shipyard::app.card>

        <x-shipyard::app.card
            title="Dane do przelewu"
            icon="card-account-details-outline"
        >
            <table>
                <tr>
                    <td>Odbiorca</td>
                    <th>Wojciech Przybyła</th>
                </tr>
                <tr>
                    <td>Adres</td>
                    <th>Łąkie 62, 62-068 Łąkie</th>
                </tr>
                <tr>
                    <td>Numer konta</td>
                    <th>58 1090 1607 0000 0001 5333 1539</th>
                </tr>
            </table>
            <p>
                W tytule proszę o wpisanie <strong>ID zlecenia, które opłacasz</strong> dla łatwiejszej identyfikacji wpłaty.
                Więcej szczegółów znajdziesz w konkretnym zleceniu{{ (count($data["unpaids"]) ? ' i w zestawieniu powyżej' : '') }}.
            </p>
            @if($data["unpaids"]->filter(fn($quest) => $quest->delayed_payment?->gte(Carbon\Carbon::today()))->count())
            <p class="yellowed-out">
                <i class="fas fa-triangle-exclamation"></i>
                Posiadasz nieopłacone zlecenia z opóźnionym terminem płatności.
                Zanim dokonasz przelewu, zwróć uwagę, czy nie wykonujesz go zbyt wcześnie.
            </p>
            @endif
        </x-shipyard::app.card>
    </x-shipyard::app.section>
</div>

<div class="flex right center">
    @unless (Auth::user()->trust == -1)
    <x-shipyard::ui.button
        label="Złóż zapytanie o podkład/nuty"
        icon="send"
        action="none"
        onclick="openModal('send-podklady-request', {
            client_id: {{ Auth::user()?->id ?? 'null' }},
            client_name: '{{ Auth::user()?->display_name }}' || null,
            email: '{{ Auth::user()?->can_be_mailed ? Auth::user()->email : null }}' || null,
            phone: '{{ Auth::user()?->phone }}' || null,
            other_medium: '{{ Auth::user()?->other_medium }}' || null,
            contact_preference: '{{ Auth::user()?->contact_preference }}' || 'email',
        })"
        class="primary"
    />
    @endunless
</div>

@endif
