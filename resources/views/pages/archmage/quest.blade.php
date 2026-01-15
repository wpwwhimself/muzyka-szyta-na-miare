@extends('layouts.app')
@section("title", $quest->song->full_title)
@section("subtitle", "Zlecenie")

@section('content')

<x-phase-indicator :status-id="$quest->status_id" />
<div class="archmage-quest-phases flex right center middle">
    @foreach ([
        ["Wprowadzenie/odrzucenie zmian", 11, [21]],
        ["Rozpocznij", 12, [11, 13, 14, 16, 26, 96]],
        ["Oddaj", 15, [11, 12, 13, 14, 16, 26, 96]],
        ["Doprecyzuj", 95, [11, 12, 13, 14, 16, 26, 96]],
        ["Klient odpowiada", 96, [95]],
        ["Zawieś", 13, [11, 12, 14, 16, 96]],
        ["Klient akceptuje", $quest->files_ready ? 19 : 14, [15, 31, 96]],
        ["Klient cofa", 16, [15]],
        ["Klient odrzuca", 18, [11, 12, 13, 14, 15, 16, 31, 21, 95, 96]],
        ["Kient przywraca", 26, [17, 18, 19]],
        ["Kient prosi o zmiany", 21, [11]],
        ["Wygaś", 17, [13, 15]],
        ["Popraw ostatni komentarz", $quest->status_id, [$quest->status_id]],
    ] as [$label, $status_id, $show_on_statuses])
        @if (in_array($quest->status_id, $show_on_statuses))
        @php
        $nomail = (!$quest->user->notes->email && in_array($status_id, [15, 95]));
        $new_status = \App\Models\Status::find(abs($status_id));
        @endphp
        <x-shipyard.ui.button
            :icon="$new_status->icon"
            :label="$label . ($nomail ? ' (bez maila)' : '')"
            action="none"
            onclick="openModal('quest-change-status', {
                quest_id: '{{ $quest->id }}',
                status_id: {{ $status_id }},
            })"
            class="tertiary"
        />
        @endif
    @endforeach
</div>

