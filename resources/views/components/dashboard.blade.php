@props([
    "user",
    "data",
])

@if (is_archmage())
<div @class(["grid but-mobile-down", "stagger-contents" => setting("animations_mode") >= 1]) style="--col-count: 2;">

@if (count($data["patrons_adepts"]) > 0)
<x-section id="patrons-adepts"
    title="Potencjalni patroni"
    icon="seal"
    style="grid-column: span 2;"
>
    <x-slot name="buttons">
        <x-a href="https://www.facebook.com/muzykaszytanamiarepl/reviews" target="_blank">Recenzje</x-a>
    </x-slot>

    <table>
        <thead>
            <th>Klient</th>
            <th>Decyzja</th>
        </thead>
        <tbody>
            @foreach ($patrons_adepts as $patron)
            <tr>
                <td>
                    <a href="{{ route('admin.model.edit', ['model' => 'users', 'id' => $patron->id]) }}">{!! $patron !!}</a>
                </td>
                <td>
                    <x-button label="" icon="check" action="{{ route('patron-mode', ['client_id' => $patron->id, 'level' => 2]) }}" :small="true" />
                    <x-button label="" icon="x" action="{{ route('patron-mode', ['client_id' => $patron->id, 'level' => 0]) }}" :small="true" />
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-section>
@endif

<x-extendo-block key="requests"
    title="Zapytania"
    :header-icon="model_icon('requests')"
    :extended="$data['requests']->filter(fn ($r) => in_array($r->status_id, [1, 6, 96]))->count() > 0 || $data['requests']->count() === 0"
    style="grid-column: span 2;"
>
    <x-slot name="buttons">
        <x-shipyard::stats.counter
            :rank="$data['requests']->count()"
            label="Liczba zapytań"
            style="lines"
        />

        <x-shipyard::ui.button class="primary" :action="route('add-request')" icon="plus" label="Dodaj nowe" />
        <x-a href="{{ route('requests') }}">Wszystkie</x-a>
    </x-slot>

    <div class="flex down">
        @forelse ($data['requests'] as $request)
        <x-requests.tile :request="$request" />
        @empty
        <p class="grayed-out"><i class="fas fa-check"></i> brak aktywnych zapytań</p>
        @endforelse
    </div>
</x-extendo-block>

@if (count($data['showcases_missing']))
<x-section title="Showcase'y do stworzenia" :icon="model_icon('showcases')" style="grid-column: span 2;">
    <table>
        <thead>
            <tr>
                <th>ID questa</th>
                <th>ID utworu</th>
                <th>Utwór</th>
                <th>Co trzeba zrobić</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['showcases_missing'] as $quest)
            <tr>
                <td><a href="{{ route('quest', ['id' => $quest->id]) }}">{{ $quest->id }}</a></td>
                <td><a href="{{ route('admin.model.edit', ['model' => 'songs', 'id' => $quest->song->id]) }}">{{ $quest->song->id }}</a></td>
                <td>{{ $quest->song->full_title }}</td>
                <td>
                    @if ($quest->song->has_recorded_reel)
                        @if ($quest->song->has_original_mv)
                        <span @popper(Rolka z teledyskiem)><x-shipyard::app.icon name="video-vintage" /></span>
                        @else
                        <span @popper(Rolka)><x-shipyard::app.icon name="movie-roll" /></span>
                        @endif
                    @endif

                    @if (!$quest->song->has_showcase_file)
                    <span @popper(Krótki showcase)><x-shipyard::app.icon name="tshirt-crew" /></span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-section>
@endif

<x-shipyard::app.section
    title="Zlecenia w toku"
    :icon="model_icon('quests')"
    :extended="true"
>
    <div class="flex down">
        @foreach ($data['quests_ongoing']->groupBy("client_id")
            ->sortBy(fn ($q) => min($q->min("deadline")?->format("Ymd") ?? "99999999", $q->min("hard_deadline")?->format("Ymd") ?? "99999999"))
        as $client_id => $clients_quests)
        <div class="grid but-mobile-down animatable highlight" style="grid-template-columns: auto 1fr;">
            @php
            $client = $clients_quests->first()->user;
            @endphp

            <div class="flex down but-mobile-right" style="row-gap: 0;">
                <span>{{ $client }}</span>
                <span>{!! $client->display_subtitle !!}</span>
            </div>
            <div class="flex down no-gap">
                @foreach ($clients_quests as $quest)
                <span style="text-align: right;"
                    @if ($quest->hard_deadline?->isPast()) class="accent error" @endif
                >
                    <span {{ Popper::pop($quest->quest_type->type) }}>
                        <x-shipyard::app.icon :name="$quest->quest_type->icon" />
                    </span>
                    <a href="{{ route('quest', ['id' => $quest->id]) }}">{{ $quest->song->title ?? "bez tytułu" }}</a>
                    {!! $quest->status->name_and_label !!}
                </span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</x-shipyard::app.section>

