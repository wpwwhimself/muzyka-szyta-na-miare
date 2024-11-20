@extends('layouts.app')

@section('content')
<form action="{{ route("song-process") }}" method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ $song->id }}">

    <x-section title="Dane utworu" icon="compact-disc">
        <div class="flex-right center">
            @foreach ([
                ["text", "title", "Tytuł"],
                ["text", "artist", "Wykonawca"],
                ["text", "link", "Link"],
                ["TEXT", "notes", "Notatki"],
                ["text", "price_code", "Kod wyceny"],
            ] as [$type, $name, $label])
            <x-input :type="$type"
                :name="$name"
                :label="$label"
                :value="$song->{$name}"
            />
            @endforeach
            
            <x-select
                name="genre_id" label="Gatunek"
                :value="$song->genre_id"
                :options="$genres"
            />
        </div>
    </x-section>

    <x-section title="Reklama" icon="bullhorn">
        <x-extendo-section title="Showcase">
            @if($song->has_showcase_file)
            <audio controls><source src="{{ route('showcase-file-show', ['id' => $song->id]) }}?{{ time() }}" type="audio/ogg" /></audio>
            @else
            <span class="grayed-out">Brak showcase'u</span>
            @endif
    
            <form id="showcase-file-form" class="flex-right center" method="post" action="{{ route('showcase-file-upload') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="showcase_file" />
                <input type="hidden" name="id" value="{{ $song->id }}" />
                <x-button action="#/" id="showcase-file-button" :small="true"
                    :icon="$song->has_showcase_file ? 'pencil' : 'plus'"
                    label="{{ $song->has_showcase_file ? 'Podmień' : 'Dodaj' }} plik"
                    />
                <script>
                const button = $("#showcase-file-button");
                const file_input = $("input[name='showcase_file']");
                button.click(() => file_input.trigger("click"));
                file_input.change(() => $("#showcase-file-form").submit());
                </script>
            </form>
        </x-extendo-section>
    </x-section>

    <x-button action="submit" label="Popraw dane" icon="pencil" />
</form>
@endsection
