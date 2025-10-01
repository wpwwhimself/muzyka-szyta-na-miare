@extends("layouts.app", ["stripped" => true])

@section("content")

<div class="grid-2">
    <script src="{{ mix('js/react/workTimeClock.js') }}" defer></script>
    <section id="clock"></section>

    <div id="meta" class="flex down">
        <x-extendo-block key="song"
            header-icon="compact-disc"
            title="Utwór"
            :subtitle="$quest->song->id . '_' . $quest->song->title"
            :extended="true"
        >
            <x-extendo-section title="Rodzaj">
                <x-quest-type :type="$quest->quest_type" />
                <span>{{ $quest->song->genre->name }}</span>
                <span><strong>wycena</strong>: {{ $quest->price_code_override }}</span>
            </x-extendo-section>

            <div>
                <x-input type="text" name="link" label="Linki" value="{{ $quest->song->link }}" :small="true" />
                <x-link-interpreter :raw="$quest->song->link" />
            </div>

            <x-input type="TEXT" name="notes" label="Życzenia dotyczące utworu" value="{{ $quest->song->notes }}" />

            <x-input type="TEXT" name="wishes" label="Życzenia dotyczące zlecenia" value="{{ $quest->wishes }}" />
        </x-extendo-block>

        <x-quest-history :quest="$quest" />
    </div>
</div>
<x-a :href="route('studio')">Wróć</x-a>

@endsection