<x-section id="dashboard-requests"
    title="Grafik"
    icon="calendar"
    :extended="true"
    scissors
>
    <x-slot name="buttons">
        <x-a href="{{ route('quests-calendar') }}">Wszystkie</x-a>
    </x-slot>

    <x-calendar :click-days="false" :suggest="false" :with-today="true" />
</x-section>

<x-section id="dashboard-quests"
    title="Zlecenia w toku (klasycznie)"
    :icon="model_icon('quests')"
    :extended="false"
>
    <x-slot:buttons>
        <x-shipyard::stats.counter
            :rank="$data['quests_ongoing']->count()"
            label="Liczba zleceń"
            style="lines"
        />
    </x-slot:buttons>

    <div class="flex down">
        @forelse ($data['quests_ongoing'] as $key => $quest)
        <x-quests.tile :quest="$quest" :no="$key + 1" />
        @empty
        <p class="grayed-out"><i class="fas fa-check"></i> brak aktywnych zleceń</p>
        @endforelse
    </div>
</x-section>

<x-section id="dashboard-quests"
    title="Zlecenia czekające"
    icon="package-variant"
    :extended="false"
>
    <x-slot:buttons>
        <x-shipyard::stats.counter
            :rank="$data['quests_review']->count()"
            label="Liczba zleceń"
            style="lines"
        />
    </x-slot:buttons>

    <div class="flex down">
        @forelse ($data['quests_review'] as $key => $quest)
        <x-quests.tile :quest="$quest" :no="$key + 1" />
        @empty
        <p class="grayed-out">brak aktywnych zleceń</p>
        @endforelse
    </div>
</x-section>

<x-section id="recent"
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
</x-section>

<x-section title="Raport sprzątacza" icon="broom">
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
                            {{ $i->subject->song?->title ?? $i->subject->title ?? "utwór bez tytułu" }}
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
                    <x-phase-indicator-mini :status="\App\Models\Status::find($i->comment['status_id'])" />
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
</x-section>

</div>

