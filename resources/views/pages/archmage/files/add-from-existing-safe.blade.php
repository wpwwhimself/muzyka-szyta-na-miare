@extends("layouts.app", ["title" => "Recykling Sejfu"])

@section("content")

<form action="{{ route('files-process-add-from-existing-safe') }}" method="POST">
    @csrf
    <input type="hidden" name="song_id" value="{{ $song->id }}" />

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
                    <th>Popraw</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($existing_files as $efile)
                <tr>
                    <td onclick="copyFileField('variant_name', '{{ $efile->variant_name }}')">{{ $efile->variant_name }}</td>
                    <td onclick="copyFileField('version_name', '{{ $efile->version_name }}')">{{ $efile->version_name }}</td>
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
                    <td>
                        <input type="radio" name="existing_file_id" value="{{ $efile->id }}" />
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

    <x-section title="Wybierz pliki" icon="file">
        <div class="flex right center">
            @foreach ($files as $file)
            @if (pathinfo($file, PATHINFO_EXTENSION) == "md")
            <div>
                <strong>{{ basename($file) }}:</strong>
                <pre>{{ Storage::get($file) }}</pre>
            </div>
            @else
            <x-input type="checkbox" name="file_to_recycle[{{ preg_replace('/(\[|\])/', '$', $file) }}]" :label="basename($file)" />
            @endif
            @endforeach
        </div>
    </x-section>

    <div class="grid" style="--col-count: 2;">
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
            name="only_for_client_id[]" label="Upoważnieni"
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

            <div class="flex right center wrap">
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
$("[name^=only_for_client_id]").select2({ allowClear: true, multiple: true });
</script>

@endsection
