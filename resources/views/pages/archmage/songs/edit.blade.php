@extends('layouts.app')
@section("title", $song->full_title)
@section("subtitle", "Edycja utworu")

@section('content')
<x-shipyard.app.form :action="route('song-process')" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="{{ $song->id }}">

    <div class="grid but-mobile-down" style="--col-count: 2;">
        <x-section title="Dane utworu" :icon="model_icon('songs')">
            <div class="grid but-halfsize-down" style="--col-count: 2;">
                <x-shipyard.ui.input type="dummy-text" name="id" label="ID" icon="barcode" :value="$song->id" />

                @foreach ([
                    "title",
                    "artist",
                    "link",
                    "notes",
                    "price_code",
                ] as $field_name)
                <div>
                    <x-shipyard.ui.field-input :model="$song" :field-name="$field_name" />

                    @if ($field_name == "link")
                    <x-link-interpreter :raw="$song->$field_name" />
                    @endif
                </div>
                @endforeach

                <x-shipyard.ui.connection-input :model="$song" connection-name="genre" />
            </div>
        </x-section>

        <x-section title="Reklama" :icon="model_icon('showcases')">
            <x-shipyard.app.h lvl="4" :icon="model_icon('showcases')">Showcase</x-shipyard.app.h>
            @if($song->has_showcase_file)
            <audio controls><source src="{{ route('showcase-file-show', ['id' => $song->id]) }}?{{ time() }}" type="audio/ogg" /></audio>
            @else
            <span class="grayed-out">Brak showcase'u</span>
            @endif

            <script>
            function setFormToFileUpload() {
                const form = document.querySelector('form');
                form.action = `{{ route("showcase-file-upload") }}`;
                form.submit();
            }
            </script>
            <x-shipyard.ui.input type="file" name="showcase_file" label="Nowy plik showcase'u" icon="file-music"
                onchange="setFormToFileUpload();"
            />

            <x-shipyard.app.h lvl="4" :icon="model_icon('song-tags')">Tagi</x-shipyard.app.h>
            <div class="flex right center wrap">
                @foreach ($tags as $tag)
                <x-shipyard.ui.input type="checkbox"
                    name="tags[{{ $tag->id }}]"
                    :label="$tag->name"
                    icon="tag"
                    value="1"
                    :checked="in_array($tag->id, $song->tags->pluck('id')->toArray())"
                />
                @endforeach
            </div>

            <x-shipyard.app.h lvl="4" :icon="model_icon('showcases')">Rolka</x-shipyard.app.h>
            <x-shipyard.ui.input type="select"
                name="reel_platform"
                label="Platforma"
                :icon="model_icon('showcase-platforms')"
                :select-data="[
                    'options' => $showcase_platforms,
                ]"
                :value="$showcase?->platform ?? $platform_suggestion['code']"
            />
            <x-shipyard.ui.input type="url"
                name="reel_link"
                label="Link"
                icon="link"
                :value="$showcase?->link"
            />
        </x-section>
    </div>

    <x-button action="submit" label="Popraw dane" icon="pencil" />
</x-shipyard.app.form>

<x-extendo-block key="reel_desc" title="Opis" header-icon="text">
    <x-showcases.description for="podklady" :songdata="$song" />
</x-extendo-block>

@endsection
