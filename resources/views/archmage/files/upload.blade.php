@extends('layouts.app', ["title" => "Wgraj pliki"])

@section("content")

<form action="{{ route('files-upload-process') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="quest_id" value="{{ $quest->id }}" />
    <input type="hidden" name="song_id" value="{{ $quest->song_id }}" />

    <x-section title="Dane wersji" icon="note-sticky">
        <div class="flex-right center">
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
                small
                value="0"
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
        </div>
    </x-section>

    <div class="grid-2">
        <x-section title="Tagi" icon="tag">
            <div class="flex-right center wrap">
            @foreach ($tags as $tag)
            <x-input type="checkbox" name="tags[{{ $tag->id }}]" :label="$tag->name" />
            @endforeach
            </div>
        </x-section>

        <x-section title="Pliki" icon="file">
            <x-input type="file" name="files[]" label="Pliki" multiple style="display: initial" />
        </x-section>
    </div>

    <div class="flex-right center">
        <x-button action="submit" name="action" value="save" label="Wgraj" icon="upload" />
    </div>
</form>

<script defer>
$("#only_for_client_id").select2({ allowClear: true, placeholder: "bez ograniczeń" });
</script>

@endsection