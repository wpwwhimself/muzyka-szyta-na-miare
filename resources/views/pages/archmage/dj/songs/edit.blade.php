@extends("layouts.app", ["title" => implode(" | ", array_filter([$song?->full_title, "Edycja utworu"]))])

@section("content")

<form action="{{ route('dj-process-song') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ $song?->id ?? App\Models\DjSong::nextId() }}" />

    <x-section title="Meta" icon="compact-disc">
        <div class="flex-right center black-back">
            <x-input name="id" label="ID"
                type="text" :value="$song?->id"
                small disabled
            />

            @foreach ([
                ["title", "Tytuł"],
                ["artist", "Artysta"],
            ] as [$name, $label])
            <x-input :name="$name" :label="$label"
                type="text" :value="$song?->{$name}"
                required
            />
            @endforeach
        </div>

        <div class="flex-right center">
            <x-input name="key" label="Tonacja wyjściowa"
                type="text" :value="$song?->key"
                small
            />
            <x-select name="tempo" label="Tempo"
                :options="$tempos" :value="$song?->tempo"
                small
            />
            <x-select name="genre_id" label="Gatunek"
                :options="$genres" :value="$song?->genre_id"
                empty-option
                small
            />
            <x-input name="changes_description" label="Opis aranżu"
                type="TEXT" :value="$song?->changes_description"
            />
        </div>
    </x-section>

    <x-section title="Pomoce wykonawcze" icon="masks-theater">
        <div class="flex-right center">
            <x-input name="songmap" label="Mapa utworu"
                type="text" :value="$song?->songmap"
                small
            />
            <x-select name="dj_sample_set_id" label="Sample"
                :value="$song?->dj_sample_set_id"
                :options="$potential_sample_sets" empty-option
                small
            />
        </div>

        <div class="grid-3" style="grid-template-columns: repeat(4, 1fr);" role="song-helpers">
            @foreach ([
                ["lyrics", "Tekst"],
                ["chords", "Akordy"],
                ["samples", "Sample"],
                ["extra_notes", "Notatki"],
            ] as [$name, $label])
            <x-input :name="$name" :label="$label"
                type="TEXT" :value="$song?->jsonForEdit($name)"
                class="wide"
            />
            @endforeach
        </div>
    </x-section>

    @if ($song)
    <x-section title="Reklama" icon="bullhorn">
        <div class="flex-right center">
            <x-extendo-section title="Showcase">
                @if($song->has_showcase_file)
                <audio controls><source src="{{ route('showcase-file-show', ['id' => $song->id]) }}?{{ time() }}" type="audio/ogg" /></audio>
                @else
                <span class="grayed-out">Brak showcase'u</span>
                @endif

                <input type="file" name="showcase_file" />
                <x-button action="#/" id="showcase-file-button" :small="true"
                    :icon="$song->has_showcase_file ? 'pencil' : 'plus'"
                    label="{{ $song->has_showcase_file ? 'Podmień' : 'Dodaj' }} plik"
                    />
                <script>
                const button = $("#showcase-file-button");
                const file_input = $("input[name='showcase_file']");
                button.click(() => file_input.trigger("click"));
                file_input.change(function() {$(this).closest("form").attr("action", "{{ route('showcase-file-upload') }}").submit()})
                </script>
            </x-extendo-section>

            <x-extendo-section title="Rolka">
                <x-select name="reel_platform" label="Platforma" :options="$showcase_platforms" :value="$showcase?->platform ?? $platform_suggestion" />
                {{-- <x-input type="url" name="reel_link" label="Link" :value="$showcase?->link" small /> --}}
            </x-extendo-section>
        </div>
    </x-section>
    @endif

    <div>
        <x-button :action="route('dj-list-songs')" label="Wróć" icon="angles-left" small />
        <x-button action="submit" name="action" value="save" icon="check" label="Zapisz" />
        @if ($song)
        <x-button :action="route('dj-gig-mode', ['song' => $song->id])" label="Podgląd" icon="microphone" small />
        <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" danger />
        @endif
    </div>
</form>

<style>
[role="song-helpers"] textarea {
    height: 30em;
}
</style>

@endsection
