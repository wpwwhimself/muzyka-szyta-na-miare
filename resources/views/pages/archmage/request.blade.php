@extends("shipyard::layouts.admin")
@section("title", $request->full_title)
@section("subtitle", "Zapytanie")

@section('content')

<x-shipyard::app.form method="POST" :action="route('mod-request-back')">
    <x-slot:actions>
        <x-a href="{!! route('add-request', [
            'client' => $request->client_id,
            'client_new' => implode('*', [
                $request->client_name,
                $request->email,
                $request->phone,
                $request->other_medium,
                $request->contact_preference,
            ])
        ]) !!}" icon="plus">Dodaj kolejne</x-a>
    </x-slot:actions>

    <x-phase-indicator :status-id="$request->status_id" />

    <div id="phases" class="archmage-quest-phases flex right center middle nowrap">
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
            <x-shipyard::ui.button
                :action="abs($status_id) == 9
                    ? 'none'
                    : 'submit'"
                :onclick="abs($status_id) == 9
                    ? 'openModal(\'confirm-quote\', {
                        id: `'.$request->id.'`,
                        is_priority: '.intval($status_id < 0).',
                        confirm_song: true,
                        confirm_price: true,
                        confirm_deadline: true,
                        confirm_delayed_payment: true,
                    })'
                    : null"
                name="new_status"
                :icon="$new_status->icon"
                :value="$status_id"
                :pop="$label"
                :class="$status_id < 0 ? 'priority' : 'primary'"
            />
            @endif
        @endforeach
    </div>

    @if ($request->quest_id)
    <h3>Zlecenie przepisane z numerem <a class="mono" href="{{ $request->quest->link_to }}">{{ $request->quest_id }}</a></h3>
    @endif

    <div class="grid but-mobile-down" style="--col-count: 2;">
        @if (in_array($request->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
        <x-quest-history :quest="$request" :extended="true" style="grid-column: span 2;" />
        @endif

        <x-extendo-block key="client"
            :icon="model_icon('users')"
            title="Dane klienta"
            :subtitle="$request->user?->name_and_badges ?? $request->client_name"
            :extended="in_array($request->status_id, [1, 6])"
        >
            <x-slot:buttons>
                @if ($request->client_id)
                <x-shipyard::ui.button
                    :action="route('admin.model.edit', ['model' => 'users', 'id' => $request->client_id])"
                    :icon="model_icon('users')"
                    pop="Szczegóły"
                    target="_blank"
                />
                <x-shipyard::ui.button
                    :action="route('admin.model.list', ['model' => 'quests', 'fltr[client]' => $request->client_id])"
                    :icon="model_icon('quests')"
                    pop="Zlecenia"
                    target="_blank"
                />
                @else
                <x-shipyard::ui.button
                    icon="link"
                    pop="Przypisz klienta"
                    action="none"
                    onclick="openModal('select-user-to-request', {
                        request_id: '{{ $request->id }}',
                        query: document.querySelector(`#client_name`).value,
                        mode: 'manual',
                    }, {}, () => {
                        document.querySelector(`#query`).focus();
                    })"
                    class="tertiary"
                />
                @endif
            </x-slot:buttons>

            @if ($request->status_id == 1 && $request->client_name && !$request->client_id)
            <x-shipyard::app.card id="binder-suggestions-for-user"
                title="Podobni istniejący klienci"
                :icon="model_icon('users')"
                inner-class="flex down"
            >
                <div class="suggestions"></div>
                <div class="flex right center middle">
                    <x-shipyard::ui.button
                        icon="link"
                        label="Przypisz wybranego klienta"
                        class="primary hidden"
                        action="none"
                        onclick="bindUserToRequest(`{{ $request->id }}`, document.forms[0].elements.client_id.value)"
                    />
                </div>
                <script defer>
                    const binder_user_container = document.querySelector(`#binder-suggestions-for-user`);

                    function bindUserToRequest(request_id, user_id) {
                        if (!user_id) {
                            popToast("error", "Wybierz klienta");
                            return;
                        }

                        binder_user_container.querySelector(`.loader`).classList.remove(`hidden`);
                        fetchWithUser(`/requests/select/user`, {
                            method: "POST",
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                request_id: request_id,
                                client_id: user_id,
                                mode: 'semi-auto',
                            }),
                        })
                            .then(res => res.json())
                            .then(res => {
                                if (!res.success) throw new Error(res.message);
                                location.reload();
                            })
                            .catch(err => {
                                console.error(err);
                            });
                    }

                    binder_user_container.querySelector(`.loader`).classList.remove(`hidden`);
                    fetchPublic(`/api/lookup/users?query={{ $request->client_name }}`)
                        .then(res => res.text())
                        .then(table => {
                            document.querySelector(`#binder-suggestions-for-user .contents .suggestions`).innerHTML = table;
                            document.querySelector(`#binder-suggestions-for-user .button[data-label*="Przypisz"]`).classList.remove(`hidden`);
                        })
                        .finally(() => {
                            binder_user_container.querySelector(`.loader`).classList.add(`hidden`);
                        });
                </script>
            </x-shipyard::app.card>
            @endif

            <input type="hidden" name="client_id" value="{{ $request->client_id }}" />

            @foreach ([
                "client_name",
                "email",
                "phone",
                "other_medium",
                "contact_preference",
            ] as $field_name)
            <x-shipyard::ui.field-input :model="$request" :field-name="$field_name" />
            @endforeach
        </x-extendo-block>

        <x-extendo-block key="song"
            :icon="model_icon('songs')"
            title="Dane utworu"
            :subtitle="$request->full_title"
            :extended="in_array($request->status_id, [1, 6, 96])"
            :warning="$warnings['song']"
        >
            <x-slot:buttons>
                @if ($request->song_id)
                <x-shipyard::ui.button
                    :action="route('admin.model.list', ['model' => 'songs', 'fltr[id]' => $request->song_id])"
                    :icon="model_icon('songs')"
                    pop="Szczegóły"
                    target="_blank"
                />
                @else
                <x-shipyard::ui.button
                    icon="link"
                    pop="Przypisz utwór"
                    action="none"
                    onclick="openModal('select-song-to-request', {
                        request_id: '{{ $request->id }}',
                        query: document.querySelector(`#title`).value,
                        mode: 'manual',
                    }, {}, () => {
                        document.querySelector(`#query`).focus();
                    })"
                    class="tertiary"
                />
                @endif
            </x-slot:buttons>

            @if ($request->status_id == 1 && $request->title && !$request->song_id)
            <x-shipyard::app.card id="binder-suggestions-for-song"
                title="Podobne istniejące utwory"
                :icon="model_icon('songs')"
                inner-class="flex down"
            >
                <div class="suggestions"></div>
                <div class="flex right center middle">
                    <x-shipyard::ui.button
                        icon="link"
                        label="Przypisz wybrany utwór"
                        class="primary hidden"
                        action="none"
                        onclick="bindSongToRequest(`{{ $request->id }}`, document.forms[0].elements.song_id.value)"
                    />
                </div>
                <script defer>
                    const binder_song_container = document.querySelector(`#binder-suggestions-for-song`);

                    function bindSongToRequest(request_id, song_id) {
                        if (!song_id) {
                            popToast("error", "Wybierz utwór");
                            return;
                        }

                        binder_song_container.querySelector(`.loader`).classList.remove(`hidden`);
                        fetchWithUser(`/requests/select/song`, {
                            method: "POST",
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                request_id: request_id,
                                song_id: song_id,
                                mode: 'semi-auto',
                            }),
                        })
                            .then(res => res.json())
                            .then(res => {
                                if (!res.success) throw new Error(res.message);
                                location.reload();
                            })
                            .catch(err => {
                                console.error(err);
                            });
                    }

                    binder_song_container.querySelector(`.loader`).classList.remove(`hidden`);
                    fetchPublic(`/api/lookup/songs?query={{ urlencode($request->title) }}`)
                        .then(res => res.text())
                        .then(table => {
                            document.querySelector(`#binder-suggestions-for-song .contents .suggestions`).innerHTML = table;
                            document.querySelector(`#binder-suggestions-for-song .button[data-label*="Przypisz"]`).classList.remove(`hidden`);
                        })
                        .finally(() => {
                            binder_song_container.querySelector(`.loader`).classList.add(`hidden`);
                        });
                </script>
            </x-shipyard::app.card>
            @endif

            <input type="hidden" name="song_id" value="{{ $request->song_id }}" />

            @foreach ([
                "title",
                "artist",
                "link",
            ] as $field_name)
                <x-shipyard::ui.field-input :model="$request" :field-name="$field_name" />
                @if ($field_name == "link")
                <x-link-interpreter :raw="$request->$field_name" />
                @endif
            @endforeach

            <x-shipyard::ui.connection-input :model="$request" connection-name="genre" />
            <x-shipyard::ui.connection-input :model="$request" connection-name="composition" />
            <x-shipyard::ui.connection-input :model="$request" connection-name="quest_type" />

            @foreach ([
                "wishes",
                "hard_deadline",
            ] as $field_name)
                <x-shipyard::ui.field-input :model="$request" :field-name="$field_name" />
                @if ($field_name == "wishes" && $request->user?->default_wishes)
                <x-shipyard::ui.button
                    :icon="model_field_icon('users', 'default_wishes')"
                    label="Wklej domyślne życzenia"
                    action="none"
                    onclick="document.querySelector(`#wishes`).value = `{{ $request->user->default_wishes }}`;"
                    class="tertiary"
                />
                @endif
            @endforeach
        </x-extendo-block>

        <x-extendo-block key="quote"
            :header-icon="model_icon('prices')"
            title="Wycena"
            :extended="true"
        >
            <div>
                @if (!$request->price_code)
                <div class="flex down center">
                    @if ($request->song)
                    <span>Sugerowana wycena: {{ $request->song->price_code }}</span>
                    @endif

                    @if ($request->user?->special_prices)
                    <span>Klient ma specjalne warunki cenowe: {{ $request->user->special_prices }}</span>
                    @endif
                </div>
                @endif

                <x-shipyard::ui.field-input :model="$request" field-name="price_code"
                    onchange="reQuestCalcPrice(event.target.value, {{ $request->client_id ?? 'null' }});"
                />
                <x-re_quests.price-summary :model="$request" />

                @if ($request->user?->budget && in_array($request->status_id, [1, 5, 6]))
                <span class="accent {{ $request->user->budget >= $request->price ? 'success' : 'danger' }}">
                    <x-shipyard::app.icon name="safe-square" />
                    Budżet w wysokości <b>{{ _c_(as_pln($request->user->budget)) }}</b> automatycznie
                    <br>
                    pokryje
                    @if ($request->user->budget >= $request->price)
                    całą kwotę zlecenia
                    @else
                    część kwoty zlecenia
                    @endif
                </span>
                @endif

                <x-re_quests.monthly-payment-limit :model="$request" />
                <x-shipyard::ui.field-input :model="$request" field-name="delayed_payment" />

                <div>
                    @if (in_array($request->status_id, [1, 6, 96]))
                    <div class="folding"><x-calendar /></div>
                    <script>
                    function handleCalendarClick(date) {
                        if (document.querySelector("#deadline")) {
                            document.querySelector("#deadline").value = date;
                        }
                    }
                    </script>
                    @endif
                    <x-shipyard::ui.field-input :model="$request" field-name="deadline" />
                </div>
            </div>
        </x-extendo-block>

        @unless (in_array($request->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
        <x-quest-history :quest="$request" :extended="true" />
        @endunless
    </div>
</x-shipyard::app.form>

@endsection
