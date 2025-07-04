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
                <td onclick="copyFileField('variant_name', '{{ $efile->variant_name }}')">{{ $efile->variant_name }}</td>
                <td onclick="copyFileField('version_name', '{{ $efile->version_name }}')">
                    {{ $efile->version_name }}
                    @if ($efile->description) <i class="fas fa-note-sticky" {{ Popper::pop(Illuminate\Mail\Markdown::parse($efile->description)) }}></i> @endif
                </td>
                <td onclick="copyFileField('transposition', {{ $efile->transposition }})">{{ $efile->transposition }}</td>
                <td>
                    @if ($efile->exclusiveClients->count())
                    {{ $efile->exclusiveClients->pluck("client_name")->join(", ") }}
                    @else
                    <span class="grayed-out">nikt</span>
                    @endif
                </td>
                <td>
                    @foreach ($efile->tags as $tag)
                    <x-file-tag :tag="$tag" />
                    @endforeach
                </td>
                <td>
                    @foreach ($efile->file_paths as $ext => $path)
                    <span {{ Popper::pop($path) }}>{{ $ext }}</span>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
    const copyFileField = (field, value) => {
        document.getElementById(field).value = value
    }
    </script>
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
                name="only_for_client_id[]" label="Upoważnieni"
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
        <x-a :href="route('files-upload-by-entity', ['entity_name' => 'file', 'id' => $file?->id])">Wgraj kolejny</x-a>
        @endif
    </div>
</form>

<script defer>
$("[name^=only_for_client_id]").select2({ allowClear: true, multiple: true })
    .val({{ json_encode($file?->exclusiveClients()->pluck("user_id") ?? [$quest?->client_id] ?? []) }})
    .trigger("change");
</script>

@endsection