@else
<div class="grid stagger-contents" style="--col-count: 2;">
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

    <x-section id="who-am-i"
        title="Moje dane"
        :subtitle="Auth::user()"
        :icon="model_icon('users')"
        scissors
    >
        <x-sc-scissors />

        <div class="hint-table">
            <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
            <div class="positions">
                <span>Ukończonych zleceń</span>
                <span>
                    {{ $data["quests_total"] }}
                    <x-shipyard::stats.counter :rank="$data['quests_total']" style="military" />
                </span>

                <span>Status klienta</span>
                <span>
                    @if (Auth::user()->trust == -1)
                    <span class="error"><x-shipyard::app.icon name="ninja" /></span> niezaufany
                    @elseif (Auth::user()->is_veteran)
                    <span><x-shipyard::app.icon name="shield-account" /></span> stały klient
                    @else
                    <span><x-shipyard::app.icon name="account" /></span> klient początkujący<br>
                    <i>pozostało zleceń: {{ setting("msznm_veteran_from") - $data["quests_total"] }}</i>
                    @endif
                </span>

                @if (Auth::user()->is_patron)
                <span>Pomoc w reklamie</span>
                <span>odnotowana</span>
                @endif

                <span>Łącznie zniżek</span>
                <span>
                    {{
                        Auth::user()->special_prices ? "spersonalizowany cennik"
                        : (
                            (Auth::user()->is_veteran) * floatval(DB::table("prices")->where("indicator", "=")->value("price_".pricing(Auth::id())))
                            +
                            (Auth::user()->is_patron) * floatval(DB::table("prices")->where("indicator", "-")->value("price_".pricing(Auth::id())))
                        )*100 . "%"
                    }}
                </span>
            </div>
        </div>

        @if (Auth::user()->trust == -1)
        <br>
        <div class="section-header accent error">
            <h1><x-shipyard::app.icon name="ninja" /> Jesteś na czarnej liście!</h1>
        </div>
        <p>
            Z powodu nieopłaconych przez bardzo długi czas projektów, ograniczyłem możliwości korzystania ze strony.
            Do momentu ich opłacenia nie możesz przeglądać udostępnionych plików.
        </p>
        <h2 class="error">Nieopłacone zlecenia</h2>
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
        <br>
        <div class="section-header showcase-highlight">
            <h1><x-shipyard::app.icon name="seal" /> Oceń naszą współpracę</h1>
        </div>
        <p>
            Recenzje pomagają mi pozyskiwać nowych klientów.
            Jeśli i Tobie przypadły do gustu efekty moich prac,
            możesz dać o tym znać innym i uzyskać <strong class="showcase-highlight">dodatkowe 5% zniżki na kolejne zlecenia</strong>!
        </p>
        <form>
            <x-button
                label="Przejdź do mojego fanpage'a" icon="open-in-new" target="_blank"
                action="https://www.facebook.com/muzykaszytanamiarepl/reviews"
                />
            <p>
                Po wystawieniu opinii kliknij przycisk poniżej – wtedy sprawdzę opinię i przyznam zniżkę.
                <x-warning>
                    Zwróć uwagę, żeby widoczność posta była ustawiona na <strong>Wszyscy</strong>.
                    Inaczej nie będę mógł stwierdzić, że faktycznie napisał{{ client_polonize(Auth::user()->display_name)['kobieta'] ? 'aś' : 'eś' }} opinię.
                </x-warning>
            </p>
            <x-shipyard::ui.button
                label="Właśnie wystawił{{ client_polonize(Auth::user()->display_name)['kobieta'] ? 'am' : 'em' }} opinię" icon="signature"
                action="{{ route('patron-mode', ['client_id' => Auth::id(), 'level' => 1]) }}"
                class="primary"
            />
        </form>
        @endif
    </x-section>

    <x-section id="dashboard-finances"
        title="Finanse"
        subtitle="Dane do przelewu i suma zobowiązań"
        :icon="model_icon('prices')"
        :extended="$data['unpaids']->sum('payment_remaining') > 0"
        :warning="[
            'Niektóre ze zleceń, które musisz opłacić, posiadają opóźniony termin płatności' => $data['unpaids']->filter(fn($quest) => $quest->delayed_payment?->gte(Carbon\Carbon::today()))->count(),
        ]"
    >
        <h2 @class([
            "error" => Auth::user()->trust == -1,
        ])>
            Do zapłacenia za zlecenia
        </h2>
        <div class="hint-table">
            <style>.hint-table div{ grid-template-columns: 1fr 1fr; }</style>
            <div class="positions">
                <span>Zaakceptowane</span>
                <span>{{ as_pln($data["unpaids"]->filter(fn ($q) => in_array($q->status_id, [17, 19]))->sum("payment_remaining")) }}</span>

                <span>Wszystkie</span>
                <span>{{ as_pln($data["unpaids"]->sum("payment_remaining")) }}</span>
            </div>
        </div>

        <h2>
            Stan konta:
            {{ as_pln(Auth::user()->budget) }}
        </h2>
        <x-tutorial>
            Jeśli zdarzy Ci się wpłacić więcej, niż to było planowane, to odnotuję tę różnicę i wpiszę ją na poczet przyszlych zleceń.
        </x-tutorial>

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
                W tytule proszę o wpisanie <strong>ID zlecenia</strong> (np. <code>P23-5X</code>) dla łatwiejszej identyfikacji wpłaty.
                Więcej szczegółów znajdziesz w konkretnym zleceniu.
            </p>
            @if($data["unpaids"]->filter(fn($quest) => $quest->delayed_payment?->gte(Carbon\Carbon::today()))->count())
            <p class="yellowed-out">
                <i class="fas fa-triangle-exclamation"></i>
                Posiadasz nieopłacone zlecenia z opóźnionym terminem płatności.
                Zanim dokonasz przelewu, zwróć uwagę, czy nie wykonujesz go zbyt wcześnie.
            </p>
            @endif
        </x-shipyard::app.card>
    </x-section>
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
