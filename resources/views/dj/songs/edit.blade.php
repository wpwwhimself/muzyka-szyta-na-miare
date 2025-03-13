@extends("layouts.app", ["title" => implode(" | ", array_filter([$song?->full_title, "Edycja utworu"]))])

@section("content")

<section>
    <form action="{{ route('dj-process-song') }}" method="POST">
        @csrf

        <input type="hidden" name="id" value="{{ $song?->id ?? App\Models\DjSong::nextId() }}" />

        <div class="flex-right center">
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
            <x-input name="songmap" label="Mapa utworu"
                type="text" :value="$song?->songmap"
                small
            />
            <x-input name="has_project_file" label="Plik istnieje"
                type="checkbox" :value="$song?->has_project_file"
                small
            />
        </div>

        <div class="grid-3">
            @foreach ([
                ["lyrics", "Tekst"],
                ["chords", "Akordy"],
                ["notes", "Notatki"],
            ] as [$name, $label])
            <x-input :name="$name" :label="$label"
                type="TEXT" :value="$song?->jsonForEdit($name)"
                class="wide"
            />
            @endforeach
        </div>

        <div>
            <x-button :action="route('dj-list-songs')" label="Wróć" icon="angles-left" small />
            <x-button action="submit" name="action" value="save" icon="check" label="Zapisz" />
            @if ($song)
            <x-button :action="route('dj-gig-mode', ['song' => $song->id])" label="Podgląd" icon="microphone" small />
            <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" danger />
            @endif
        </div>
    </form>
</section>

@endsection
