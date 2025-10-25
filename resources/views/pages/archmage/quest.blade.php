@extends('layouts.app')
@section("title", $quest->song->full_title)
@section("subtitle", "Zlecenie")

@section('content')

<x-shipyard.app.form method="POST" :action="route('mod-quest-back')">
    <x-phase-indicator :status-id="$quest->status_id" />

    <div class="archmage-quest-phases flex right center middle">
        <x-input type="TEXT" name="comment" label="Komentarz do zmiany statusu" />
        <input type="hidden" name="quest_id" value="{{ $quest->id }}" />

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
            <x-shipyard.ui.button action="submit"
                name="status_id"
                :icon="$new_status->icon"
                :value="$status_id"
                :pop="$label"
                :class="$nomail ? 'warning' : ''"
            />
            @endif
        @endforeach
    </div>
</x-shipyard.app.form>

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-extendo-block key="quest"
        :header-icon="model_icon('songs')"
        title="Utwór"
        :subtitle="$quest->song_id . ' // ' . $quest->song->full_title"
        :extended="true"
    >
        @php $song = $quest->song; @endphp

        <div class="flex right center middle">
            <x-quest-type :type="$song->type" />
            <x-a :href="route('songs', ['search' => $quest->song_id])">Szczegóły</x-a>
            <x-a :href="route('song-edit', ['id' => $quest->song_id])">Edytuj</x-a>
        </div>

        <div class="grid but-halfsize-down" style="--col-count: 2;">
            <x-shipyard.ui.connection-input :model="$song" connection-name="genre" />

            @foreach ([
                "title",
                "artist",
                "link",
                "has_recorded_reel",
                "has_original_mv",
                "notes",
            ] as $field_name)
            <div>
                <x-shipyard.ui.field-input :model="$song" :field-name="$field_name" />
                @if ($field_name == "link")
                <x-link-interpreter :raw="$song->$field_name" />
                @endif
            </div>
            @endforeach

            <x-shipyard.ui.field-input :model="$quest" field-name="wishes" />
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
                    questId: '{{ $quest->id }}',
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

    @if($quest->status_id == 12)
    <x-song-work-time-log :quest="$quest" :extended="true" />
    <x-quest-history :quest="$quest" :extended="true" />

    @elseif (in_array($quest->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
    <x-quest-history :quest="$quest" :extended="true" />

    @endif

    <x-extendo-block key="files"
        :header-icon="model_icon('files')"
        title="Pliki"
        :extended="!in_array($quest->status_id, [11])"
        :warning="$warnings['files']"
        scissors
    >
        <x-slot:buttons>
            @unless(Auth::id() === 0)
            <x-shipyard.ui.button
                icon="plus"
                pop="Wgraj"
                :action="route('files-upload-by-entity', ['entity_name' => 'quest', 'id' => $quest->id])"
                target="_blank"
                onclick="primeReload();"
                class="primary"
            />
            <x-shipyard.ui.button
                icon="recycle"
                pop="Dodaj istniejące"
                :action="route('files-add-from-existing-safe', ['song_id' => $quest->song_id])"
                target="_blank"
                onclick="primeReload();"
                class="primary"
            />
            @endunless
        </x-slot:buttons>

        <x-extendo-section>
            @if ($quest->user->notes->external_drive)
            <x-shipyard.ui.button
                :action="$quest->user->notes->external_drive"
                :icon="model_field_icon('user_notes', 'external_drive')"
                label="Link"
                target="_blank"
            />
            <form action="{{ route('quest-files-external-update') }}" method="post" class="flex right center">
                @csrf
                <input type="hidden" name="quest_id" value="{{ $quest->id }}" />

                @if ($quest->has_files_on_external_drive)
                <span><i class="fas fa-cloud success"></i> Posiada pliki</span>
                <x-button action="submit" label="Zmień" icon="cloud-arrow-down" name="external" value="0" :small="true" />
                @else
                <span><i class="fas fa-cloud-bolt error"></i> Brak plików</span>
                <x-button action="submit" label="Zmień" icon="cloud-arrow-up" name="external" value="1" :small="true" />
                @endif
            </form>
            @endif

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

                <form action="{{ route('quest-files-ready-update') }}" method="post" class="flex right center">
                    @csrf
                    <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
                    <x-button action="submit" :label="!$quest->files_ready ? 'Wszystko wgrane' : 'Jednak nie'"
                        icon="file-check" name="ready" value="{{ !$quest->files_ready }}" :small="true" />
                </form>
            </div>
        </x-extendo-section>

        <x-files.list :grouped-files="$files" :editable="true" :highlight-for-client-id="$quest->client_id" :can-download-files="true" />
    </x-extendo-block>

    <x-extendo-block key="client"
        :header-icon="model_icon('users')"
        title="Klient"
        :subtitle="$quest->user"
    >
        @foreach ([
            "client_name",
            // "contact_preference",
            "password",
            // "pickiness",
        ] as $field_name)
            <x-shipyard.ui.field-input :model="$quest->user->notes" :field-name="$field_name" dummy />
        @endforeach

        <x-shipyard.ui.input type="dummy-text"
            name="pickiness"
            label="Wybredność"
            icon="fencing"
            :value="$quest->user->notes->pickiness"
        />

        <x-slot:buttons>
            <x-button :action="route('clients', ['search' => $quest->client_id])" :icon="model_icon('users')" pop="Szczegóły" />
            <x-button :action="route('quests', ['client' => $quest->client_id])" :icon="model_icon('quests')" pop="Zlecenia" />
        </x-slot:buttons>
    </x-extendo-block>

    @unless (in_array($quest->status_id, STATUSES_WITH_ELEVATED_HISTORY()))
    <x-quest-history :quest="$quest" :extended="true" />
    @endunless
</div>

@endsection