<div class="grid but-mobile-down" style="--col-count: 2;">
    @if (in_array($quest->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
    <x-quest-history :quest="$quest" :extended="true" style="grid-column: span 2;" />
    @endif

    <div class="flex down">
        <x-extendo-block key="quest"
            :header-icon="model_icon('songs')"
            title="Utwór"
            :subtitle="$quest->song_id . ' // ' . $quest->song->full_title"
            :extended="true"
        >
            @php $song = $quest->song; @endphp

            <x-slot:buttons>
                <x-quest-type :type="$song->type" />
                <x-shipyard.ui.button
                    pop="Edytuj utwór"
                    :icon="model_icon('songs')"
                    :action="route('song-edit', ['id' => $quest->song_id])"
                />
                <x-shipyard.ui.button
                    pop="Edytuj zlecenie"
                    :icon="model_icon('quests')"
                    :action="route('admin.model.edit', ['model' => 'quests', 'id' => $quest->id])"
                />
            </x-slot:buttons>

            <div class="grid but-halfsize-down" style="--col-count: 2;">
                <x-shipyard.ui.connection-input :model="$song" connection-name="genre" dummy />

                @foreach ([
                    "title",
                    "artist",
                    "link",
                    // "has_recorded_reel",
                    // "has_original_mv",
                ] as $field_name)
                <x-shipyard.ui.field-input :model="$song" :field-name="$field_name" dummy />
                @if ($field_name == "link")
                <x-link-interpreter :raw="$song->$field_name" />
                @endif
                @endforeach

                <x-shipyard.ui.field-input :model="$quest" field-name="wishes" dummy />
            </div>
        </x-extendo-block>

        <x-extendo-block key="quote"
            :header-icon="model_icon('prices')"
            title="Wycena"
            :subtitle="implode(' // ', array_filter([
                _c_(as_pln($quest->price)),
                'do '.$quest->deadline?->format('d.m.Y'),
                $quest->paid ? '🟢' : ($quest->payments_sum > 0 ? '🟡' : null)
            ], fn($val) => !is_null($val)))"
            :warning="$warnings['quote']"
            :extended="!$quest->paid"
        >
            <x-slot:buttons>
                <x-shipyard.ui.button
                    icon="cash-edit"
                    pop="Zmień wycenę"
                    action="none"
                    onclick="openModal('quest-quote-update', {
                        id: '{{ $quest->id }}',
                        price_code_override: '{{ $quest->price_code_override }}',
                        deadline: '{{ $quest->deadline?->format('Y-m-d') }}',
                    })"
                    class="tertiary"
                />
            </x-slot:buttons>

            <x-re_quests.price-summary :model="$quest" />
            <div class="grid but-halfsize-down" style="--col-count: 2;">
                @foreach ([
                    "deadline",
                    "hard_deadline",
                ] as $field_name)
                    <x-shipyard.ui.field-input :model="$quest" :field-name="$field_name" dummy />
                @endforeach
            </div>
            <x-quests.payments-bar :quest="$quest" />
            <x-quests.invoices :quest="$quest" />
            <x-quests.costs :quest="$quest" />
        </x-extendo-block>
    </div>

    <div class="flex down">
        <x-extendo-block key="files"
            :header-icon="model_icon('files')"
            title="Pliki"
            :extended="!in_array($quest->status_id, [11])"
            :warning="$warnings['files']"
            scissors
        >
            <x-slot:buttons>
                @if ($quest->user->notes->external_drive)
                <x-shipyard.ui.button
                    :action="$quest->user->notes->external_drive"
                    :icon="model_field_icon('user_notes', 'external_drive')"
                    pop="Przejdź do chmury"
                    target="_blank"
                />
                <form action="{{ route('quest-files-external-update') }}" method="post" class="flex right center">
                    @csrf
                    <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                    <x-shipyard.ui.button
                        action="submit"
                        pop="Zmień status plików w chmurze"
                        :icon="$quest->has_files_on_external_drive ? 'cloud-arrow-down' : 'cloud-arrow-up'"
                        name="external"
                        :value="$quest->has_files_on_external_drive ? 0 : 1"
                        class="primary"
                    />
                </form>
                @endif

                @unless(Auth::id() === 0)
                <form action="{{ route('quest-files-ready-update') }}" method="post" class="flex right center">
                    @csrf
                    <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                    <x-shipyard.ui.button
                        pop="Przełącz komplet"
                        :icon="$quest->files_ready ? 'tray-alert' : 'tray-full'"
                        action="submit"
                        name="ready"
                        :value="(int) !$quest->files_ready"
                        class="primary"
                    />
                </form>
                <x-shipyard.ui.button
                    icon="plus"
                    pop="Wgraj"
                    :action="route('files-upload-by-entity', ['entity_name' => 'quest', 'id' => $quest->id])"
                    target="_blank"
                    onclick="primeReloadFileList(document.querySelector(`.files-container`));"
                />
                <x-shipyard.ui.button
                    icon="recycle"
                    pop="Dodaj istniejące"
                    :action="route('files-add-from-existing-safe', ['song_id' => $quest->song_id])"
                    target="_blank"
                    onclick="primeReloadFileList(document.querySelector(`.files-container`));"
                />
                @endunless
            </x-slot:buttons>

            <x-extendo-section>
                <div class="flex right center middle">
                    @if (can_download_files($quest->client_id, $quest->id))
                    <span class="accent success">
                        <x-shipyard.app.icon name="download" />
                        Klient może pobierać
                    </span>

                    @elseif ($quest->user->notes->can_see_files)
                    <span class="accent danger">
                        <x-shipyard.app.icon name="eye" />
                        Klient widzi podglądy
                    </span>


                    @else
                    <span class="accent error">
                        <x-shipyard.app.icon name="eye-remove" />
                        Klient nic nie widzi
                    </span>

                    @endif

                    @if ($quest->files_ready)
                    <span class="accent success">
                        <x-shipyard.app.icon name="tray-full" />
                        Pliki w komplecie
                    </span>
                    @else
                    <span class="accent danger">
                        <x-shipyard.app.icon name="tray-alert" />
                        Brak kompletu
                    </span>
                    @endif

                    @if ($quest->user->notes->external_drive)
                    <span @class(["accent success" => $quest->has_files_on_external_drive])>
                        <x-shipyard.app.icon :name="model_field_icon('user_notes', 'external_drive')" />
                        @if ($quest->has_files_on_external_drive)
                        Posiada pliki
                        @else
                        Brak plików
                        @endif
                    </span>
                    @endif
                </div>
            </x-extendo-section>

            <x-files.list :song-id="$quest->song_id" :editable="true" :highlight-for-client-id="$quest->client_id" :can-download-files="true" />
        </x-extendo-block>

        <x-song-work-time-log :quest="$quest" :extended="true" />
    </div>

    <x-extendo-block key="client"
        :header-icon="model_icon('users')"
        title="Klient"
        :subtitle="$quest->user->name_and_badges"
    >
        <div class="grid but-halfsize-down" style="--col-count: 2;">
            @foreach ([
                "client_name",
                "contact_preference",
                "password",
            ] as $field_name)
                <x-shipyard.ui.field-input :model="$quest->user->notes" :field-name="$field_name" dummy />
            @endforeach

            <x-shipyard.ui.input type="dummy-text"
                name="pickiness"
                label="Wybredność"
                icon="fencing"
                :value="$quest->user->notes->pickiness"
            />
        </div>

        <x-slot:buttons>
            <x-button :action="route('client-view', ['id' => $quest->client_id])" :icon="model_icon('users')" pop="Szczegóły" />
            <x-button :action="route('quests', ['client' => $quest->client_id])" :icon="model_icon('quests')" pop="Zlecenia" />
        </x-slot:buttons>
    </x-extendo-block>

    @unless (in_array($quest->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
    <x-quest-history :quest="$quest" :extended="true" />
    @endunless
</div>

@endsection
