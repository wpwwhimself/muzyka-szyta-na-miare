@extends("layouts.app", ["title" => implode(" | ", array_filter([$set?->name, "Edycja zestawu"]))])

@section("content")

<section>
    <form action="{{ route('dj-process-set') }}" method="POST">
        @csrf

        <input type="hidden" name="id" value="{{ $set?->id }}" />

        <div class="flex-right center">
            <x-input name="name" label="Nazwa"
                type="text" :value="$set?->name"
            />
            <x-input name="description" label="Opis"
                type="TEXT" :value="$set?->description"
            />
        </div>

        @if ($set)
        <x-select name="song" label="Dodaj utwór" :options="$songs" :empty-option="true" :small="true" />

        <div id="song_list" class="flex-down center"></div>

        <script defer>
        const songs = {!! json_encode($set->songs) !!}

        function addSong(long_title) {
            const song_id = long_title.substring(0, 4)
            $("#song_list").append(`
                <span onclick="event.target.remove()">
                    <input type="hidden" name="songs[]" value="${song_id}" />
                    ${long_title}
                </span>
            `)
            $("#song").val(null).trigger("change")
        }

        songs.forEach((song) => {
            addSong(`${song.id}: ${song.full_title}`)
        })

        $("#song").select2({ allowClear: true, placeholder: "Wybierz..." })
            .on("change", (ev) => {
                if (!ev.target.value) return
                addSong(ev.target.value)
            })
        </script>
        @endif

        <div>
            <x-button :action="route('dj-list-sets')" label="Wróć" icon="angles-left" small />
            <x-button action="submit" name="action" value="save" icon="check" label="Zapisz" />
            @if ($set)
            <x-button :action="route('dj-gig-mode', ['set' => $set->id])" label="Podgląd" icon="microphone" small />
            <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" danger />
            @endif
        </div>
    </form>
</section>

@endsection
