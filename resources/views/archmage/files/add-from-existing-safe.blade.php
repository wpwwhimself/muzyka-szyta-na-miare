@extends("layouts.app", ["title" => "Recykling Sejfu"])

@section("content")

<form action="{{ route('files-process-add-from-existing-safe') }}" method="POST">
    @csrf
    <input type="hidden" name="song_id" value="{{ $song->id }}" />

    <x-section title="Wybierz pliki" icon="file">
        <div class="flex-right center">
            @foreach ($files as $file)
            <x-input type="checkbox" name="file_to_recycle[{{ htmlspecialchars($file) }}]" :label="basename($file)" />
            @endforeach
        </div>
    </x-section>

    <div class="grid-2">
        <x-section title="Opisz wersję" icon="note-sticky">
            <x-input type="text"
                name="variant_name" label="Nazwa wariantu"
                placeholder="podstawowy"
            />
            <x-input type="text"
                name="version_name" label="Nazwa wersji"
                placeholder="wersja główna"
                small
            />
            <x-input type="number"
                name="transposition" label="Transpozycja"
                :value="0"
                small
            />
            <x-select
                name="only_for_client_id" label="Tylko dla klienta"
                :options="$clients"
                :empty-option="true"
                small
            />
            <x-input type="TEXT"
                name="description" label="Opis"
            />
        </x-section>

        <x-section title="Tagi" icon="tag">
            <x-slot name="buttons">
                <x-a :href="route('file-tag-edit')" target="_blank" icon="plus">Dodaj nowy</x-a>
            </x-slot>

            <div class="flex-right center wrap">
            @forelse ($tags as $tag)
            <div>
                <x-input type="checkbox" name="tags[{{ $tag->id }}]" :label="$tag->name" />
                <x-file-tag :tag="$tag" />
            </div>
            @empty
            <span class="grayed-out">Brak utworzonych tagów</span>
            @endforelse
            </div>
        </x-section>
    </div>

    <div>
        <x-button action="submit" name="action" value="save" label="Zapisz" icon="check" />
    </div>
</form>

<script defer>
$("#only_for_client_id").select2({ allowClear: true, placeholder: "bez ograniczeń" });
</script>

@endsection
