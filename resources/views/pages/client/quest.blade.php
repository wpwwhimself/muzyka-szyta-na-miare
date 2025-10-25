@extends('layouts.app')
@section("title", $quest->song->full_title)
@section("subtitle", "Zlecenie")

@section('content')

@if (sumWarnings($warnings))
<h1 class="accent danger">
    <x-shipyard.app.icon name="alert" />
    Jest kilka rzeczy, z którymi musisz się koniecznie zapoznać!
</h1>
@endif

<x-phase-indicator :status-id="$quest->status_id" />

<div class="grid but-mobile-down" style="--col-count: 2;">
    <x-extendo-block key="meta"
        :header-icon="model_icon('songs')"
        title="Szczegóły utworu"
        :subtitle="$quest->song->full_title"
        :extended="true"
    >
        @php $song = $quest->song; @endphp

        <div class="flex right center middle">
            <x-quest-type :type="$song->type" />
        </div>

        <div class="grid but-mobile-down" style="--col-count: 2;">
            @foreach ([
                "title",
                "artist",
                "link",
                "notes",
            ] as $field_name)
            <div>
                <x-shipyard.ui.field-input :model="$song" :field-name="$field_name" dummy />
                @if ($field_name == "link")
                <x-link-interpreter :raw="$song->$field_name" />
                @endif
            </div>
            @endforeach

            <x-shipyard.ui.field-input :model="$quest" field-name="wishes" dummy />
        </div>
    </x-extendo-block>

    <x-extendo-block key="quote"
        :header-icon="model_icon('prices')"
        title="Wycena"
        :subtitle="implode(' ', array_filter([
            'do zapłaty:',
            as_pln($quest->payment_remaining),
            $quest->delayed_payment_in_effect ? 'po '.$quest->delayed_payment->format('d.m.Y') : null,
            '//',
            'pliki do '.$quest->deadline?->format('d.m.Y'),
        ]))"
        :warning="$warnings['quote']"
        :extended="true"
    >
        <x-slot:buttons>
            @unless ($quest->paid)
            <x-tutorial>
                <p>Opłaty projektu możesz dokonać na 2 sposoby:</p>
                <ul>
                    <li>na numer konta <b>58 1090 1607 0000 0001 5333 1539</b><br>
                        (w tytule wpisz <i>{{ $quest->id }}</i>)</li>
                    <li>BLIKiem na numer telefonu <b>530 268 000</b>.</li>
                </ul>
                <p>
                    Jest ona potrzebna do pobierania plików,<br>
                    chyba, że jesteś np. stałym klientem
                </p>
            </x-tutorial>
            @if ($quest->delayed_payment)
            <x-warning>
                Z uwagi na limity przyjmowanych przeze mnie wpłat,
                <b>proszę o dokonanie wpłaty po {{ $quest->delayed_payment->format('d.m.Y') }}</b>.
                Po zaakceptowaniu zlecenia dostęp do plików
                zostanie przyznany automatycznie.
            </x-warning>
            @endif
            @endunless
        </x-slot:buttons>

        <x-re_quests.price-summary :model="$quest" />
        <div class="grid but-mobile-down" style="--col-count: 2;">
            @foreach ([
                "deadline",
                "hard_deadline",
            ] as $field_name)
                <x-shipyard.ui.field-input :model="$quest" :field-name="$field_name" dummy />
            @endforeach
        </div>
        <x-quests.payments-bar :quest="$quest" />

        <x-quests.invoices :quest="$quest" />
    </x-extendo-block>

    <x-extendo-block key="files"
        :header-icon="model_icon('files')"
        title="Pliki"
        :extended="true"
        scissors
    >
        @if (Auth::user()->notes->can_see_files)
        <x-files.list :grouped-files="$files" :can-download-files="can_download_files(Auth::id(), $quest->id)" />
        @endif

        @if ($quest->status_id == 15 && !$quest->files_ready)
        <p class="yellowed-out">
            To nie są jeszcze wszystkie pliki.<br>
            Dalsze prace po akceptacji tego etapu.
        </p>
        @endif

        @if (empty($files))
        <x-tutorial>
            Tutaj pojawią się pliki związane
            z przygotowywanym dla Ciebie zleceniem.
            Po dokonaniu wpłaty będzie możliwość
            ich pobrania lub odsłuchania.
        </x-tutorial>
            @if (in_array($quest->status_id, [19]))
            <p class="yellowed-out">
                Sejf został usunięty.<br>
                Przywróć zlecenie przyciskiem poniżej<br>
                i poproś o ponowne wgranie
            </p>
            @endif
        @endif

        <x-extendo-section title="Chmura">
        @if ($quest->has_files_on_external_drive)
            <span><i class="fas fa-cloud ghost"></i> W chmurze znajdują się pliki związane z tym zleceniem</span>
            <x-a :href="$quest->client->external_drive">Otwórz</x-a>
        @endif
        </x-extendo-section>
    </x-extendo-block>

    <x-quest-history :quest="$quest" />
</div>

