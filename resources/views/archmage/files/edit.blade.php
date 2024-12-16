@extends('layouts.app', ["title" => "Edycja plików"])

@section("content")

@if ($existing_files->count())
<x-section title="Już wgrane pliki" icon="folder-open">
    <table>
        <thead>
            <tr>
                <th>Wariant</th>
                <th>Wersja</th>
                <th>Transpozycja</th>
                <th>Widoczność</th>
                <th>Tagi</th>
                <th>Formaty</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($existing_files as $efile)
            <tr class="{{ $file?->id == $efile->id ? 'accent' : '' }}">
                <td>{{ $efile->variant_name }}</td>
                <td>{{ $efile->version_name }}</td>
                <td>{{ $efile->transposition }}</td>
                <td>
                    @if ($efile->only_for_client_id)
                    {{ $efile->exclusiveClient->client_name }}
                    @else
                    <span class="grayed-out">wszyscy</span>
                    @endif
                </td>
                <td>
                    @foreach ($efile->tags as $tag)
                    <x-file-tag :tag="$tag" />
                    @endforeach
                </td>
                <td>{{ implode(", ", array_keys($efile->file_paths)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-section>
@endif

<form action="{{ route('files-process') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ $file?->id }}" />
    <input type="hidden" name="song_id" value="{{ $file?->song_id ?? $song?->id }}" />

    <x-section title="Dane wersji" icon="note-sticky">
        <div class="flex-right center">
            <x-input type="text"
                name="variant_name" label="Nazwa wariantu"
                :value="$file?->variant_name"
                placeholder="podstawowy"
            />
            <x-input type="text"
                name="version_name" label="Nazwa wersji"
                :value="$file?->version_name"
                placeholder="wersja główna"
                small
            />
            <x-input type="number"
                name="transposition" label="Transpozycja"
                :value="$file?->transposition ?? 0"
                small
            />
            <x-select
                name="only_for_client_id" label="Tylko dla klienta"
                :value="$file?->only_for_client_id"
                :options="$clients"
                :empty-option="true"
                small
            />
            <x-input type="TEXT"
                name="description" label="Opis"
                :value="$file?->description"
            />
        </div>
    </x-section>

    <div class="grid-2">
        <x-section title="Tagi" icon="tag">
            <x-slot name="buttons">
                <x-a :href="route('file-tag-edit')" target="_blank" icon="plus">Dodaj nowy</x-a>
            </x-slot>

            <div class="flex-right center wrap">
            @forelse ($tags as $tag)
            <div>
                <x-input type="checkbox" name="tags[{{ $tag->id }}]" :label="$tag->name" :value="$file?->tags->contains($tag->id)" />
                <x-file-tag :tag="$tag" />
            </div>
            @empty
            <span class="grayed-out">Brak utworzonych tagów</span>
            @endforelse
            </div>
        </x-section>

        <x-section title="Pliki" icon="file">
            @if ($file)
            <h2>Usuń istniejące pliki</h2>
            <div class="flex-right center">
                @foreach ($file?->file_paths as $extension => $path)
                <x-input type="checkbox" name="delete_files[{{ $extension }}]" :label="$extension" />
                @endforeach
            </div>
            @endif

            <x-input type="file" name="files[]" label="Wgraj nowe pliki" multiple style="display: initial" />
        </x-section>
    </div>

    <div>
        <x-button action="submit" name="action" value="save" :label="$file ? 'Popraw' : 'Wgraj'" icon="check" />
        @if ($file)
        <x-button action="submit" name="action" value="delete" label="Usuń" icon="trash" />
        @endif
    </div>
</form>

<script defer>
$("#only_for_client_id").select2({ allowClear: true, placeholder: "bez ograniczeń" });
</script>

@endsection
