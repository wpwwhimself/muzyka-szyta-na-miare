@extends("layouts.app")

@section("content")

<div class="input-container">
<div class="grid-2">
    <script src="{{ mix('js/workTimeClock.js') }}" defer></script>
    <section id="clock"></section>

    <div id="meta" class="flex-down spaced">
        <x-extendo-block key="song"
            header-icon="compact-disc"
            title="Utwór"
            :subtitle="$quest->song->id . '_' . $quest->song->title"
            :extended="true"
        >
            <x-extendo-section title="ID">
                {{ $quest->song->id }}
            </x-extendo-section>

            <x-extendo-section title="Rodzaj">
                <x-quest-type
                    :id="$quest->song->type->id"
                    :label="$quest->song->type->type"
                    :fa-symbol="$quest->song->type->fa_symbol"
                />
            </x-extendo-section>

            <div>
                <x-input type="text" name="link" label="Linki" value="{{ $quest->song->link }}" :small="true" />
                <x-link-interpreter :raw="$quest->song->link" />
            </div>

            <x-input type="TEXT" name="notes" label="Życzenia dotyczące utworu" value="{{ $quest->song->notes }}" />

            <x-input type="TEXT" name="wishes" label="Życzenia dotyczące zlecenia" value="{{ $quest->wishes }}" />
        </x-extendo-block>

        <x-quest-history :quest="$quest" :extended="true" />
    </div>
</div>
<x-a :href="route('studio')">Wróć</x-a>
</div>

@endsection