<x-shipyard.app.form :action="route('mod-quest-back')" method="POST" id="phases">
    <x-slot:actions>
        <x-shipyard.ui.button
            icon="chevron-left"
            label="Wróć do listy"
            :action="route('quests')"
        />
    </x-slot:actions>

    <div class="flex right center middle">
        @if (in_array($quest->status_id, [15]))
        <x-tutorial>
            Za pomocą poniższych przycisków możesz przyjąć zlecenie lub,
            jeśli coś Ci się nie podoba w przygotowanych przeze mnie materiałach, poprosić o przygotowanie poprawek.
            Instrukcje do tego celu możesz umieścić w oknie, które pojawi się po wybraniu jednej z poniższych opcji.
            Ta informacja będzie widoczna i na jej podstawie będę mógł wprowadzić poprawki.
        </x-tutorial>

        @elseif ($quest->status_id == 19)
        <x-tutorial>
            Zlecenie zostało przez Ciebie zamknięte, ale nadal możesz je przywrócić w celu wprowadzenia kolejnych zmian.
        </x-tutorial>

            @if ($quest->history->first()?->date->diffInDays() >= 30)
            <p class="accent error" style="text-align: left;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                Ostatnia zmiana padła {{ $quest->history->first()->date->diffForHumans() }}.
                Zażądanie poprawek może wiązać się z dopłatą.
                Zobacz <a href="{{ route('prices') }}">cennik</a> po więcej informacji.
            </p>
            @endif
        @endif

        <input type="hidden" name="quest_id" value="{{ $quest->id }}" />

        @if (in_array($quest->status_id, [11]))
        <x-shipyard.ui.button
            icon="account-alert"
            label="Poproś o zmiany"
            action="none"
            onclick="openModal('quest-change-status', {
                quest_id: '{{ $quest->id }}',
                status_id: 21,
            })"
            class="tertiary"
        />
        @endif

        @if (in_array($quest->status_id, [16, 21, 26, 96]))
        <x-shipyard.ui.button
            icon="comment-edit"
            label="Popraw ostatni komentarz"
            action="none"
            onclick="openModal('quest-change-status', {
                quest_id: '{{ $quest->id }}',
                status_id: {{ $quest->status_id }},
                comment: '{{ $quest->history->first()?->comment }}',
            })"
            class="tertiary"
        />
            @if ($quest->status_id == 21)
            <x-shipyard.ui.button
                icon="comment-remove"
                label="Zrezygnuj ze zmian"
                action="none"
                onclick="openModal('quest-change-status', {
                    quest_id: '{{ $quest->id }}',
                    status_id: 11,
                })"
                class="tertiary"
            />
            @endif
        @endif

        @if (in_array($quest->status_id, [95]))
        <x-shipyard.ui.button
            icon="reply-all"
            label="Odpowiedz"
            action="none"
            onclick="openModal('quest-change-status', {
                quest_id: '{{ $quest->id }}',
                status_id: 96,
            })"
            class="tertiary"
        />
        @endif

        @if (in_array($quest->status_id, [15, 31, 95]))
            @if ($quest->files_ready)
            <x-shipyard.ui.button
                icon="check-all"
                label="Zaakceptuj i zakończ"
                action="none"
                onclick="openModal('quest-change-status', {
                    quest_id: '{{ $quest->id }}',
                    status_id: 19,
                })"
                class="tertiary"
            />
            @else
            <x-shipyard.ui.button
                icon="check"
                label="Zaakceptuj etap"
                action="none"
                onclick="openModal('quest-change-status', {
                    quest_id: '{{ $quest->id }}',
                    status_id: 14,
                })"
                class="tertiary"
            />
            @endif
        @endif

        @if (in_array($quest->status_id, [14, 15]))
        <x-shipyard.ui.button
            icon="chat-alert"
            :label="$quest->files_ready ? 'Poproś o poprawki' : 'Poproś o poprawki w tym etapie'"
            action="none"
            onclick="openModal('quest-change-status', {
                quest_id: '{{ $quest->id }}',
                status_id: 16,
            })"
            class="tertiary"
        />
        @endif

        @if (in_array($quest->status_id, [18, 19]))
            @if ($quest->completed_once)
            <x-shipyard.ui.button
                icon="check-all"
                label="Zrezygnuj z dalszych zmian"
                action="none"
                onclick="openModal('quest-change-status', {
                    quest_id: '{{ $quest->id }}',
                    status_id: 19,
                })"
                class="tertiary"
            />
            @else
            <x-shipyard.ui.button
                icon="package-variant-closed-remove"
                label="Zrezygnuj ze zlecenia"
                action="none"
                onclick="openModal('quest-change-status', {
                    quest_id: '{{ $quest->id }}',
                    status_id: 18,
                })"
                class="tertiary"
            />
            @endif

        <x-shipyard.ui.button
            icon="recycle"
            label="Przywróć zlecenie"
            action="none"
            onclick="openModal('quest-change-status', {
                quest_id: '{{ $quest->id }}',
                status_id: 26,
            })"
            class="tertiary"
        />
        @endif
    </div>
</x-shipyard.app.form>

@endsection
