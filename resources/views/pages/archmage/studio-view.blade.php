@extends("layouts.minimal")
@section("title", $quest->full_title)
@section("subtitle", "Studio")

@section("content")

<div class="grid" style="--col-count: 2;">
    <script src="{{ mix('js/react/workTimeClock.js') }}" defer></script>
    <section id="clock" class="section"></section>

    <div id="meta" class="flex down">
        <x-extendo-block key="song"
            :header-icon="model_icon('songs')"
            title="Utwór"
            :subtitle="$quest->song->id . '_' . $quest->song->title"
            :extended="true"
        >
            <div class="flex right center middle">
                <x-quest-type :type="$quest->quest_type" />
                <span>{{ $quest->song->genre->name }}</span>
                <span><strong>wycena</strong>: {{ $quest->price_code_override }}</span>
            </div>

            <x-shipyard.ui.field-input :model="$quest->song" field-name="link" dummy />
            <x-link-interpreter :raw="$quest->song->link" />

            <x-shipyard.ui.field-input :model="$quest->song" field-name="notes" dummy />
            <x-shipyard.ui.field-input :model="$quest" field-name="wishes" dummy />
        </x-extendo-block>

        <x-quest-history :quest="$quest" />
    </div>
</div>
<x-a :href="route('studio')">Wróć</x-a>

@endsection
