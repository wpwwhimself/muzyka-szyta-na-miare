@extends('layouts.app')
@section("title", "Edycja pliku")

@section("content")

@if ($existing_files->count())
<x-section title="Już wgrane pliki" :icon="model_icon('files')">
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
                <td class="interactive" onclick="copyFileField('variant_name', '{{ $efile->variant_name }}')">{{ $efile->variant_name }}</td>
                <td class="interactive" onclick="copyFileField('version_name', '{{ $efile->version_name }}')">
                    {{ $efile->version_name }}
                    @if ($efile->description) <i class="fas fa-note-sticky" {{ Popper::pop(Illuminate\Mail\Markdown::parse($efile->description)) }}></i> @endif
                </td>
                <td class="interactive" onclick="copyFileField('transposition', {{ $efile->transposition }})">{{ $efile->transposition }}</td>
                <td>
                    @if ($efile->exclusiveClients->count())
                    {{ $efile->exclusiveClients->pluck("notes.client_name")->join(", ") }}
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

<x-shipyard.app.form :action="route('files-process')" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="{{ $file?->id }}" />
    <input type="hidden" name="song_id" value="{{ $file?->song_id ?? $song?->id }}" />

    <x-section title="Dane wersji" :icon="model_icon('files')">
        <div class="grid but-moblie-down" style="--col-count: 3;">
            @foreach ([
                "variant_name",
                "version_name",
                "transposition",
                "description",
            ] as $field_name)
            <x-shipyard.ui.field-input :model="$file ?? new \App\Models\File" :field-name="$field_name" />
            @endforeach

            <x-shipyard.ui.input type="select"
                :select-data="[
                    'optionsFromScope' => [
                        '\App\Models\UserNote',
                        'clients',
                        'option_label',
                        'user_id',
                    ],
                ]"
                name="only_for_client_id[]"
                label="Upoważnieni"
                :icon="model_icon('users')"
                :value="$file?->exclusiveClients->pluck('id')->toArray() ?? [$quest?->client_id] ?? []"
                multiple
                style="grid-column: 2 / span 2;"
            />
        </div>
    </x-section>

    <div class="grid" style="--col-count: 2;">
        <x-section title="Tagi" icon="tag">
            <x-slot name="buttons">
                <x-a :href="route('file-tag-edit')" target="_blank" icon="plus">Dodaj nowy</x-a>
            </x-slot>

            <div class="flex right center wrap">
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
            <div class="flex right center">
                @foreach ($file?->file_paths as $extension => $path)
                <x-input type="checkbox" name="delete_files[{{ $extension }}]" :label="$extension" />
                @endforeach
            </div>
            @endif

            <x-shipyard.ui.input type="file"
                name="files[]"
                label="Wgraj nowe pliki"
                :icon="model_field_icon('files', 'file_paths')"
                multiple
            />
        </x-section>
    </div>

    <x-slot:actions>
        <x-shipyard.ui.button action="submit" name="action" value="save" :label="$file ? 'Popraw' : 'Wgraj'" icon="check" />
        @if ($file)
        <x-shipyard.ui.button action="submit" name="action" value="delete" label="Usuń" icon="trash" />
        <x-a :href="route('files-upload-by-entity', ['entity_name' => 'file', 'id' => $file?->id])">Wgraj kolejny</x-a>
        @endif
    </x-slot:actions>
</x-shipyard.app.form>

@endsection
