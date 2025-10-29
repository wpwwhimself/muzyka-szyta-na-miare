@extends('layouts.app')
@section("title", "Lista utworów")

@section('content')

<div class="flex right">
    <x-a :href="route('file-size-report')" icon="weight-hanging">Raport wielkości sejfów</x-a>
</div>

<x-section id="songs-list" class="flex down"
    title="Lista utworów"
    :icon="model_icon('songs')"
>
    <x-slot:buttons>
        <x-a :href="route('song-genres')" :icon="model_icon('genres')">Gatunki</x-a>
        <x-a :href="route('song-tags')" :icon="model_icon('song-tags')">Tagi utworów</x-a>
        <x-a :href="route('file-tags')" :icon="model_icon('file-tags')">Tagi plików</x-a>
        <form method="get" id="search" class="flex right middle nowrap" action="{{ route('songs') }}">
            <x-shipyard.ui.input type="text" name="search" label="Szukaj" :value="$search" />
            <x-button action="submit" icon="search" label="" :small="true" />
        </form>
    </x-slot:buttons>

    <div class="flex down">
        @forelse ($songs as $song)
        <x-extendo-block :key="$song->id"
            :header-icon="preg_replace('/fa-/', '', $song->type->fa_symbol)"
            :title="$song->title ?? 'bez tytułu'"
            :subtitle="$song->artist"
        >
            <x-extendo-section title="ID">{{ $song->id }}</x-extendo-section>
            <x-extendo-section title="Typ">{{ $song->type->type }}</x-extendo-section>
            <x-extendo-section title="Gatunek">{{ $song->genre?->name }}</x-extendo-section>
            <x-extendo-section title="Kod wyceny">{!! $price_codes[$song->id] !!}</x-extendo-section>
            <x-extendo-section title="Linki"><x-link-interpreter :raw="$song->link" :editable="$song->id" /></x-extendo-section>
            <x-extendo-section title="Notatki">{{ $song->notes ? Illuminate\Mail\Markdown::parse($song->notes) : "" }}</x-extendo-section>
            <x-extendo-section title="Czas wykonania">
                <span {{ Popper::pop($song_work_times[$song->id]['parts']) }}>
                    {{ $song->work_time_total }}
                </span>
            </x-extendo-section>
            <x-extendo-section title="Zlecenia">
                @foreach ($song->quests as $quest)
                    <a href="{{ $quest->linkTo }}">{{ $quest->id }}</a>
                @endforeach
            </x-extendo-section>
            <x-extendo-section title="Koszty">
                <table>
                    <thead>
                        <tr>
                            <th>Kategoria</th>
                            <th>Kwota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($song->costs as $cost)
                        <tr>
                            <td>{{ $cost->type->name }}</td>
                            <td>{{ _c_(as_pln($cost->amount)) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan=2><span class="grayed-out">Brak kosztów</span></td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Suma:</th>
                            <th>{{ _c_(as_pln($song->costs?->sum("amount"))) }}</th>
                        </tr>
                    </tfoot>
                </table>
                <x-button action="{{ route('costs') }}" name="" icon="money-bill-wave" label="Koszty" :small="true" />
            </x-extendo-section>
            <x-extendo-section title="Akcje">
                <x-a :href="route('song-edit', ['id' => $song->id])">Edytuj</x-a>
            </x-extendo-section>
        </x-extendo-block>
        @empty
        <p class="grayed-out">Nie ma żadnych utworów</p>
        @endforelse
    </div>

    {{ $songs->links("components.shipyard.pagination.default") }}
</x-section>

@endsection
